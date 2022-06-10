<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Interfaces\QueryService;
use Illuminate\Http\Request;

class RadiusController extends Controller
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
        $latitude = $request->input('latitude') ?? $request->cookie('latitude') ?? '';
        $longitude = $request->cookie('longitude') ?? 'longitude';

        if (! $request->has('latitude')) {
            $mapState = $request->cookie('mapState') ?? env('DEFAULT_MAP_STATE');

            return view('radius-search',
            [
                'latitude'  => $latitude,
                'longitude' => $longitude,
                'mapState'    => $mapState,
                'showResults' => false,
            ]);
        } else {
            $mapState = $request->query('mapState');

            return redirect('/radius/latitude/'.$latitude.'/longitude/'.$longitude.'?mapState=' + $mapState);
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
