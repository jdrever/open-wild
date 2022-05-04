<?php

namespace App\Models;

class AutocompleteResult
{
    //TODO: specifiy type (can be object or array)
    public $records;
    public bool $status;
    public ?string $message;
    public string $queryUrl;
}
