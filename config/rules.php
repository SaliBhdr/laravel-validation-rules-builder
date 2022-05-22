<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rules Cache
    |--------------------------------------------------------------------------
    |
    | This value is defines the cache strategy. This value is used when the
    | package needs to define the cache path or status.
    |
    | path : The location of the rules cache file. remember to provide the valid permissions.
    |
    | enable : This option enables or disables cache in the whole application.
    |          You can also customize the cache config for each rule builder separately.
    */
    'cache' => [

        'path' => storage_path('framework/cache/rules'),

        'enable' => env('VALIDATION_RULE_CACHE_ENABLE', true),
    ],
];
