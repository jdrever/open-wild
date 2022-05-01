<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Interfaces\QueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SitesController extends Controller
{
    /**
     * The APIQueryService implementation.
     *
     * @var QueryService
     */
    protected $queryService;

    /**
     * Create a new controller instance.
     *
     * @param  QueryService  $apiQueryService
     * @return void
     */
    public function __construct(QueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $siteName = $request->input('siteName') ?? $request->cookie('siteName') ?? '';

        if (! $request->has('siteName')) {
            return view('site-search',
            [
                'siteName' => $siteName,
                'showResults' => false,
            ]);
        } else {
            return redirect('/sites/'.$siteName);
        }
    }

    /**
     * Display a list of sites in the county.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $siteName
     * @return \Illuminate\View\View
     */
    public function listForDataset(Request $request, string $siteName, bool $refresh=false)
    {
        Cookie::queue('siteName', $siteName);

        $currentPage = $this->getCurrentPage($request);

        $speciesNameType = $request->cookie('speciesNameType') ?? 'scientific';
        $speciesGroup = $request->cookie('speciesGroup') ?? 'plants';
        $axiophyteFilter = $request->cookie('axiophyteFilter') ?? 'false';

        $results = $this->queryService->getSiteListForDataset($siteName, $currentPage);

        $viewName=$refresh ? "data-tables/sites-in-dataset" : "site-search";

        return view($viewName,
        [
            'siteName' => $siteName,
            'speciesNameType' => $speciesNameType,
            'speciesGroup' => $speciesGroup,
            'axiophyteFilter' => $axiophyteFilter,
            'showResults' => true,
            'results' =>$results,
        ]);
    }
}
