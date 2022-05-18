<?php

namespace App\Models;

class QueryResult
{
    public iterable $records;
    public $sites;
    public ?array $siteLocation;
    public string $downloadLink;
    public int $numberOfRecords;
    public int $numberOfPages;
    public string $queryUrl;
    public bool $status;
    public ?string $message;
}
