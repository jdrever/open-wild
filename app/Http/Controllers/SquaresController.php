<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Interfaces\QueryService;
use Illuminate\Http\Request;

class SquaresController extends Controller
{
    /**
     * The QueryService implementation.
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
     * handles initial site search.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $squareName = $request->input('squareName') ?? $request->cookie('squareName') ?? '';
        $speciesNameType = $request->cookie('speciesNameType') ?? 'scientific';
        $speciesGroup = $request->cookie('speciesGroup') ?? 'plants';
        $axiophyteFilter = $request->cookie('axiophyteFilter') ?? 'false';

        if (! $request->has('squareName')) {
            $mapState = $request->cookie('mapState') ?? env('DEFAULT_MAP_STATE');

            return view('squares-search',
            [
                'squareName'  => $squareName,
                'mapState'    => $mapState,
                'speciesNameType' => $speciesNameType,
                'speciesGroup' => $speciesGroup,
                'axiophyteFilter' => $axiophyteFilter,
                'showResults' => false,
            ]);
        } else {
            $mapState = $request->query('mapState');

            return redirect('/square/'.$squareName.'?mapState=' + $mapState);
        }
    }

    /**
     * Displays a list of sites in the dataset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $siteName
     * @return \Illuminate\View\View
     */
    public function listForDataset(Request $request, string $siteName, string $refresh = '')
    {
        $currentPage = $this->getCurrentPage($request);

        $speciesNameType = $request->cookie('speciesNameType') ?? 'scientific';
        $speciesGroup = $request->cookie('speciesGroup') ?? 'plants';
        $axiophyteFilter = $request->cookie('axiophyteFilter') ?? 'false';

        $results = $this->queryService->getSiteListForDataset($siteName, $currentPage);

        $viewName = ($refresh == 'refresh') ? 'data-tables/sites-in-dataset' : 'site-search';

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
