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
    | enable : This option enables or disables cache. In order to enable caching, you have to call cache()
    |          method on each RulesBuilder instance . With this option, you can control the
    |          effect of the cache() method on the entire application.
    */
    'cache' => [

        'path' => storage_path('framework/cache/rules'),

        'enable' => env('VALIDATION_RULE_CACHE_ENABLE', true),
    ],
];
