<?php
declare(strict_types=1);

if (!function_exists('nf_database_config_from_app_config')) {
    /**
     * Normalize the database configuration extracted from the generated application config.
     *
     * @param array<string,mixed>|null $config
     * @return array{host:string,port:int,name:string,user:string,password:string,charset:string}|null
     */
    function nf_database_config_from_app_config(?array $config): ?array
    {
        if ($config === null) {
            return null;
        }

        $database = $config['database'] ?? null;
        if (!is_array($database)) {
            return null;
        }

        $host = is_string($database['host'] ?? null) ? trim((string) $database['host']) : 'localhost';
        if ($host === '') {
            $host = 'localhost';
        }

        $port = (int) ($database['port'] ?? 3306);
        if ($port <= 0) {
            $port = 3306;
        }

        $name = is_string($database['name'] ?? null) ? trim($database['name']) : '';
        $user = is_string($database['user'] ?? null) ? trim($database['user']) : '';
        $password = is_string($database['password'] ?? null) ? $database['password'] : '';

        $charset = is_string($database['charset'] ?? null) ? strtolower(trim($database['charset'])) : '';
        if ($charset === '' || !preg_match('/^[a-z0-9_\-]+$/', $charset)) {
            $charset = 'utf8mb4';
        }

        return [
            'host' => $host,
            'port' => $port,
            'name' => $name,
            'user' => $user,
            'password' => $password,
            'charset' => $charset,
        ];
    }
}

if (!function_exists('nf_build_pdo_dsn')) {
    /**
     * @param array{host:string,port:int,name:string,charset:string} $config
     */
    function nf_build_pdo_dsn(array $config): string
    {
        $parts = [
            'host=' . $config['host'],
            'port=' . $config['port'],
            'dbname=' . $config['name'],
            'charset=' . $config['charset'],
        ];

        return 'mysql:' . implode(';', $parts);
    }
}

if (!function_exists('nf_create_pdo_connection')) {
    /**
     * @param array{host:string,port:int,name:string,user:string,password:string,charset:string} $config
     * @throws PDOException
     */
    function nf_create_pdo_connection(array $config): \PDO
    {
        $dsn = nf_build_pdo_dsn($config);

        $pdo = new \PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return $pdo;
    }
}

if (!function_exists('nf_initialize_database_connection')) {
    /**
     * Bootstrap a PDO connection based on the generated configuration.
     *
     * @param array<string,mixed>|null $config
     * @return array{pdo:PDO|null,status:string,message:?string,config:?array{host:string,port:int,name:string,user:string,password:string,charset:string}}
     */
    function nf_initialize_database_connection(?array $config): array
    {
        if (!class_exists(\PDO::class) || !extension_loaded('pdo_mysql')) {
            return [
                'pdo' => null,
                'status' => 'missing_extension',
                'message' => 'The pdo_mysql extension is required to establish a database connection.',
                'config' => null,
            ];
        }

        $databaseConfig = nf_database_config_from_app_config($config);
        if ($databaseConfig === null) {
            return [
                'pdo' => null,
                'status' => 'missing_configuration',
                'message' => null,
                'config' => null,
            ];
        }

        if ($databaseConfig['name'] === '' || $databaseConfig['user'] === '') {
            return [
                'pdo' => null,
                'status' => 'incomplete_configuration',
                'message' => 'Database name and user must be provided before a connection can be opened.',
                'config' => $databaseConfig,
            ];
        }

        try {
            $pdo = nf_create_pdo_connection($databaseConfig);
        } catch (\PDOException $exception) {
            return [
                'pdo' => null,
                'status' => 'connection_failed',
                'message' => $exception->getMessage(),
                'config' => $databaseConfig,
            ];
        }

        return [
            'pdo' => $pdo,
            'status' => 'connected',
            'message' => null,
            'config' => $databaseConfig,
        ];
    }
}
