<?php

namespace App\Services;

/**
 * Facade for the NBN records end point.
 *
 * See the NBN Atlas Query Primer for details about using the API
 * https://docs.google.com/document/d/1FiVasGGZ3kRPnu5347GPAef7Tr5LvvghCS6x82xnfu4/edit
 *
 * @author  Careful Digital <hello@careful.digital>
 */
class NbnQueryBuilder
{
    const BASE_URL = 'https://records-ws.nbnatlas.org';

    const OCCURRENCES_SEARCH = '/occurrences/search';
    const OCCURRENCE = '/occurrence';
    const OCCURRENCE_DOWNLOAD = 'occurrences/index/download';

    const SCIENTIFIC_NAME = 'taxon_name';

    const SORT_BY_YEAR = 'year';
    const SORT_DESCENDING = 'desc';
    /**
     * The unique data resource id code.
     *
     * The id can by found by searching the NBN Atlas data sets at
     * https://registry.nbnatlas.org/datasets. The id is located in the URL of a
     * data resource page and consists of the letters "dr" followed by a number;
     * e.g., https://registry.nbnatlas.org/public/showDataResource/dr782
     *
     * dr782 is the SEDN data set.
     *
     * Use dr1323 for Worcestershire data if SEDN data not available:
     * https://registry.nbnatlas.org/public/showDataResource/dr1323
     *
     * @var string
     */
    private $dataResourceUid = '';

    /**
     * The filter for axiophytes in Shropshire, as supplied by Sophie at NBN is species_list_uid:dr1940.
     *
     * @var string
     */
    private $axiophyteFilter = '';

    /**
     * TODO: Describe what the $searchType member variable is for.
     *
     * @var string
     */
    public $searchType = '';

    /**
     * TODO: Describe what the $facets member variable is for.
     *
     * @var string
     */
    public $facets;

    /**
     * TODO: Describe what the $fsort member variable is for.
     *
     * @var string
     */
    public $fsort;

    /**
     * Sets the number of paged records returned by each NBN query.
     *
     * @var int
     */
    public $pageSize;

    /**
     * Sets the number of the current page.
     *
     * @var int
     */
    public $currentPage;

    /**
     * TODO: Describe what the $sort member variable is for.
     *
     * @var string
     */
    public $sort;

    /**
     * The direction of the sort.
     *
     * @var string
     */
    public $dir = 'asc';

    /**
     * Constructor.
     *
     * Accepts a searchType fragment which indicates the NBN Atlas API search type to
     * perform. Defaults to Occurrence search: https://api.nbnatlas.org/#ws3
     *
     * See https://api.nbnatlas.org/ for others.
     *
     * @param  string  $searchType  NBN Atlas API search type
     */
    public function __construct(string $searchType = self::OCCURRENCES_SEARCH)
    {
        $this->searchType = $searchType;
        $this->pageSize = env('RESULTS_PER_PAGE', 10);
        $this->dataResourceUid = env('DATA_RESOURCE_ID');
        $this->axiophyteFilter = env('AXIOPHYTE_FILTER');
        $this->currentPage = 1;
    }

    /**
     * Return the base search query string.
     *
     * @param  string  $url  The full url to query
     * @return string
     */
    private function getQueryString(string $url)
    {
        $queryString = $url.'?';
        $queryParameters = array_merge(['data_resource_uid:'.$this->dataResourceUid], $this->extraQueryParameters);
        $fqAndParameters = implode('%20AND%20', $this->filterQueryParameters);
        $fqNotParameters = '';
        if (count($this->filterNotQueryParameters) > 0) {
            $fqNotParameters = '%20AND%20NOT%20'.implode('%20AND%20NOT%20', $this->filterNotQueryParameters);
        }
        $queryString .= 'q='.implode('%20AND%20', $queryParameters).'&';
        $queryString .= 'fq='.$fqAndParameters.$fqNotParameters.'&';
        $queryString .= 'facets='.$this->facets.'&';
        $queryString .= 'sort='.$this->sort.'&';
        $queryString .= 'fsort='.$this->fsort.'&';
        $queryString .= 'dir='.$this->dir.'&';

        return $queryString;
    }

    /**
     * Return the url for single record download querty.
     *
     * @param  string  $url  The full url to query
     * @return string
     */
    private function getSingleRecordDownloadUrl(string $url, string $occurrenceId)
    {
        $queryString = $url.'?';
        $queryString .= 'fq=occurrence_id:'.$occurrenceId.'&';

        return $queryString;
    }

    /**
     * Return the base url and searchType (really only used for getting a single
     * occurence record).
     *
     * @return string
     */
    public function url()
    {
        return $this::BASE_URL.$this->searchType;
    }

    public function isFacetedSearch()
    {
        return ! empty($this->facets);
    }

    /**
     * Return the query string without paging
     * Used to determine total number of records for query
     * without paging.
     *
     * @return string
     */
    public function getUnpagedQueryString()
    {
        $queryString = $this->getQueryString($this::BASE_URL.$this->searchType);
        $queryString .= 'pageSize=0&flimit=-1';

        return $queryString;
    }

    /**
     * Return the query string for paging.
     *
     * @return string
     */
    public function getPagingQueryString()
    {
        $queryString = $this->getQueryString($this::BASE_URL.$this->searchType);

        if ($this->isFacetedSearch()) {
            $queryString .= 'flimit='.$this->pageSize.'&facet.offset='.(($this->currentPage - 1) * $this->pageSize);
        } else {
            $queryString .= 'pageSize='.$this->pageSize.'&start='.(($this->currentPage - 1) * $this->pageSize);
        }

        return $queryString;
    }

    /**
     * Return the query string for downloading the data.
     *
     * @return string
     */
    public function getDownloadQueryString()
    {
        $queryString = $this->getQueryString($this::BASE_URL.'/occurrences/index/download');
        $queryString .= '&reasonTypeId=11&fileType=csv';

        return $queryString;
    }

    /**
     * Return the query string for downloading the data.
     *
     * @return string
     */
    public function getSingleRecordDownloadQueryString($occurrenceId)
    {
        $queryString = $this->getSingleRecordDownloadUrl($this::BASE_URL.'occurrences/index/download', $occurrenceId);
        $queryString .= '&reasonTypeId=11';

        return $queryString;
    }

    /**
     * Keeps an internal array of query filter parameters.
     *
     * A list of available index fields can be found at
     * https://species-ws.nbnatlas.org/admin/indexFields
     *
     * @var string[] Array of strings
     */
    protected $filterQueryParameters = [];

    /**
     * Adds to the internal list of filter query parameters.
     *
     * A list of available index fields can be found at
     * https://species-ws.nbnatlas.org/admin/indexFields
     *
     * @param  string  $filterQueryParameter  A single filter query parameter
     * @return $this
     */
    public function add(string $filterQueryParameter)
    {
        $this->filterQueryParameters[] = $filterQueryParameter;

        return $this;
    }

    /**
     * Keeps an internal array of query filter parameters.
     *
     * A list of available index fields can be found at
     * https://species-ws.nbnatlas.org/admin/indexFields
     *
     * @var string[] Array of strings
     */
    protected $filterNotQueryParameters = [];

    /**
     * Adds to the internal list of filter NOT query parameters.
     *
     * A list of available index fields can be found at
     * https://species-ws.nbnatlas.org/admin/indexFields
     *
     * @param  string  $filterNotQueryParameter  A single filter query parameter
     * @return $this
     */
    public function addNot(string $filterNotQueryParameter)
    {
        $this->filterNotQueryParameters[] = $filterNotQueryParameter;

        return $this;
    }

    /**
     * Adds a taxon_name or common_name query parameter, dependent on $speciesNameType.
     *
     * @param  string  $speciesNameType  either scientific or common
     * @param  string  $speciesName  e.g. Hedera or Ivy
     * @return void
     */
    public function addSpeciesNameType(string $speciesNameType, string $speciesName, bool $isFacetedSearch = false, bool $isPartialName = false): void
    {
        if ($speciesNameType === 'scientific') {
            $this->addScientificName($speciesName, $isFacetedSearch, $isPartialName);
        }

        if ($speciesNameType === 'common') {
            $this->addCommonName($speciesName, $isFacetedSearch, $isPartialName);
        }
    }

    public function addScientificName(string $speciesName, bool $isFacetedSearch = false, bool $isPartialName = false): void
    {
        $speciesName = $this->prepareSearchString($speciesName, $isPartialName);

        $this->add('taxon_name:'.$speciesName);
        if ($isFacetedSearch) {
            $this->facets = 'names_and_lsid';
            $this->fsort = 'index';
        }
    }

    public function addCommonName(string $speciesName, bool $isFacetedSearch = false, bool $isPartialName = false): void
    {
        $speciesName = $this->prepareSearchString($speciesName, $isPartialName);
        $this->add('common_name:'.$speciesName);
        if ($isFacetedSearch) {
            $this->facets = 'common_name_and_lsid';
            $this->fsort = 'index';
        }
    }

    /**
     * Deals with multi-word search terms and prepares
     * theme for use by the NBN API by adding ANDs and
     * setting to all lower case.
     *
     * @param  string  $searchString  the search term to prepare
     * @return string the prepared search search name
     */
    private function prepareSearchString(string $searchString, bool $isPartialName)
    {
        if (! $isPartialName) {
            return '"'.rawurlencode($searchString).'"';
        }

        $searchString = ucfirst(strtolower($searchString));
        $searchWords = explode(' ', $searchString);
        if (count($searchWords) === 1) {
            return '*'.rawurlencode($searchString).'*';
        }
        $preparedSearchString = $searchWords[0].'*';
        unset($searchWords[0]);
        foreach ($searchWords as $searchWord) {
            $preparedSearchString .= '+AND+'.$searchWord;
        }
        $preparedSearchString = str_replace(' ', '+%2B', $preparedSearchString);

        return $preparedSearchString;
    }

    /**
     * Adds a query parameter for species_group.
     *
     * @param  string  $speciesGroup  either Plants, Bryophytes or Both
     * @return void
     */
    public function addSpeciesGroup(string $speciesGroup): void
    {
        $speciesGroup = ucfirst($speciesGroup);
        if ($speciesGroup === 'Plants') {
            $this->add('species_group:'.'Plants');
            $this->addNot('species_group:'.'Bryophytes');
        } elseif ($speciesGroup === 'Bryophytes') {
            $this->add('species_group:'.'Bryophytes');
        } else {
            $this->add('species_group:'.'Plants+OR+Bryophytes');
        }
    }

    public function addAxiophyteFilter()
    {
        $this->filterQueryParameters[] = $this->axiophyteFilter;

        return $this;
    }

    /**
     * List of extra parameters to be added to the query (in addition to data_resource_uid).
     *
     * @var string[] Array of strings
     */
    protected $extraQueryParameters = [];

    /**
     * Adds to the list of extra query parameters.
     *
     * @param  string  $extraQueryParameter  A single extra query parameter
     * @return $this
     */
    public function addExtraQueryParameter(string $extraQueryParameter)
    {
        $this->extraQueryParameters[] = $extraQueryParameter;

        return $this;
    }

    public function sortBy(string $sort)
    {
        $this->sort = $sort;
    }

    /**
     * Set the facts for a species name type.
     *
     * @param  string  $nameType  : either scientific or common
     * @return void
     */
    public function setSpeciesNameType(string $nameType)
    {
        if ($nameType === 'scientific') {
            $this->facets = 'names_and_lsid';
        }

        if ($nameType === 'common') {
            $this->facets = 'common_name_and_lsid';
        }
    }

    public function setFacetedSort(string $facetedSort)
    {
        $this->fsort = $facetedSort;
    }

    public function setDirection(string $direction)
    {
        $this->dir = $direction;
    }
}
