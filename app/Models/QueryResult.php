<?php

namespace App\Models;

class QueryResult
{
	public $records;
	public $sites;
	public $downloadLink;
    public $numberOfRecords;
	public $totalNumberOfRecords;
	public $queryUrl;
	public $status;
	public $message;

	public function getTotalPages()
	{
		$limit = 10; //per page
		return ceil($this->totalNumberOfRecords / $limit); //calculate total pages
	}
}

?>
