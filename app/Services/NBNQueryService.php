<?php
namespace App\Services;

use App\Interfaces\QueryService;

class NBNQueryService implements QueryService
{
    public function getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter, $page) { return false; }
	public function getSingleSpeciesRecordsForDataset($speciesName, $page) { return false; }
	public function getSingleOccurenceRecord($uuid) { return false; }

	public function getSiteListForDataset($siteName, $page){ return false; }
	public function getSpeciesListForSite($siteName, $speciesNameType, $speciesGroup, $axiophyteFilter, $page){ return false; }
	public function getSingleSpeciesRecordsForSite($site_name, $speciesName,$page){ return false; }

	public function getSpeciesListForSquare($gridSquare, $speciesGroup, $speciesNameType, $axiophyteFilter, $page){ return false; }
	public function getSingleSpeciesRecordsForSquare($gridSquare, $speciesName, $page){ return false; }
}

?>
