<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Interfaces\QueryService;

class RecordsController extends Controller
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


    /**
     * Show the single species listing for a dataset
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $speciesName
     * @return \Illuminate\View\View
     */
    public function singleSpeciesForDataset(Request $request, string $speciesName)
	{
        $currentPage=$request->input('page') ?? 1;

        $results=$this->queryService->getSingleSpeciesRecordsForDataset($speciesName,$currentPage);
        $speciesNameSearchedFor=Cookie::get('speciesName') ?? $speciesName ;
        $speciesNameToDisplay=$request->input('speciesNameToDisplay') ?? $speciesName;
        $speciesNameType=Cookie::get('speciesNameType') ?? "scientific" ;
        $speciesGroup=Cookie::get('speciesGroup') ?? "plants" ;
        $axiophyteFilter=Cookie::get('axiophyteFilter') ?? "false" ;

        $speciesGuid=isset($results->records->records[0]->speciesGuid) ? $results->records->records[0]->speciesGuid : '';
        return view('single-species-records',
        [
            'speciesName' => $speciesName,
            'speciesNameType' => $speciesNameType,
            'speciesGroup' => $speciesGroup,
            'axiophyteFilter' => $axiophyteFilter,
            'speciesGuid' => $speciesGuid,
            'speciesNameSearchedFor' => $speciesNameSearchedFor,
            'speciesNameToDisplay' => $speciesNameToDisplay,
            'results' =>$results
         ]);
    }
}
