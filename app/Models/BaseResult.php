<?php

namespace App\Models;

abstract class BaseResult
{
    public string $queryUrl;
    public bool $status;
    public ?string $message;
}
