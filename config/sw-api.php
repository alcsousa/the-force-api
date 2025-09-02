<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Star Wars API
    |--------------------------------------------------------------------------
    |
    | This value is the base URL for the Star Wars API (SWAPI).
    |
    */

    'base_url' => env('SW_API_BASE_URL', 'https://www.swapi.tech/api/'),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | This value set the default time-to-live for cached API responses.
    |
    */

    'cache_ttl' => env('SW_API_CACHE_TTL_IN_MINUTES', 60),

];
