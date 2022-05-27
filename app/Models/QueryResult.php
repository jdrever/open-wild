<?php

namespace App\Models;

class QueryResult extends BaseResult
{
    public iterable $records;
    public $sites;
    public ?array $siteLocation;
    public string $downloadLink;
    public int $numberOfRecords;
    public int $numberOfPages;
    public ?string $dotMapLink;
}
