<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Optimization Settings
    |--------------------------------------------------------------------------
    |
    | This file contains performance optimization settings for ButcherPro.
    | These settings help improve the application's speed and efficiency.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Analytics Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache duration for analytics data in seconds.
    | Recommended: 900 seconds (15 minutes) for production
    |
    */
    'analytics_cache_ttl' => env('ANALYTICS_CACHE_TTL', 900),

    /*
    |--------------------------------------------------------------------------
    | Query Cache Settings
    |--------------------------------------------------------------------------
    |
    | Enable query result caching for frequently accessed data.
    |
    */
    'query_cache_enabled' => env('QUERY_CACHE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Slow Query Monitoring
    |--------------------------------------------------------------------------
    |
    | Threshold in milliseconds for logging slow queries.
    | Queries exceeding this threshold will be logged.
    |
    */
    'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 1000),

    /*
    |--------------------------------------------------------------------------
    | Database Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for database query optimization.
    |
    */
    'database' => [
        'enable_query_logging' => env('DB_QUERY_LOGGING', false),
        'connection_timeout' => env('DB_CONNECTION_TIMEOUT', 30),
        'max_connections' => env('DB_MAX_CONNECTIONS', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Optimization
    |--------------------------------------------------------------------------
    |
    | Cache-related performance settings.
    |
    */
    'cache' => [
        'enable_compression' => env('CACHE_COMPRESSION', true),
        'default_ttl' => env('CACHE_DEFAULT_TTL', 3600),
        'prefix' => env('CACHE_PREFIX', 'butcherpro'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for frontend performance optimization.
    |
    */
    'frontend' => [
        'enable_asset_minification' => env('ASSET_MINIFICATION', true),
        'enable_browser_caching' => env('BROWSER_CACHING', true),
        'cdn_url' => env('CDN_URL', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for memory usage optimization.
    |
    */
    'memory' => [
        'limit' => env('MEMORY_LIMIT', '1024M'),
        'enable_garbage_collection' => env('GC_ENABLED', true),
    ],
];
