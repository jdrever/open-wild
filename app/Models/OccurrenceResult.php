<?php

namespace App\Models;

class OccurrenceResult extends BaseResult
{
    public string $scientificName;
    public string $commonName;
    public string $recordId;
    public string $recorders;
    public string $siteName;
    public string $locality;
    public string $gridReference;
    public string $gridReferenceWKT;
    public string $occurrenceDate;
    public string $occurrenceYear;
    public string $species;
    public string $genus;
    public string $family;
    public string $order;
    public string $class;
    public string $phylum;
    public string $kingdom;
    public string $basisOfRecord;
    public string $license;
    public string $verificationStatus;
    public string $remarks;
    public string $dataProvider;
}
