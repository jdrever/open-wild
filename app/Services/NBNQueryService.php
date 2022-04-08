<?php
namespace App\Services;

use App\Interfaces\QueryService;
use App\Models\QueryResult;

class NBNQueryService implements QueryService
{
    public function getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter, $page) : QueryResult
    {

        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURENCES_SEARCH);

        $speciesNameForSearch=$this->prepareSearchString($speciesName);
        $nbnQuery->addSpeciesNameType($speciesNameType, $speciesNameForSearch);
        if ($axiophyteFilter === "true")
            $nbnQuery->addAxiophyteFilter();

        $nbnQuery->addSpeciesGroup($speciesGroup);

        //first get number of records from unpaged query
        $nbnQueryUrl            = $nbnQuery->getUnpagedQueryString();
        $nbnQueryResponse    = $this->callNbnApi($nbnQueryUrl);
        $totalNumberOfRecords 		 = $nbnQueryResponse->getNumberOfRecords($nbnQuery->searchType);


        // then get paged results
        //TODO: number of records in paged result should be a constant.
        $nbnQuery->flimit   = config('RESULTS_PER_PAGE');
        $nbnQueryUrl             = $nbnQuery->getPagingQueryStringWithFacetStart($page);
        $nbnQueryResponse = $this->callNbnApi($nbnQueryUrl);


        $queryResult  = $this->createQueryResult($nbnQueryResponse, $nbnQuery, $nbnQueryUrl);
        $queryResult->totalNumberOfRecords=$totalNumberOfRecords;

        return $queryResult;

    }


	/**
	 * Get the records for a single species
	 *
	 * e.g. https://records-ws.nbnatlas.org/occurrences/search?q=data_resource_uid:dr782&fq=taxon_name:Abies%20alba&sort=taxon_name&fsort=index&pageSize=9
	 *
	 * The taxon needs to be in double quotes so the complete string is searched for rather than a partial.
	 */
	public function getSingleSpeciesRecordsForDataset($speciesName, $page) : QueryResult
	{
		// mainly to replace the spaces with %20
		$speciesName      = rawurlencode($speciesName);
		$nbnQuery       = new NbnQueryBuilder(NbnQueryBuilder::OCCURENCES_SEARCH);
		$nbnQuery->sort = "year";
		$nbnQuery->dir  = "desc";
		$nbnQuery
			->add('taxon_name:' . '"' . $speciesName . '"');

        $nbnQueryUrl           = $nbnQuery->getPagingQueryStringWithStart($page);
		$nbnQueryResponse = $this->callNbnApi($nbnQueryUrl);

        $queryResult  = $this->createQueryResult($nbnQueryResponse, $nbnQuery, $nbnQueryUrl);

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
		$nbnQueryUrl              = $nbnQuery->url() . $uuid;
		$queryResponse      = $this->callNbnApi($nbnQueryUrl);
        $queryResult  = $this->createQueryResult($queryResponse, $nbnQuery, $nbnQueryUrl);

		$nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURENCE_DOWNLOAD);
		$queryResult->downloadLink = $nbnQuery->getSingleRecordDownloadQueryString($queryResult->records->raw->occurrence->occurrenceID);
		return $queryResult;
	}


	public function getSiteListForDataset($siteName, $page){ return false; }
	public function getSpeciesListForSite($siteName, $speciesNameType, $speciesGroup, $axiophyteFilter, $page){ return false; }
	public function getSingleSpeciesRecordsForSite($site_name, $speciesName,$page){ return false; }

	public function getSpeciesListForSquare($gridSquare, $speciesGroup, $speciesNameType, $axiophyteFilter, $page){ return false; }
	public function getSingleSpeciesRecordsForSquare($gridSquare, $speciesName, $page){ return false; }


    private function createQueryResult(NbnAPIResponse $nbnAPIResponse, NbnQueryBuilder $nbnQuery, string $queryUrl) : QueryResult
    {
		$queryResult = new QueryResult();
        $queryResult->status   	  = $nbnAPIResponse->status;
        $queryResult->message  	  = $nbnAPIResponse->message;
        $queryResult->queryUrl = $queryUrl;

		if ($nbnAPIResponse->status === 'OK' )
        {
            $queryResult->records     = $nbnAPIResponse->getRecords($nbnQuery->searchType);
            $queryResult->numberOfRecords     = $nbnAPIResponse->getNumberOfRecords($nbnQuery->searchType);
            $queryResult->totalNumberOfRecords = $nbnAPIResponse->getTotalNumberOfRecords($nbnQuery->searchType);
            $queryResult->downloadLink = $nbnQuery->getDownloadQueryString();
        }
        return $queryResult;
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



