<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Interfaces\QueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SpeciesController extends Controller
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
     * handles initial species search
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
        Cookie::queue('speciesName', $speciesName);
        Cookie::queue('speciesNameType', $speciesNameType);
        Cookie::queue('speciesGroup', $speciesGroup);
        Cookie::queue('axiophyteFilter', $axiophyteFilter);

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
}
