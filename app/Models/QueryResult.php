<?php

namespace App\Models;

class QueryResult
{
    //TODO: specifiy type (can be object or array)
	public $records;
	public $sites;
	public string $downloadLink;
    public int $numberOfRecords;
    public int $numberOfPages;
	public string $queryUrl;
	public string $status;
	public ?string $message;
}

?>
