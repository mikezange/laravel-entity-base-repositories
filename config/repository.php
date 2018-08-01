<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repositories to bind
    |--------------------------------------------------------------------------
    |
    |   Example:
    |   [
    |     'contract' => App\Repositories\UserRepository::class,
    |     'entity' => \App\Entities\User::class,
    |     'eloquent_repository' => App\Repositories\Eloquent\EloquentUserRepository::class,
    |     'cache_decorator' => App\Repositories\Cache\CacheUserDecorator::class,
    |   ]
    |
    */

    'repositories' => [],

    'cache' => [

        /*
        |--------------------------------------------------------------------------
        | Enable caching:
        | This will use the caching method specified in config/cache.php
        |--------------------------------------------------------------------------
        */

        'enabled' => true,

        /*
        |--------------------------------------------------------------------------
        | Cache time: How long should the cache hold data
        |--------------------------------------------------------------------------
        */

        'time' => 30
    ]
];