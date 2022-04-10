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
	public string $status;
	/**
	 * The error message (if one is raised) from calling
	 * the NBN API
	 *
	 * @var string
	 */
	public ?string $message;

    public ?int $numberOfRecords;

    public function __construct()
	{
		$this->message = "";
	}

    //TODO: tighten return type - can be object or array
    public function getRecords($searchType)
    {
        //either return faceted results or occurences
        if ($searchType==NBNQueryBuilder::OCCURENCES_SEARCH&&isset($this->jsonResponse->facetResults[0]))
        {
            $this->numberOfRecords=count($this->jsonResponse->facetResults[0]->fieldResult);
            return $this->jsonResponse->facetResults[0]->fieldResult;
        }

        if ($searchType==NBNQueryBuilder::OCCURENCES_SEARCH&&isset($this->jsonResponse->occurrences))
        {
            $this->numberOfRecords=$this->jsonResponse->totalRecords;
            return $this->jsonResponse->occurrences;
        }

        if ($searchType==NBNQueryBuilder::OCCURENCE&&isset($this->jsonResponse))
        {
            $this->numberOfRecords=count($this->jsonResponse);
            return $this->jsonResponse;
        }

        return [];
    }

    public function getNumberOfRecords() : int
    {
        return $this->numberOfRecords;
    }


    public function getNumberOfPages($pageSize) : int
    {
		return ceil($this->numberOfRecords / $pageSize); //calculate total pages
    }

    public function getNumberOfPagesWithNumberOfRecords($pageSize, $numberOfRecords) : int
    {
		return ceil($numberOfRecords / $pageSize); //calculate total pages
    }
}
