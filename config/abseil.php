<?php

return [
    /*
    |--------------------------------------------------------------------------
    | UUIDs
    |--------------------------------------------------------------------------
    |
    | By default Abseil assumes that all your models use UUIDs as primary
    | keys. Firstly, because they're awesome, and secondly, because they
    | work really well with JSON:API
    |
    */

    'use_uuids' => true,

    /*
    |--------------------------------------------------------------------------
    | Resource Namespace
    |--------------------------------------------------------------------------
    |
    | Set this to the namespace where your resources are available from.
    | Abseil then uses this namespace to make some educated guesses
    | about your models from the names of your resources.
    |
    */

    'resource_namespace' => 'App\\Http\\Resources\\',

    /*
    |--------------------------------------------------------------------------
    | Morph Map Location
    |--------------------------------------------------------------------------
    |
    | Set this to the location where your morph map is available from.
    | Abseil expects there to be a public constant named MORPH_MAP
    |s
    */

    'morph_map_location' => 'App\\Providers\\AppServiceProvider',
];
