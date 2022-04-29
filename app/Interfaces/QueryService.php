<?php

namespace App\Interfaces;

use App\Models\QueryResult;
use App\Models\OccurrenceResult;
interface QueryService
{
    public function getSpeciesListForDataset(string $speciesName, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage) : QueryResult;

    public function getSingleSpeciesRecordsForDataset(string $speciesName, int $currentPage) : QueryResult;

    public function getSingleOccurenceRecord(string $occurenceId) : OccurrenceResult;

    public function getSiteListForDataset(string $siteName, int $currentPage) : QueryResult;

    public function getSpeciesListForSite($siteName, $speciesNameType, $speciesGroup, $axiophyteFilter, $currentPage);

    public function getSingleSpeciesRecordsForSite($siteName, $speciesName, $currentPage);

    public function getSpeciesListForSquare($gridSquare, $speciesGroup, $speciesNameType, $axiophyteFilter, $currentPage);

    public function getSingleSpeciesRecordsForSquare($gridSquare, $speciesName, $currentPage);
}
