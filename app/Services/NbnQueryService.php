<?php
namespace App\Services;

use App\Interfaces\QueryService;
use App\Models\OccurrenceResult;
use App\Models\QueryResult;

class NbnQueryService implements QueryService
{
    public function getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter, $currentPage = 1) : QueryResult
    {

        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURENCES_SEARCH);

        $speciesNameForSearch=$this->prepareSearchString($speciesName);
        $nbnQuery->addSpeciesNameType($speciesNameType, $speciesNameForSearch);
        if ($axiophyteFilter === "true")
            $nbnQuery->addAxiophyteFilter();

        $nbnQuery->addSpeciesGroup($speciesGroup);

        $nbnQuery->currentPage=$currentPage;
        $queryResult=$this->getPagedQueryResult($nbnQuery);

        return $queryResult;

    }


	/**
	 * Get the records for a single species
	 *
	 * e.g. https://records-ws.nbnatlas.org/occurrences/search?q=data_resource_uid:dr782&fq=taxon_name:Abies%20alba&sort=taxon_name&fsort=index&pageSize=9
	 *
	 * The taxon needs to be in double quotes so the complete string is searched for rather than a partial.
	 */
	public function getSingleSpeciesRecordsForDataset($speciesName, $currentPage) : QueryResult
	{
		// mainly to replace the spaces with %20
		$speciesName      = rawurlencode($speciesName);
		$nbnQuery       = new NbnQueryBuilder(NbnQueryBuilder::OCCURENCES_SEARCH);
		$nbnQuery->sort = "year";
		$nbnQuery->dir  = "desc";
		$nbnQuery
			->add('taxon_name:' . '"' . $speciesName . '"');
        $nbnQuery->currentPage=$currentPage;

        $queryResult=$this->getPagedQueryResult($nbnQuery);

        $queryResult->records=$this->prepareSingleSpeciesRecords($queryResult->records);

        $queryResult->sites=$this->prepareSites($queryResult->records);

		return $queryResult;
	}

    private function prepareSingleSpeciesRecords($records)
    {
        usort($records, function ($a, $b) {
            return $b->year <=> $a->year;
        });
        return $records;
    }

    private function prepareSites($records)
    {
        $sites = [];
        foreach ($records as $record)
        {
            $record->locationId = $record->locationId ?? '';
            $record->collector  = $record->collector ?? 'Unknown';

            // To plot site markers on the map, we must capture the locationId
            // (site name) and latLong of only the _first_ record for each site.
            // The latLong returned from the API is a single string, so we
            // convert into an array of two floats.
            if (! array_key_exists($record->locationId, $sites)&& isset($record->latLong))
            {
                $sites[$record->locationId] = array_map('floatval', explode(",", $record->latLong));
            }
        }
        return $sites;
    }


	public function getSingleOccurenceRecord($uuid)
    {
        $nbnQuery            = new NbnQueryBuilder(NbnQueryBuilder::OCCURENCE);
		$nbnQueryUrl              = $nbnQuery->url() . '/'.  $uuid;
		$queryResponse      = $this->callNbnApi($nbnQueryUrl);
        $occurrenceResult  = $this->createOccurrenceResult($queryResponse, $nbnQuery, $nbnQueryUrl);


		return $occurrenceResult;
	}


	public function getSiteListForDataset($siteName, $page){ return false; }
	public function getSpeciesListForSite($siteName, $speciesNameType, $speciesGroup, $axiophyteFilter, $page){ return false; }
	public function getSingleSpeciesRecordsForSite($site_name, $speciesName,$page){ return false; }

	public function getSpeciesListForSquare($gridSquare, $speciesGroup, $speciesNameType, $axiophyteFilter, $page){ return false; }
	public function getSingleSpeciesRecordsForSquare($gridSquare, $speciesName, $page){ return false; }

    private function getPagedQueryResult(NBNQueryBuilder $nbnQuery)
    {
        if ($nbnQuery->isFacetedSearch())
        {
            $nbnQueryUrl            = $nbnQuery->getUnpagedQueryString();
            $nbnQueryResponse    = $this->callNbnApi($nbnQueryUrl);
            $nbnQueryResponse->getRecords($nbnQuery->searchType);
            $totalNumberOfRecords 		 = $nbnQueryResponse->getNumberOfRecords($nbnQuery->searchType);
        }
        $nbnQueryUrl           = $nbnQuery->getPagingQueryString();
		$nbnQueryResponse = $this->callNbnApi($nbnQueryUrl);

        if ($nbnQuery->isFacetedSearch())
        {
            $queryResult  = $this->createQueryResult($nbnQueryResponse, $nbnQuery, $nbnQueryUrl,$totalNumberOfRecords);
        }
        else
            $queryResult  = $this->createQueryResult($nbnQueryResponse, $nbnQuery, $nbnQueryUrl);

        return $queryResult;
    }


    private function createQueryResult(NbnAPIResponse $nbnAPIResponse, NbnQueryBuilder $nbnQuery, string $queryUrl, ?int $numberOfRecords=null) : QueryResult
    {
		$queryResult = new QueryResult();
        $queryResult->status   	  = $nbnAPIResponse->status;
        $queryResult->message  	  = $nbnAPIResponse->message;
        $queryResult->queryUrl = $queryUrl;

		if ($nbnAPIResponse->status === 'OK' )
        {
            $queryResult->records     = $nbnAPIResponse->getRecords($nbnQuery->searchType);
            //where the number of records has been specified - because a facteted query and therefore dervied from unpaged query
            if (isset($numberOfRecords))
            {
                $queryResult->numberOfRecords=$numberOfRecords;
                $queryResult->numberOfPages=$nbnAPIResponse->getNumberOfPagesWithNumberOfRecords($nbnQuery->pageSize,$numberOfRecords);
            }
            else
            {
                $queryResult->numberOfRecords     = $nbnAPIResponse->getNumberOfRecords();
                $queryResult->numberOfPages     = $nbnAPIResponse->getNumberOfPages($nbnQuery->pageSize);
            }
            $queryResult->currentPage = $nbnQuery->currentPage;
            $queryResult->downloadLink = $nbnQuery->getDownloadQueryString();
        }
        return $queryResult;
    }

    private function createOccurrenceResult(NBNAPIResponse $nbnAPIResponse, $nbnQuery, $queryUrl)
    {
        $occurrenceResult= new OccurrenceResult();
        $occurrenceResult->status   	  = $nbnAPIResponse->status;
        $occurrenceResult->message  	  = $nbnAPIResponse->message;
        $occurrenceResult->queryUrl = $queryUrl;

        $occurrenceData=$nbnAPIResponse->getRecords($nbnQuery->searchType);

        $occurrenceResult->recordId=$occurrenceData->processed->rowKey;
        $occurrenceResult->scientificName=$occurrenceData->processed->classification->scientificName;
        $occurrenceResult->commonName=$occurrenceData->processed->classification->vernacularName ?? "";
        $occurrenceResult->phylum=$occurrenceData->processed->classification->phylum ?? "";

        $occurrenceResult->recorders=$this->prepareRecorders($occurrenceData->processed->occurrence->recordedBy);
        $occurrenceResult->siteName=$occurrenceData->raw->location->locationID  ?? "";
        $occurrenceResult->gridReference=$occurrenceData->raw->location->gridReference ?? "Unknown grid reference";
        $occurrenceResult->gridReferenceWKT=$occurrenceData->raw->location->gridReferenceWKT;
        $occurrenceResult->fullDate='Not available';
        if (isset($occurrenceData->processed->event->eventDate))
            $occurrenceResult->fullDate =date_format(date_create($occurrenceData->processed->event->eventDate),'jS F Y');
        $occurrenceResult->year=$occurrenceData->processed->event->year;
        $occurrenceData->phylum=$occurrenceData->processed->classification->phylum;




        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURENCE_DOWNLOAD);
		$occurrenceResult->downloadLink = $nbnQuery->getSingleRecordDownloadQueryString($occurrenceResult->recordId);

        return $occurrenceResult;

    }

    private function prepareRecorders(string $recorders) : string
    {
        $oldRecorders    = explode("|", $recorders);
        $newRecorders = [];
        foreach ($oldRecorders as $key => $value)
        {
            // Stick a semicolon between every other name pair
            if ($key !== 0 && $key % 2 === 0)
            {
                array_push($newRecorders, '; ');
            }
            array_push($newRecorders, $value);
        }
        return implode($newRecorders);
    }

    	/**
	 * Deals with multi-word search terms and prepares
	 * theme for use by the NBN API by adding ANDs and
	 * setting to all lower case
	 *
	 * @param string $searchString the search term to prepare
	 *
	 * @return string the prepared search search name
	 */
	private function prepareSearchString($searchString)
	{
		$searchString=ucfirst(strtolower($searchString));
		$searchWords  = explode(' ', $searchString);
		if (count($searchWords) === 1)
		{
			return '*' . $searchString . '*';
		}
		$preparedSearchString = $searchWords[0] . '*';
		unset($searchWords[0]);
		foreach ($searchWords as $searchWord)
		{
			$preparedSearchString .= '+AND+'. $searchWord;
		}
		$preparedSearchString = str_replace(' ', '+%2B', $preparedSearchString);
		return $preparedSearchString;
	}


	private function callNbnApi($queryUrl) : NBNApiResponse
	{
		$nbnApiResponse = new NbnApiResponse();
		try
		{
			//setting timeout to five seconds
            //TODO: make a env variable
			ini_set('default_socket_timeout', 5);
			$jsonResults  = file_get_contents($queryUrl);
			$jsonResponse = json_decode($jsonResults);

			if (isset($jsonResponse->status) &&  $jsonResponse->status === 'ERROR')
			{
				$nbnApiResponse->status  = 'ERROR';
				$errorMessage = $jsonResponse->errorMessage;
				if (strPos($errorMessage, 'No live SolrServers available') !== false)
				{
					$errorMessage = '<b>The NBN API is currently not able to provide results.</b>';
				}
				$nbnApiResponse->message = $errorMessage;
				$nbnApiResponse->jsonResponse = [];
			}
			else
			{
				$nbnApiResponse->jsonResponse = $jsonResponse;
				$nbnApiResponse->status       = 'OK';
			}
		}
		catch (\Throwable $e)
		{
			$nbnApiResponse->status = 'ERROR';
			$errorMessage           = $e->getMessage();
			if (strpos($errorMessage, '400 Bad Request') !== false)
			{
				$errorMessage = '<b>It looks like there is a problem with the query.</b>  Here are the details: ' . $errorMessage;
			}
			if (strpos($errorMessage, '500') !== false||strpos($errorMessage, '503') !== false ||strpos($errorMessage, 'php_network_getaddresses') !== false||strpos($errorMessage, 'SSL') !== false||strpos($errorMessage, 'stream') !== false)
			{
				$errorMessage = '<b>It looks like there is a problem with the NBN API</b>.  Here are the details: ' . $errorMessage;
			}
			$nbnApiResponse->message = $errorMessage;
		}
		return $nbnApiResponse;
	}
}



?>



