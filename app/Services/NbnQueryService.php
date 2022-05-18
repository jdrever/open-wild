<?php

namespace App\Services;

use App\Interfaces\QueryService;
use App\Models\AutocompleteResult;
use App\Models\OccurrenceResult;
use App\Models\QueryResult;
use App\Models\SingleSpeciesRecord;
use App\Models\Site;
use App\Models\Species;

class NbnQueryService implements QueryService
{
    public function getSpeciesListForDataset(string $speciesName, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage = 1): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);

        $nbnQuery
            ->addSpeciesNameType($speciesNameType, $speciesName, true, true)
            ->addSpeciesGroup($speciesGroup);
        if ($axiophyteFilter === 'true') {
            $nbnQuery->addAxiophyteFilter();
        }

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);
        $queryResult->records = $this->getSpeciesList($queryResult->records, $speciesNameType);

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
        $nbnQuery
            ->sortBy(NbnQueryBuilder::SORT_BY_YEAR)
            ->setDirection(NbnQueryBuilder::SORT_DESCENDING)
            ->addScientificName($speciesName, false, true); //add scientific name with double quotes added

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);

        $queryResult->records = $this->prepareSingleSpeciesRecords($queryResult->records);

        $queryResult->sites = $this->prepareSites($queryResult->records);
        $queryResult->records = $this->getSingleSpeciesRecordList($queryResult->records);

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

    public function getSiteListForDataset(string $siteName, int $currentPage = 1): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery->addWildcardLocationParamter($siteName);
        $nbnQuery->addSpeciesGroup('Both');

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);

        $queryResult->records = $this->getSiteList($queryResult->records);

        return $queryResult;
    }

    public function getSpeciesListForSite(string $siteName, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage = 1): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);

        $nbnQuery
            ->addSpeciesGroup($speciesGroup)
            ->setSpeciesNameType($speciesNameType)
            ->addLocation($siteName)
            ->setFacetedSort('index');

        if ($axiophyteFilter === 'true') {
            $nbnQuery->addAxiophyteFilter();
        }

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);
        $queryResult->records = $this->getSpeciesList($queryResult->records, $speciesNameType);

        return $queryResult;
    }

    public function getSingleSpeciesRecordsForSite(string $siteName, string $speciesName, int $currentPage = 1): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery
            ->addScientificName($speciesName)
            ->addLocation($siteName)
            ->setDirection('desc')
            ->sortBy('year');

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);
        $queryResult->records = $this->prepareSingleSpeciesRecords($queryResult->records);
        $queryResult->sites = $this->prepareSites($queryResult->records);
        $queryResult->records = $this->getSingleSpeciesRecordList($queryResult->records);

        return $queryResult;
    }

    public function getSpeciesListForSquare(string $gridSquare, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage = 1): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);

        $nbnQuery
            ->add1kmGridSquare($gridSquare)
            ->addSpeciesGroup($speciesGroup)
            ->setSpeciesNameType($speciesNameType)
            ->setFacetedSort('index');

        if ($axiophyteFilter === 'true') {
            $nbnQuery->addAxiophyteFilter();
        }

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);
        $queryResult->records = $this->getSpeciesList($queryResult->records, $speciesNameType);

        return $queryResult;
    }

    public function getSingleSpeciesRecordsForSquare(string $gridSquare, string $speciesName, int $currentPage = 1): QueryResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery
            ->addScientificName($speciesName)
            ->add1kmGridSquare($gridSquare)
            ->setDirection('desc')
            ->sortBy('year');

        $queryResult = $this->getPagedQueryResult($nbnQuery, $currentPage);
        $queryResult->records = $this->getSingleSpeciesRecordList($queryResult->records);

        $queryResult->records = $this->prepareSingleSpeciesRecords($queryResult->records);
        $queryResult->sites = $this->prepareSites($queryResult->records);

        return $queryResult;
    }

    public function getSpeciesNameAutocomplete(string $speciesName): AutocompleteResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::AUTOCOMPLETE_SEARCH);
        $nbnQueryUrl = $nbnQuery->getAutocompleteQueryString($speciesName);
        $nbnQueryResponse = $this->callNbnApi($nbnQueryUrl);
        $queryResult = $this->createSpeciesNameAutocompleteResult($nbnQueryResponse, $nbnQueryUrl);

        return $queryResult;
    }

    public function getSiteNameAutocomplete(string $siteName): AutocompleteResult
    {
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery->addWildcardLocationParamter($siteName);
        $nbnQuery->addSpeciesGroup('Both');
        $nbnQuery->pageSize = 10;
        $nbnQueryUrl = $nbnQuery->getUnpagedQueryString();
        $nbnQueryResponse = $this->callNbnApi($nbnQueryUrl);
        $queryResult = $this->createSiteNameAutocompleteResult($nbnQueryResponse, $nbnQueryUrl);

        return $queryResult;
    }

    private function getPagedQueryResult(NBNQueryBuilder $nbnQuery, int $currentPage)
    {
        $nbnQuery->currentPage = $currentPage;
        if ($nbnQuery->isFacetedSearch()) {
            $nbnQueryUrl = $nbnQuery->getUnpagedQueryString();
            $nbnQueryResponse = $this->callNbnApi($nbnQueryUrl);
            //if the unpaged query throws an error, return the error
            if ($nbnQueryResponse->status == false) {
                return $this->createQueryResult($nbnQueryResponse, $nbnQuery, $nbnQueryUrl);
            }

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

        if ($nbnAPIResponse->status) {
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

            $queryResult->siteLocation = $nbnAPIResponse->getSiteLocation();
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

    private function createSpeciesNameAutocompleteResult(NbnApiResponse $nbnApiResponse, string $queryUrl)
    {
        $queryResult = $this->createAutoCompleteResult($nbnApiResponse, $queryUrl);

        if ($nbnApiResponse->status) {
            $nbnApiRecords = $nbnApiResponse->getRecords(NbnQueryBuilder::AUTOCOMPLETE_SEARCH);
            $records = [];
            foreach ($nbnApiRecords as $record) {
                $speciesNameType = isset($record->commonName) ? 'Common Name' : 'Scientific Name';
                $records[] = $speciesNameType.': '.$record->matchedNames[0];
            }
            $queryResult->records = $records;
        }

        return $queryResult;
    }

    private function createSiteNameAutocompleteResult(NbnApiResponse $nbnApiResponse, string $queryUrl)
    {
        $queryResult = $this->createAutoCompleteResult($nbnApiResponse, $queryUrl);
        if ($nbnApiResponse->status) {
            $nbnApiRecords = $nbnApiResponse->getRecords(NbnQueryBuilder::OCCURRENCES_SEARCH);
            //dd($nbnApiRecords);
            $records = [];
            foreach ($nbnApiRecords as $record) {
                $records[] = $record->label;
            }
            $queryResult->records = $records;
        }

        return $queryResult;
    }

    private function createAutoCompleteResult(NbnApiResponse $nbnApiResponse, string $queryUrl): AutocompleteResult
    {
        $queryResult = new AutocompleteResult();
        $queryResult->status = $nbnApiResponse->status;
        $queryResult->message = $nbnApiResponse->message;
        $queryResult->queryUrl = $queryUrl;

        return $queryResult;
    }

    /**
     * Converts NBN record data into array of Species objects.
     *
     * @param [type] $records
     * @return iterable Species[]
     */
    private function getSpeciesList($records, $speciesNameType): iterable
    {
        $speciesList = [];
        foreach ($records as $record) {
            $species = new Species();
            $speciesArray = explode('|', (string) $record->label);
            if ($speciesNameType == 'scientific') {
                $species->family = $speciesArray[4];
                $species->scientificName = $speciesArray[0];
                $species->commonName = $speciesArray[2];
            }
            if ($speciesNameType == 'common') {
                $species->family = $speciesArray[5];
                $species->scientificName = $speciesArray[1];
                $species->commonName = $speciesArray[0];
            }
            $species->recordCount = $record->count;
            $speciesList[] = $species;
        }

        return $speciesList;
    }

    /**
     * Converts NBN record data into array of Site objects.
     *
     * @param [type] $records
     * @return iterable Site[]
     */
    private function getSiteList($records): iterable
    {
        $siteList = [];
        foreach ($records as $record) {
            $site = new Site();
            $site->name = $record->label;
            $site->recordCount = $record->count;
            $siteList[] = $site;
        }

        return $siteList;
    }

    /**
     * Converts NBN record data into array of SingleSpeciesRecord objects.
     *
     * @param [type] $records
     * @return iterable Site[]
     */
    private function getSingleSpeciesRecordList($records): iterable
    {
        $speciesRecordList = [];
        foreach ($records as $record) {
            $speciesRecord = new SingleSpeciesRecord();
            $speciesRecord->recordId = $record->uuid;
            $speciesRecord->site = $record->locationId;
            $speciesRecord->square = $record->gridReference;
            $speciesRecord->collector = $record->collector;
            $speciesRecord->decimalLongitude = $record->decimalLongitude;
            $speciesRecord->decimalLatitude = $record->decimalLatitude;
            $speciesRecord->year = $record->year;
            $speciesRecord->speciesGuid = $record->speciesGuid;
            $speciesRecordList[] = $speciesRecord;
        }

        return $speciesRecordList;
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
                $nbnApiResponse->status = false;
                $errorMessage = $jsonResponse->errorMessage;
                if (strpos($errorMessage, 'No live SolrServers available') !== false) {
                    $errorMessage = '<b>The NBN API is currently not able to provide results.</b>';
                }
                $nbnApiResponse->message = $errorMessage;
                $nbnApiResponse->jsonResponse = [];
            } else {
                $nbnApiResponse->jsonResponse = $jsonResponse;
                $nbnApiResponse->status = true;
            }
        } catch (\Throwable $e) {
            $nbnApiResponse->status = false;
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



