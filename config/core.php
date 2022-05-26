<?php

return [
    'siteName' => env('SITE_NAME', 'Site Not Configured!'),
    'dataResourceId' => env('DATA_RESOURCE_ID', 'Data Resource Not Configured!'),
    'region' => env('REGION', 'Region Not Configured!'),
    'defaultMapState' => env('DEFAULT_MAP_STATE', '52.6354,-2.71975,9'),
    'showSpeciesSearch' => env('SPECIES_SEARCH', false),
    'showSitesSearch' => env('SITES_SEARCH', false),
    'showSquaresSearch' => env('SQUARES_SEARCH', false),
    'caching' => env('CACHING', false),
    'resultsPerPage' => env('RESULTS_PER_PAGE', 10),
    'axiophyteFilter' => env('AXIOPHYTE_FILTER', 'species_list_uid:dr1940'),

];
