<?php

namespace App\Services;

/**
 * The response from the NBN API, including JSON response, status
 * and error message if one is required.
 */
class NbnApiResponse
{
    /**
     * The json response from the NBN API.
     *
     * @var object
     */
    public object $jsonResponse;
    /**
     * The status of the response from the NBN API.
     *
     * @var bool
     */
    public bool $status;
    /**
     * The error message (if one is raised) from calling
     * the NBN API.
     *
     * @var string
     */
    public ?string $message;

    public ?int $numberOfRecords;

    public function __construct()
    {
        $this->message = '';
    }

    //TODO: tighten return type - can be object or array
    public function getRecords($searchType)
    {
        //either return faceted results or occurences
        if ($searchType == NbnQueryBuilder::OCCURRENCES_SEARCH && isset($this->jsonResponse->facetResults[0])) {
            $this->numberOfRecords = count($this->jsonResponse->facetResults[0]->fieldResult);

            return $this->jsonResponse->facetResults[0]->fieldResult;
        }

        if ($searchType == NbnQueryBuilder::OCCURRENCES_SEARCH && isset($this->jsonResponse->occurrences)) {
            $this->numberOfRecords = $this->jsonResponse->totalRecords;

            return $this->jsonResponse->occurrences;
        }

        if ($searchType == NbnQueryBuilder::OCCURRENCE && isset($this->jsonResponse)) {
            $this->numberOfRecords = 1;

            return $this->jsonResponse;
        }

        if ($searchType == NbnQueryBuilder::AUTOCOMPLETE_SEARCH) {
            return $this->jsonResponse->autoCompleteList;
        }

        return [];
    }

    public function getNumberOfRecords(): int
    {
        return $this->numberOfRecords;
    }

    public function getNumberOfPages($pageSize): int
    {
        return ceil($this->numberOfRecords / $pageSize); //calculate total pages
    }

    public function getNumberOfPagesWithNumberOfRecords($pageSize, $numberOfRecords): int
    {
        return ceil($numberOfRecords / $pageSize); //calculate total pages
    }

    public function getSiteLocation(): array
    {
        // Get site location from first occurrence
        if (isset($this->jsonResponse->occurrences[0]->decimalLatitude)) {
            return [$this->jsonResponse->occurrences[0]->decimalLatitude, $this->jsonResponse->occurrences[0]->decimalLongitude];
        } else {
            // No location data - currently just doesn't show a site marker
            return [];
        }
    }
}
