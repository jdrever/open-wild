<?php

namespace App\Interfaces;

interface QueryService
{
	public function getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter, $currentPage);
	public function getSingleSpeciesRecordsForDataset($speciesName, $currentPage);
	public function getSingleOccurenceRecord($occurenceId);

	public function getSiteListForDataset($siteName, $currentPage);
	public function getSpeciesListForSite($siteName, $speciesNameType, $speciesGroup, $axiophyteFilter, $currentPage);
	public function getSingleSpeciesRecordsForSite($siteName, $speciesName,$currentPage);

	public function getSpeciesListForSquare($gridSquare, $speciesGroup, $speciesNameType, $axiophyteFilter, $currentPage);
	public function getSingleSpeciesRecordsForSquare($gridSquare, $speciesName, $currentPage);
}

?>
