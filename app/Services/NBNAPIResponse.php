<?php
namespace App\Services;
/**
 * The response from the NBN API, including JSON response, status
 * and error message if one is required
 */
class NBNApiResponse
{
	/**
	 * The json response from the NBN API
	 *
	 * @var object
	 */
	public object $jsonResponse;
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

    public function getRecords($searchType)
    {
        //either return faceted results or occurences
        if ($searchType==NBNQueryBuilder::OCCURENCES_SEARCH&&isset($this->jsonResponse->facetResults[0]))
            return $this->jsonResponse->facetResults[0]->fieldResult;

        if ($searchType==NBNQueryBuilder::OCCURENCES_SEARCH&&isset($this->jsonResponse->occurrences))
            return $this->jsonResponse->occurrences;

        if ($searchType==NBNQueryBuilder::OCCURENCE&&isset($this->jsonResponse))
            return $this->jsonResponse;

        return [];
    }

    public function getNumberOfRecords($searchType) : int
    {
        //if a faceted query, return number of facet results
        //otherwise just return number of records
        if ($searchType==NBNQueryBuilder::OCCURENCES_SEARCH&&isset($this->jsonResponse->facetResults[0]))
            return count($this->jsonResponse->facetResults[0]->fieldResult);
        if ($searchType==NBNQueryBuilder::OCCURENCES_SEARCH&&isset($this->jsonResponse->totalRecord))
            return count($this->jsonResponse->totalRecord);
        return 0;
    }
}
