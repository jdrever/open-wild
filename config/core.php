<?php

return [
    'siteId' => env('SITE_ID', 'Site ID Configured!'),
    'siteName' => env('SITE_NAME', 'Site Name Not Configured!'),
    'dataResourceId' => env('DATA_RESOURCE_ID', false),
    'region' => env('REGION', false),
    'defaultMapState' => env('DEFAULT_MAP_STATE', '52.6354,-2.71975,9'),
    'startingLongitude' => env('STARTING_LONGITUDE'),
    'startingLatitude' => env('STARTING_LATITUDE'),
    'radius' => env('RADIUS'),
    'showSpeciesSearch' => env('SPECIES_SEARCH', false),
    'speciesNameExample' => env('SPECIES_NAME_EXAMPLE', false),
    'showSitesSearch' => env('SITES_SEARCH', false),
    'showSquaresSearch' => env('SQUARES_SEARCH', false),
    'caching' => env('CACHING', false),
    'resultsPerPage' => env('RESULTS_PER_PAGE', 10),
    'axiophyteFilter' => env('AXIOPHYTE_FILTER', false),
    'plantsOrBryophytesFilter' => env('PLANTS_BRYOPHYTES', false),
    'wormsFilter' => env('WORMS', false),
];
