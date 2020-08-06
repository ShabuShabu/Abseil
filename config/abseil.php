<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auth Middleware
    |--------------------------------------------------------------------------
    |
    | By default Abseil assumes that you're using Laravel Passport. It uses
    | this middleware to figure out when exactly to allow query filters for
    | route model binding.
    |
    */

    'auth_middleware' => 'auth:api',

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

    'resource_namespace' => 'App\\Http\\Resources',

    /*
    |--------------------------------------------------------------------------
    | Morph Map Location
    |--------------------------------------------------------------------------
    |
    | Set this to the location where your morph map is available from.
    | Abseil expects there to be a public constant named MORPH_MAP
    |
    */

    'morph_map_location' => 'App\\Providers\\AppServiceProvider',

    /*
   |--------------------------------------------------------------------------
   | Policies
   |--------------------------------------------------------------------------
   |
   | This section configures the policies lookup for you. It is based on the
   | following pattern: {namespace}\{className}{suffix}. With the default
   | values and for a User model you would get the following
   | location: App\Policies\UserPolicy
   |
   */

    'policies' => [
        'disable'   => false,
        'namespace' => 'App\\Policies',
        'suffix'    => 'Policy',
    ],
];
