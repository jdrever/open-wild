<?php

namespace App\Models;

class OccurrenceResult extends BaseResult
{
    public string $scientificName;
    public string $commonName;
    public string $recordId;
    public string $recorders;
    public string $siteName;
    public string $gridReference;
    public string $gridReferenceWKT;
    public string $occurrenceDate;
    public string $occurrenceYear;
    public string $phylum;
}
