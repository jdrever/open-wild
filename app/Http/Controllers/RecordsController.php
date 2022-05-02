<?php

namespace App\Http\Controllers;

use App\Interfaces\QueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

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
     * Show the single species listing for a dataset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $speciesName
     * @return \Illuminate\View\View
     */
    public function singleSpeciesForDataset(Request $request, string $speciesName)
    {
        $currentPage = $this->getCurrentPage($request);

        $results = $this->queryService->getSingleSpeciesRecordsForDataset($speciesName, $currentPage);
        $speciesNameSearchedFor = Cookie::get('speciesName') ?? $speciesName;
        $speciesNameToDisplay = $request->input('speciesNameToDisplay') ?? $speciesName;
        $speciesNameType = Cookie::get('speciesNameType') ?? 'scientific';
        $speciesGroup = Cookie::get('speciesGroup') ?? 'plants';
        $axiophyteFilter = Cookie::get('axiophyteFilter') ?? 'false';

        $speciesGuid = isset($results->records[0]->speciesGuid) ? $results->records[0]->speciesGuid : '';

        return view('single-species-records',
        [
            'speciesName' => $speciesName,
            'speciesNameType' => $speciesNameType,
            'speciesGroup' => $speciesGroup,
            'axiophyteFilter' => $axiophyteFilter,
            'speciesGuid' => $speciesGuid,
            'speciesNameSearchedFor' => $speciesNameSearchedFor,
            'speciesNameToDisplay' => $speciesNameToDisplay,
            'results' =>$results,
        ]);
    }

    public function singleSpeciesForSquare(Request $request, $gridSquare, $speciesName)
	{
		// Get a 6 digit grid reference (1km square) from any length of original
		// grid reference by finding the midpoint of the position splitting the
		// string.
		$gsSplitPoint = strlen($gridSquare) / 2 + 1;
		$gridSquare = substr($gridSquare, 0, 4) . substr($gridSquare, $gsSplitPoint, 2);

        $currentPage = $this->getCurrentPage($request);

		$results                    = $this->queryService->getSingleSpeciesRecordsForSquare($gridSquare, $speciesName, $currentPage);

        return view('square-species-records',
        [
            'gridSquare' => $gridSquare,
            'speciesName' => $speciesName,
            'results' =>$results,
        ]);
		echo view('square_species_records', $this->data);
	}

    public function singleRecord(Request $request, string $occurrenceId)
    {
        $result = $this->queryService->getSingleOccurenceRecord($occurrenceId);

        $displayName = $request->input('displayName') ?? $result->scientificName;
        $displayTitle = 'Record detail for '.urldecode($displayName).' recorded by '.$result->recorders.' at '.$result->siteName.' ('.$result->gridReference.'),'.$result->year.'.';

        return view('single-record',
        [
            'result' =>$result,
            'displayName' => $displayName,
            'displayTitle' => $displayTitle,
        ]);
    }
}
