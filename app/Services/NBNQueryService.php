<?php
namespace App\Services;

use App\Interfaces\QueryService;

class NBNQueryService implements QueryService
{
    public function getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter, $page)
    {

        $nbnQuery = new NbnQueryBuilder('/occurrences/search');
        echo(var_dump($nbnQuery));

        $speciesNameForSearch=$this->prepareSearchString($speciesName);
        if ($speciesNameType === "scientific")
        {
            $nbnQuery->add('taxon_name:' . $speciesNameForSearch);
            $nbnQuery->facets   = 'names_and_lsid';
            $nbnQuery->fsort = "index";
        }

        if ($speciesNameType === "common")
        {
            $nbnQuery->add('common_name:' . $speciesNameForSearch);
            $nbnQuery->facets   = 'common_name_and_lsid';
            $nbnQuery->fsort = "index";
        }

        if ($axiophyteFilter === "true")
        {
            $nbnQuery->addAxiophyteFilter();
        }

        $speciesGroup = ucfirst($speciesGroup);
        if ($speciesGroup === "Plants")
        {
            $nbnQuery->add('species_group:' . "Plants");
            $nbnQuery->addNot('species_group:' . "Bryophytes");
        }
        else if ($speciesGroup=== "Bryophytes")
        {
            $nbnQuery->add('species_group:' . "Bryophytes");
        }
        else
        {
            $nbnQuery->add('species_group:' . 'Plants+OR+Bryophytes');
        }

        //first get number of records from unpaged query
        //TODO: should this be within the NbnQueryBuilder class?
        $nbnQueryUrl            = $nbnQuery->getUnpagedQueryString();
        $nbnQueryResponse    = $this->callNbnApi($nbnQueryUrl);
        $totalRecords 		 = 0;
        if (isset($nbnQueryResponse->jsonResponse->facetResults[0]))
        {
            $totalRecords = count($nbnQueryResponse->jsonResponse->facetResults[0]->fieldResult);
        }

        // then get paged results
        //TODO: number of records in paged result should be a constant.
        $nbnQuery->flimit   = config('RESULTS_PER_PAGE');
        $nbnQueryUrl             = $nbnQuery->getPagingQueryStringWithFacetStart($page);
        $nbnQueryResponse = $this->callNbnApi($nbnQueryUrl);

        //echo(var_dump($nbnQueryResponse->jsonResponse->occurrences));



        //TODO: some proper fall back arrangement
        $speciesQueryResult  = new NbnQueryResult();

        //TODO: this is probably repeated, should be in NbnQueryBuilder class?
        //TODO: don't think we need nbnQueryResponse and nbnQueryResult?
        if ($nbnQueryResponse->status === 'OK')
        {
            if (isset($nbnQueryResponse->jsonResponse->facetResults[0]))
            {
                $speciesQueryResult->records = 	$nbnQueryResponse->jsonResponse->facetResults[0]->fieldResult;;

            }
            else
            {
                $speciesQueryResult->records = [];
            }
            $speciesQueryResult->downloadLink = $nbnQuery->getDownloadQueryString();
        }
        $speciesQueryResult->status   	  = $nbnQueryResponse->status;
        $speciesQueryResult->message  	  = $nbnQueryResponse->message;
        $speciesQueryResult->queryUrl     = $nbnQueryUrl;
        $speciesQueryResult->totalRecords = $totalRecords;
        return $speciesQueryResult;

    }
	public function getSingleSpeciesRecordsForDataset($speciesName, $page) { return false; }
	public function getSingleOccurenceRecord($uuid) { return false; }

	public function getSiteListForDataset($siteName, $page){ return false; }
	public function getSpeciesListForSite($siteName, $speciesNameType, $speciesGroup, $axiophyteFilter, $page){ return false; }
	public function getSingleSpeciesRecordsForSite($site_name, $speciesName,$page){ return false; }

	public function getSpeciesListForSquare($gridSquare, $speciesGroup, $speciesNameType, $axiophyteFilter, $page){ return false; }
	public function getSingleSpeciesRecordsForSquare($gridSquare, $speciesName, $page){ return false; }

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


	private function callNbnApi($queryUrl)
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


/**
 * Facade for the NBN records end point
 *
 * See the NBN Atlas Query Primer for details about using the API
 * https://docs.google.com/document/d/1FiVasGGZ3kRPnu5347GPAef7Tr5LvvghCS6x82xnfu4/edit
 *
 * @package Libraries
 * @author  Careful Digital <hello@careful.digital>
 * @license https://www.shropshirebotany.org.uk/ Shropshire Botanical Society
 */

class NbnQueryBuilder
{
	const BASE_URL = 'https://records-ws.nbnatlas.org/';

	/**
	 * The unique data resource id code
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
	 * @var string $dataResourceUid
	 */
	private $dataResourceUid = 'dr782';

	/**
	 * The filter for axiophytes, as supplied by Sophie at NBN
	 *
	 * @var string $axiophyteFilter
	 */
	private $axiophyteFilter='species_list_uid:dr1940';

	/**
	 * TODO: Describe what the $path member variable is for
	 *
	 * @var string $path
	 */
	private $path = '';

	/**
	 * TODO: Describe what the $facets member variable is for
	 *
	 * @var string $facets
	 */
	public $facets;

	/**
	 * TODO: Describe what the $fsort member variable is for
	 *
	 * @var string $fsort
	 */
	public $fsort;

	/**
	 * Sets the number of paged records returned by each NBN query
	 *
	 * @var integer $pageSize
	 */
	public $pageSize;

	/**
	 * TODO: Describe what the $sort member variable is for
	 *
	 * @var string $sort
	 */
	public $sort;

	/**
	 * The direction of the sort
	 *
	 * @var string $dir
	 */
	public $dir = 'asc';

	/**
	 * TODO: Describe what the $flimit variable is for
	 *
	 * @var int $flimit
	 */
	public $flimit;

	/**
	 * Constructor
	 *
	 * Accepts a path fragment which indicates the NBN Atlas API search type to
	 * perform. Defaults to Occurrence search: https://api.nbnatlas.org/#ws3
	 *
	 * See https://api.nbnatlas.org/ for others.
	 *
	 * @param string $path NBN Atlas API search type
	 */
	public function __construct(string $path = 'occurrences/search')
	{
		$this->path = $path;
        $this->pageSize=env('RESULTS_PER_PAGE', false);
	}

	/**
	 * Return the base search query string
	 *
	 * @param string $url The full url to query
	 *
	 * @return string
	 */
	private function getQueryString(string $url)
	{
		$queryString  = $url . '?';
		$queryParameters = array_merge(array('data_resource_uid:' . $this->dataResourceUid), $this->extraQueryParameters);
		$fqAndParameters = implode('%20AND%20', $this->filterQueryParameters);
		$fqNotParameters = '';
		if (count($this->filterNotQueryParameters)>0)
		{
			$fqNotParameters = '%20AND%20NOT%20' . implode('%20AND%20NOT%20', $this->filterNotQueryParameters);
		}
		$queryString .= 'q=' . implode('%20AND%20', $queryParameters) . '&';
		$queryString .= 'fq=' . $fqAndParameters . $fqNotParameters . '&';
		$queryString .= 'facets=' . $this->facets . '&';
		$queryString .= 'sort=' . $this->sort . '&';
		$queryString .= 'fsort=' . $this->fsort . '&';
		$queryString .= 'dir=' . $this->dir . '&';

		if (isset($this->flimit))
		{
			$queryString .= 'flimit=' . $this->flimit . '&';
		}

		return $queryString;
	}


	/**
	 * Return the url for single record download querty
	 *
	 * @param string $url The full url to query
	 *
	 * @return string
	 */
	private function getSingleRecordDownloadUrl(string $url, string $occurrenceId)
	{
		$queryString  = $url . '?';
		$queryString .= 'fq=occurrence_id:' .$occurrenceId . '&';


		return $queryString;
	}


	/**
	 * Return the base url and path (really only used for getting a single
	 * occurence record)
	 *
	 * @return string
	 */

	public function url()
	{
		return $this::BASE_URL . $this->path;
	}

	/**
	 * Return the query string without paging
	 * Used to determine total number of records for query
	 * without paging
	 *
	 * @return string
	 */
	public function getUnpagedQueryString()
	{
		$queryString  = $this->getQueryString($this::BASE_URL . $this->path);
		$queryString .= 'pageSize=0&flimit=-1';
		return $queryString;
	}


	/**
	 * Return the query string for paging
	 *
	 * @return string
	 */
	public function getPagingQueryString()
	{
		$queryString  = $this->getQueryString($this::BASE_URL . $this->path);
		$queryString .= 'pageSize=' . $this->pageSize;
		return $queryString;
	}

	public function getPagingQueryStringWithStart($start)
	{

		$pagingQuery = $this->getPagingQueryString();
		return $pagingQuery .= "&start=" . (($start - 1) * $this->pageSize);
	}

	public function getPagingQueryStringWithFacetStart($start)
	{
        echo("=". $this->pageSize);
		$pagingQuery = $this->getPagingQueryString();
		return $pagingQuery .= "&facet.offset=" . (($start - 1) * $this->pageSize);
	}


	/**
	 * Return the query string for downloading the data
	 *
	 * @return string
	 */
	public function getDownloadQueryString()
	{
		$queryString  = $this->getQueryString($this::BASE_URL . 'occurrences/index/download');
		$queryString .= '&reasonTypeId=11&fileType=csv';
		return $queryString;
	}

	/**
	 * Return the query string for downloading the data
	 *
	 * @return string
	 */
	public function getSingleRecordDownloadQueryString($occurrenceId)
	{
		$queryString  = $this->getSingleRecordDownloadUrl($this::BASE_URL . 'occurrences/index/download',$occurrenceId);
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
	 * Adds to the internal list of filter query parameters
	 *
	 * A list of available index fields can be found at
	 * https://species-ws.nbnatlas.org/admin/indexFields
	 *
	 * @param string $filterQueryParameter A single filter query parameter
	 *
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
	 * Adds to the internal list of filter NOT query parameters
	 *
	 * A list of available index fields can be found at
	 * https://species-ws.nbnatlas.org/admin/indexFields
	 *
	 * @param string $filterNotQueryParameter A single filter query parameter
	 *
	 * @return $this
	 */
	public function addNot(string $filterNotQueryParameter)
	{
		$this->filterNotQueryParameters[] = $filterNotQueryParameter;
		return $this;
	}


	public function addAxiophyteFilter()
	{
		$this->filterQueryParameters[] = $this->axiophyteFilter;
		return $this;
	}

	/**
	 * List of extra parameters to be added to the query (in addition to data_resource_uid)
	 *
	 * @var string[] Array of strings
	 */
	protected $extraQueryParameters = [];

	/**
	 * Adds to the list of extra query parameters
	 *
	 * @param string $extraQueryParameter A single extra query parameter
	 *
	 * @return $this
	 */
	public function addExtraQueryParameter(string $extraQueryParameter)
	{
		$this->extraQueryParameters[] = $extraQueryParameter;
		return $this;
	}
}


/**
 * The response from the NBN API, including JSON response, status
 * and error message if one is required
 */
class NbnApiResponse
{
	/**
	 * The json response from the NBN API
	 *
	 * @var string
	 */
	public $jsonResponse;
	/**
	 * The status of the response from the NBN API
	 * Either OK or ERROR
	 *
	 * @var string
	 */
	public $status;
	/**
	 * The error message (if one is raised) from calling
	 * the NBN API
	 *
	 * @var string
	 */
	public $message;
}

class NbnQueryResult
{
	public $records;
	public $sites;
	public $downloadLink;
	public $totalRecords;
	public $queryUrl;
	public $status;
	public $message;

	public function getTotalPages()
	{
		$limit = 10; //per page
		return ceil($this->totalRecords / $limit); //calculate total pages
	}
}
?>



