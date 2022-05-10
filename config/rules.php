<?php

return [
    'cache' => [
        'path' => storage_path('framework/cache/rules'),

        'disabled' => env('VALIDATION_RULE_CACHE_IS_DISABLED', false),
    ],
];
