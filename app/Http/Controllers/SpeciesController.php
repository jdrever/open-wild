<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Interfaces\QueryService;
use Illuminate\Http\Request;

class SpeciesController extends Controller
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
     * @param  QueryService  $queryService
     * @return void
     */
    public function __construct(QueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * handles initial species search.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $speciesName = $request->input('speciesName') ?? $request->cookie('speciesName') ?? '';
        $speciesNameType = $request->input('speciesNameType') ?? $request->cookie('speciesNameType') ?? 'scientific';
        $speciesGroup = $request->input('speciesGroup') ?? $request->cookie('speciesGroup') ?? 'plants';
        $axiophyteFilter = $request->input('axiophyteFilter') ?? $request->cookie('axiophyteFilter') ?? 'false';
        if (! $request->has('speciesName')) {
            return view('species-search',
            [
                'speciesName' => $speciesName,
                'speciesNameType' => $speciesNameType,
                'speciesGroup' => $speciesGroup,
                'axiophyteFilter' => $axiophyteFilter,
                'showResults' => false,
            ]);
        } else {
            return redirect('/species/'.$speciesName.'/type/'.$speciesNameType.'/group/'.$speciesGroup.'/axiophytes/'.$axiophyteFilter);
        }
    }

    /**
     * Displays a list of species in the dataset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $nameSearchString
     * @param  string  $speciesGroup
     * @param  string  $nameType
     * @param  string  $axiophyteFilter
     * @return \Illuminate\View\View
     */
    public function listForDataset(Request $request, string $speciesName, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, string $refresh = '')
    {
        $this->setCookies($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter);

        $currentPage = $this->getCurrentPage($request);

        $results = $this->queryService->getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter, $currentPage);

        $viewName = ($refresh == 'refresh') ? 'data-tables/species-in-dataset' : 'species-search';

        return view($viewName,
        [
            'speciesName' => $speciesName,
            'speciesNameType' => $speciesNameType,
            'speciesGroup' => $speciesGroup,
            'axiophyteFilter' => $axiophyteFilter,
            'showResults' => true,
            'results' =>$results,
        ]);
    }

    public function listforSquare(Request $request, $gridSquare, $speciesGroup, $nameType, $axiophyteFilter)
    {
        $this->setCookies($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter);
        $results = $this->queryService->getSpeciesListForSquare($gridSquare, $speciesGroup, $nameType, $axiophyteFilter, $this->page);
    }
}
