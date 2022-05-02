<?php

namespace App\Interfaces;

use App\Models\OccurrenceResult;
use App\Models\QueryResult;

interface QueryService
{
    public function getSpeciesListForDataset(string $speciesName, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage): QueryResult;

    public function getSingleSpeciesRecordsForDataset(string $speciesName, int $currentPage): QueryResult;

    public function getSingleOccurenceRecord(string $occurenceId): OccurrenceResult;

    public function getSiteListForDataset(string $siteName, int $currentPage): QueryResult;

    public function getSpeciesListForSite(string $siteName, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage) : QueryResult;

    public function getSingleSpeciesRecordsForSite($siteName, $speciesName, $currentPage);

    public function getSpeciesListForSquare(string $gridSquare, string $speciesNameType, string $speciesGroup, string $axiophyteFilter, int $currentPage): QueryResult;

    public function getSingleSpeciesRecordsForSquare(string $gridSquare, string $speciesName, int $currentPage): QueryResult;
}
