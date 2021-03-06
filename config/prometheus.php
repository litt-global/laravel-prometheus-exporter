<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Namespace
    |--------------------------------------------------------------------------
    |
    | The namespace to use as a prefix for all metrics.
    |
    | This will typically be the name of your project, eg: 'search'.
    |
    */

    'namespace' => env('PROMETHEUS_NAMESPACE', 'app'),

    /*
    |--------------------------------------------------------------------------
    | Metrics Route Enabled?
    |--------------------------------------------------------------------------
    |
    | If enabled, a /metrics route will be registered to export prometheus
    | metrics.
    |
    */

    'metrics_route_enabled' => (boolean)env('PROMETHEUS_METRICS_ROUTE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Metrics Route Path
    |--------------------------------------------------------------------------
    |
    | The path at which prometheus metrics are exported.
    |
    | This is only applicable if metrics_route_enabled is set to true.
    |
    */

    'metrics_route_path' => env('PROMETHEUS_METRICS_ROUTE_PATH', 'metrics'),

    /*
    |--------------------------------------------------------------------------
    | Storage Adapter
    |--------------------------------------------------------------------------
    |
    | The storage adapter to use.
    |
    | Supported: "memory", "redis", "apc"
    |
    */

    'storage_adapter' => env('PROMETHEUS_STORAGE_ADAPTER', 'memory'),

    /*
    |--------------------------------------------------------------------------
    | Storage Adapters
    |--------------------------------------------------------------------------
    |
    | The storage adapter configs.
    |
    */

    'storage_adapters' => [

        'redis' => [
            'host' => env('PROMETHEUS_REDIS_HOST', env('REDIS_HOST', 'localhost')),
            'port' => (int)env('PROMETHEUS_REDIS_PORT', env('REDIS_PORT',6379)),
            'password' => env('PROMETHEUS_REDIS_PASSWORD', env('REDIS_PASSWORD', null)),
            'database' => (int)env('PROMETHEUS_REDIS_DB', env('REDIS_DB', 0)),
            'timeout' => (float)env('PROMETHEUS_REDIS_TIMEOUT', 0.1),
            'read_timeout' => (int)env('PROMETHEUS_REDIS_READ_TIMEOUT', 10),
            'persistent_connections' => (boolean)env('PROMETHEUS_REDIS_PERSISTENT_CONNECTIONS', false),
            'prefix' => env('PROMETHEUS_REDIS_PREFIX', 'PROMETHEUS_'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Collect full SQL query
    |--------------------------------------------------------------------------
    |
    | Indicates whether we should collect the full SQL query or not.
    |
    */

    'collect_full_sql_query' => (boolean)env('PROMETHEUS_COLLECT_FULL_SQL_QUERY', false),

    /*
    |--------------------------------------------------------------------------
    | Collectors
    |--------------------------------------------------------------------------
    |
    | The collectors specified here will be auto-registered in the exporter.
    |
    */

    'collectors' => [
        // \Your\ExporterClass::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Extra Labels
    |--------------------------------------------------------------------------
    |
    | Extra labels to stamp metrics with.
    |
    */

    'extra_labels' => array_reduce(explode(',', env('PROMETHEUS_EXTRA_LABELS', '')), function($carry, $item){
        if (strpos($item, ':') !== FALSE) {
            $kv = explode(':', $item);
            $carry[$kv[0]] = $kv[1];
        }
        return $carry;
    }, []),
];
