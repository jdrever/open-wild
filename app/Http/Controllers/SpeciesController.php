<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
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
        echo(var_dump($request->input));
        $speciesName=$request->input('speciesName', '');
        $speciesNameType=$request->input('speciesNameType', '');

        if (empty($speciesName))
            return view('species-search',
            [
                'speciesName' => $speciesName,
                'speciesNameType' => $speciesNameType
            ]);
        else
            return redirect('/species/' . $speciesName . '/type/' . $speciesNameType);
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
    public function listForCounty($speciesName, $speciesNameType)
	{
        return view('species-search',
        [
            'speciesName' => $speciesName,
            'speciesNameType' => $speciesNameType
         ]);
    }
}
