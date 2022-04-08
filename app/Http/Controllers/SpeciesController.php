<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Interfaces\QueryService;

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
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        echo("here");
        $speciesName=$request->input('speciesName') ?? $request->cookie('speciesName') ?? "" ;
        $speciesNameType=$request->input('speciesNameType') ?? $request->cookie('speciesNameType') ?? "scientific" ;
        $speciesGroup=$request->input('speciesGroup') ?? $request->cookie('speciesGroup') ?? "plants" ;
        $axiophyteFilter=$request->input('axiophyteFilter')  ?? $request->cookie('axiophyteFilter') ?? "false" ;
        echo($speciesNameType);
        if (!$request->has("speciesName"))
        {
            return view('species-search',
            [
                'speciesName' => $speciesName,
                'speciesNameType' => $speciesNameType,
                'speciesGroup' => $speciesGroup,
                'axiophyteFilter' => $axiophyteFilter
            ]);
        }
        else
            return redirect('/species/' . $speciesName . '/type/' . $speciesNameType . '/group/' .$speciesGroup . '/axiophytes/' . $axiophyteFilter);
    }

    /**
     * Show the profile for a given user.
     *
     * @param  string  $nameSearchString
     * @param  string  $speciesGroup
     * @param  string  $nameType
     * @param  string  $axiophyteFilter
     * @return \Illuminate\View\View
     */
    public function listForCounty($speciesName, $speciesNameType, $speciesGroup,$axiophyteFilter)
	{
        Cookie::queue('speciesName', $speciesName);


        $results=$this->queryService->getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter,1);
        return view('species-search',
        [
            'speciesName' => $speciesName,
            'speciesNameType' => $speciesNameType,
            'speciesGroup' => $speciesGroup,
            'axiophyteFilter' => $axiophyteFilter,
            'results' =>$results
         ]);
    }
}
