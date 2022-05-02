<?php

namespace App\Services;

use App\Interfaces\QueryService;
use App\Models\OccurrenceResult;
use App\Models\QueryResult;

class NbnQueryService implements QueryService
{
    public function getSpeciesListForDataset(string $speciesName, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage = 1): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);

        $nbnQuery->addSpeciesNameType($speciesNameType, $speciesName, true, true);
        if ($axiophyteFilter === 'true') {
            $nbnQuery->addAxiophyteFilter();
        }

        $nbnQuery->addSpeciesGroup($speciesGroup);

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);

        return $queryResult;
    }

    /**
     * Get the records for a single species.
     *
     * e.g. https://records-ws.nbnatlas.org/occurrences/search?q=data_resource_uid:dr782&fq=taxon_name:Abies%20alba&sort=taxon_name&fsort=index&pageSize=9
     *
     * The taxon needs to be in double quotes so the complete string is searched for rather than a partial.
     */
    public function getSingleSpeciesRecordsForDataset(string $speciesName, int $currentPage = 1): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery->sortBy(NbnQueryBuilder::SORT_BY_YEAR);
        $nbnQuery->setDirection(NbnQueryBuilder::SORT_DESCENDING);
        $nbnQuery
            ->addScientificName($speciesName, false, true); //add scientific name with double quotes added

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);

        $queryResult->records = $this->prepareSingleSpeciesRecords($queryResult->records);

        $queryResult->sites = $this->prepareSites($queryResult->records);

        return $queryResult;
    }

    public function getSingleOccurenceRecord(string $uuid): OccurrenceResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCE);
        $nbnQueryUrl = $nbnQuery->url().'/'.$uuid;
        $queryResponse = $this->callNbnApi($nbnQueryUrl);
        $occurrenceResult = $this->createOccurrenceResult($queryResponse, $nbnQuery, $nbnQueryUrl);

        return $occurrenceResult;
    }

    public function getSiteListForDataset(string $siteName, int $currentPage): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery->facets = 'location_id';
        $nbnQuery->addExtraQueryParameter('location_id:'.$siteName.'*');
        $nbnQuery->addSpeciesGroup('Both');

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);

        return $queryResult;
    }

    public function getSpeciesListForSite(string $siteName, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);

        $nbnQuery->addSpeciesGroup($speciesGroup);
        $nbnQuery->setSpeciesNameType($speciesNameType);

        if ($axiophyteFilter === 'true') {
            $nbnQuery->addAxiophyteFilter();
        }

        $nbnQuery->setFacetedSort('index');

        $nbnQuery->add('location_id:'.str_replace(' ', "\%20", $siteName)); // Use "\ " instead of spaces to search only for species matching whole site name

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);

        // Get site location from first occurrence
        if (isset($queryResult->jsonResponse->occurrences[0]->decimalLatitude)) {
            $queryResult->siteLocation = [$queryResult->jsonResponse->occurrences[0]->decimalLatitude, $queryResult->jsonResponse->occurrences[0]->decimalLongitude];
        } else {
            // No location data - currently just doesn't show a site marker
            $queryResult->siteLocation = [];
        }

        return $queryResult;
    }

    public function getSingleSpeciesRecordsForSite($siteName, $speciesName, $currentPage): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery->addScientificName($speciesName);
        $nbnQuery->setDirection('desc');
        $nbnQuery->sortBy('year');
        $nbnQuery->add('location_id:'.'"'.urlencode($siteName).'"');

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);
        $queryResult->records = $this->prepareSingleSpeciesRecords($queryResult->records);
        $queryResult->sites = $this->prepareSites($queryResult->records);

        return $queryResult;
    }

    public function getSpeciesListForSquare(string $gridSquare, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);

        $nbnQuery->addSpeciesGroup($speciesGroup);
        $nbnQuery->setSpeciesNameType($speciesNameType);

        if ($axiophyteFilter === 'true') {
            $nbnQuery->addAxiophyteFilter();
        }

        $nbnQuery->add('grid_ref_1000:"'.rawurlencode($gridSquare).'"');

        $nbnQuery->setFacetedSort('index');

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);

        return $queryResult;
    }

    public function getSingleSpeciesRecordsForSquare($gridSquare, $speciesName, $currentPage): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery->addScientificName($speciesName);
        $nbnQuery->add('grid_ref_1000:'.'"'.urlencode($gridSquare).'"');
        $nbnQuery->setDirection('desc');
        $nbnQuery->sortBy('year');

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);
        $queryResult->records = $this->prepareSingleSpeciesRecords($queryResult->records);
        $queryResult->sites = $this->prepareSites($queryResult->records);

        return $queryResult;
    }

    private function getPagedQueryResult(NBNQueryBuilder $nbnQuery, int $currentPage)
    {
        $nbnQuery->currentPage = $currentPage;
        if ($nbnQuery->isFacetedSearch()) {
            $nbnQueryUrl = $nbnQuery->getUnpagedQueryString();
            $nbnQueryResponse = $this->callNbnApi($nbnQueryUrl);
            $nbnQueryResponse->getRecords($nbnQuery->searchType);
            $totalNumberOfRecords = $nbnQueryResponse->getNumberOfRecords($nbnQuery->searchType);
        }
        $nbnQueryUrl = $nbnQuery->getPagingQueryString();
        $nbnQueryResponse = $this->callNbnApi($nbnQueryUrl);

        if ($nbnQuery->isFacetedSearch()) {
            $queryResult = $this->createQueryResult($nbnQueryResponse, $nbnQuery, $nbnQueryUrl, $totalNumberOfRecords);
        } else {
            $queryResult = $this->createQueryResult($nbnQueryResponse, $nbnQuery, $nbnQueryUrl);
        }

        return $queryResult;
    }

    private function createQueryResult(NbnAPIResponse $nbnAPIResponse, NbnQueryBuilder $nbnQuery, string $queryUrl, ?int $numberOfRecords = null): QueryResult
    {
        $queryResult = new QueryResult();
        $queryResult->status = $nbnAPIResponse->status;
        $queryResult->message = $nbnAPIResponse->message;
        $queryResult->queryUrl = $queryUrl;

        if ($nbnAPIResponse->status === 'OK') {
            $queryResult->records = $nbnAPIResponse->getRecords($nbnQuery->searchType);
            //where the number of records has been specified - because a facteted query and therefore dervied from unpaged query
            if (isset($numberOfRecords)) {
                $queryResult->numberOfRecords = $numberOfRecords;
                $queryResult->numberOfPages = $nbnAPIResponse->getNumberOfPagesWithNumberOfRecords($nbnQuery->pageSize, $numberOfRecords);
            } else {
                $queryResult->numberOfRecords = $nbnAPIResponse->getNumberOfRecords();
                $queryResult->numberOfPages = $nbnAPIResponse->getNumberOfPages($nbnQuery->pageSize);
            }
            $queryResult->currentPage = $nbnQuery->currentPage;
            $queryResult->downloadLink = $nbnQuery->getDownloadQueryString();
        }

        return $queryResult;
    }

    private function createOccurrenceResult(NBNAPIResponse $nbnAPIResponse, $nbnQuery, $queryUrl): OccurrenceResult
    {
        $occurrenceResult = new OccurrenceResult();
        $occurrenceResult->status = $nbnAPIResponse->status;
        $occurrenceResult->message = $nbnAPIResponse->message;
        $occurrenceResult->queryUrl = $queryUrl;

        $occurrenceData = $nbnAPIResponse->getRecords($nbnQuery->searchType);

        $occurrenceResult->recordId = $occurrenceData->processed->rowKey;
        $occurrenceResult->scientificName = $occurrenceData->processed->classification->scientificName;
        $occurrenceResult->commonName = $occurrenceData->processed->classification->vernacularName ?? '';
        $occurrenceResult->phylum = $occurrenceData->processed->classification->phylum ?? '';

        $occurrenceResult->recorders = $this->prepareRecorders($occurrenceData->processed->occurrence->recordedBy);
        $occurrenceResult->siteName = $occurrenceData->raw->location->locationID ?? '';
        $occurrenceResult->gridReference = $occurrenceData->raw->location->gridReference ?? 'Unknown grid reference';
        $occurrenceResult->gridReferenceWKT = $occurrenceData->raw->location->gridReferenceWKT;
        $occurrenceResult->fullDate = 'Not available';
        if (isset($occurrenceData->processed->event->eventDate)) {
            $occurrenceResult->fullDate = date_format(date_create($occurrenceData->processed->event->eventDate), 'jS F Y');
        }
        $occurrenceResult->year = $occurrenceData->processed->event->year;
        $occurrenceData->phylum = $occurrenceData->processed->classification->phylum;

        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCE_DOWNLOAD);
        $occurrenceResult->downloadLink = $nbnQuery->getSingleRecordDownloadQueryString($occurrenceResult->recordId);

        return $occurrenceResult;
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
        foreach ($records as $record) {
            $record->locationId = $record->locationId ?? '';
            $record->collector = $record->collector ?? 'Unknown';

            // To plot site markers on the map, we must capture the locationId
            // (site name) and latLong of only the _first_ record for each site.
            // The latLong returned from the API is a single string, so we
            // convert into an array of two floats.
            if (! array_key_exists($record->locationId, $sites) && isset($record->latLong)) {
                $sites[$record->locationId] = array_map('floatval', explode(',', $record->latLong));
            }
        }

        return $sites;
    }

    private function prepareRecorders(string $recorders): string
    {
        $oldRecorders = explode('|', $recorders);
        $newRecorders = [];
        foreach ($oldRecorders as $key => $value) {
            // Stick a semicolon between every other name pair
            if ($key !== 0 && $key % 2 === 0) {
                array_push($newRecorders, '; ');
            }
            array_push($newRecorders, $value);
        }

        return implode($newRecorders);
    }

    private function callNbnApi($queryUrl): NBNApiResponse
    {
        $nbnApiResponse = new NbnApiResponse();
        try {
            //setting timeout to five seconds
            //TODO: make a env variable
            ini_set('default_socket_timeout', 5);
            $jsonResults = file_get_contents($queryUrl);
            $jsonResponse = json_decode($jsonResults);

            if (isset($jsonResponse->status) && $jsonResponse->status === 'ERROR') {
                $nbnApiResponse->status = 'ERROR';
                $errorMessage = $jsonResponse->errorMessage;
                if (strpos($errorMessage, 'No live SolrServers available') !== false) {
                    $errorMessage = '<b>The NBN API is currently not able to provide results.</b>';
                }
                $nbnApiResponse->message = $errorMessage;
                $nbnApiResponse->jsonResponse = [];
            } else {
                $nbnApiResponse->jsonResponse = $jsonResponse;
                $nbnApiResponse->status = 'OK';
            }
        } catch (\Throwable $e) {
            $nbnApiResponse->status = 'ERROR';
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, '400 Bad Request') !== false) {
                $errorMessage = '<b>It looks like there is a problem with the query.</b>  Here are the details: '.$errorMessage;
            }
            if (strpos($errorMessage, '500') !== false || strpos($errorMessage, '503') !== false || strpos($errorMessage, 'php_network_getaddresses') !== false || strpos($errorMessage, 'SSL') !== false || strpos($errorMessage, 'stream') !== false) {
                $errorMessage = '<b>It looks like there is a problem with the NBN API</b>.  Here are the details: '.$errorMessage;
            }
            $nbnApiResponse->message = $errorMessage;
        }

        return $nbnApiResponse;
    }
}

?>



