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
     * Display a list of sites in the county
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $siteName
     * @return \Illuminate\View\View
     */
    /**

     */
    public function listForCounty(Request $request, string $siteName)
    {
        Cookie::queue('siteName', $siteName);

        $currentPage = $request->input('page') ?? 1;

        $results = $this->queryService->getSiteListForDataset($siteName, $currentPage);

        return view('site-search',
        [
            'siteName' => $siteName,
            'showResults' => true,
            'results' =>$results,
        ]);
    }

    /**
     * Show the profile for a given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $nameSearchString
     * @param  string  $speciesGroup
     * @param  string  $nameType
     * @param  string  $axiophyteFilter
     * @return \Illuminate\View\View
     */
    public function updateDataset(Request $request, string $speciesName, string $speciesNameType, string $speciesGroup, string $axiophyteFilter)
    {
        Cookie::queue('speciesName', $speciesName);
        Cookie::queue('speciesNameType', $speciesNameType);
        Cookie::queue('speciesGroup', $speciesGroup);
        Cookie::queue('axiophyteFilter', $axiophyteFilter);

        $currentPage = $request->input('page') ?? 1;

        $results = $this->queryService->getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter, $currentPage);

        return view('data-tables/species-in-dataset',
        [
            'speciesName' => $speciesName,
            'speciesNameType' => $speciesNameType,
            'speciesGroup' => $speciesGroup,
            'axiophyteFilter' => $axiophyteFilter,
            'showResults' => true,
            'results' =>$results,
        ]);
    }
}
