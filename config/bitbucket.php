<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bitbucket API Base URL
    |--------------------------------------------------------------------------
    */
    'api_base_url' => env('BITBUCKET_API_BASE_URL', 'https://api.bitbucket.org/2.0'),

    /*
    |--------------------------------------------------------------------------
    | Pipeline Wait Interval (seconds)
    |--------------------------------------------------------------------------
    */
    'pipeline_wait_interval' => env('BITBUCKET_PIPELINE_WAIT_INTERVAL', 2),

];
