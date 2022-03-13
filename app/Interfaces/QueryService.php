<?php

namespace App\Interfaces;

interface QueryService
{
	public function getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter, $page);
	public function getSingleSpeciesRecordsForDataset($speciesName, $page);
	public function getSingleOccurenceRecord($occurenceId);

	public function getSiteListForDataset($siteName, $page);
	public function getSpeciesListForSite($siteName, $speciesNameType, $speciesGroup, $axiophyteFilter, $page);
	public function getSingleSpeciesRecordsForSite($siteName, $speciesName,$page);

	public function getSpeciesListForSquare($gridSquare, $speciesGroup, $speciesNameType, $axiophyteFilter, $page);
	public function getSingleSpeciesRecordsForSquare($gridSquare, $speciesName, $page);
}

?>
