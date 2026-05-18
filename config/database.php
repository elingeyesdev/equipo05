<?php

use Illuminate\Support\Str;

/*
| Los seis modulos (inventario, incendios, rescate, logistica, seguimiento, cuadrillas)
| pueden usar un solo servidor PostgreSQL (database/unified_postgresql/) con un schema
| por modulo. Activa con DATABASE_UNIFIED_POSTGRES=true y variables UNIFIED_PG_*.
| El septimo espacio es la app principal (conexion default: sqlite o pgsql en .env).
*/
$dbUnifiedPostgres = filter_var(env('DATABASE_UNIFIED_POSTGRES', false), FILTER_VALIDATE_BOOL);

$unifiedModulePgsql = static function (string $schema): array {
    return [
        'driver' => 'pgsql',
        'url' => env('UNIFIED_PG_URL'),
        'host' => env('UNIFIED_PG_HOST', '127.0.0.1'),
        'port' => env('UNIFIED_PG_PORT', '5432'),
        'database' => env('UNIFIED_PG_DATABASE', 'equipo05_unificado'),
        'username' => env('UNIFIED_PG_USERNAME', 'postgres'),
        'password' => env('UNIFIED_PG_PASSWORD', ''),
        'charset' => env('UNIFIED_PG_CHARSET', 'utf8'),
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => $schema.',public',
        'sslmode' => env('UNIFIED_PG_SSLMODE', 'prefer'),
    ];
};

$sqliteModule = static function (string $urlKey, string $pathKey, string $defaultFilename): array {
    return [
        'driver' => 'sqlite',
        'url' => env($urlKey),
        'database' => env($pathKey, database_path($defaultFilename)),
        'prefix' => '',
        'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        'busy_timeout' => null,
        'journal_mode' => null,
        'synchronous' => null,
        'transaction_mode' => 'DEFERRED',
    ];
};

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],

        'core' => $dbUnifiedPostgres
            ? $unifiedModulePgsql('core')
            : [
                'driver' => 'sqlite',
                'url' => env('DB_URL'),
                'database' => env('DB_DATABASE', database_path('database.sqlite')),
                'prefix' => '',
                'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
                'busy_timeout' => null,
                'journal_mode' => null,
                'synchronous' => null,
                'transaction_mode' => 'DEFERRED',
            ],

        'transparencia' => $dbUnifiedPostgres
            ? $unifiedModulePgsql('transparencia')
            : [
                'driver' => 'sqlite',
                'url' => env('DB_URL'),
                'database' => env('DB_DATABASE', database_path('database.sqlite')),
                'prefix' => '',
                'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
                'busy_timeout' => null,
                'journal_mode' => null,
                'synchronous' => null,
                'transaction_mode' => 'DEFERRED',
            ],

        'inventario' => $dbUnifiedPostgres
            ? $unifiedModulePgsql('inventario')
            : $sqliteModule('INVENTARIO_DB_URL', 'INVENTARIO_DB_DATABASE', 'inventario.sqlite'),

        'incendios' => $dbUnifiedPostgres
            ? $unifiedModulePgsql('incendios')
            : $sqliteModule('INCENDIOS_DB_URL', 'INCENDIOS_DB_DATABASE', 'incendios.sqlite'),

        'rescate' => $dbUnifiedPostgres
            ? $unifiedModulePgsql('rescate')
            : $sqliteModule('RESCATE_DB_URL', 'RESCATE_DB_DATABASE', 'rescate.sqlite'),

        'logistica' => $dbUnifiedPostgres
            ? $unifiedModulePgsql('logistica')
            : $sqliteModule('LOGISTICA_DB_URL', 'LOGISTICA_DB_DATABASE', 'logistica.sqlite'),

        'seguimiento' => $dbUnifiedPostgres
            ? $unifiedModulePgsql('seguimiento')
            : $sqliteModule('SEGUIMIENTO_DB_URL', 'SEGUIMIENTO_DB_DATABASE', 'seguimiento.sqlite'),

        'cuadrillas' => $dbUnifiedPostgres
            ? $unifiedModulePgsql('cuadrillas')
            : $sqliteModule('CUADRILLAS_DB_URL', 'CUADRILLAS_DB_DATABASE', 'cuadrillas.sqlite'),

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

    ],

];
