<?php
require_once __DIR__ . '/../../setup/src/Config/ConfigBuilder.php';

use Setup\Config\ConfigBuilder;

function assertSame($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new Exception($message . ' (expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . ')');
    }
}

function testBuildsNetworkConfiguration(): void
{
    $builder = new ConfigBuilder();
    $timestamp = new DateTimeImmutable('2024-01-01T00:00:00Z');

    $environment = [
        'site_name' => 'Example Network',
        'base_url' => 'https://example.com/community',
        'force_https' => false,
    ];

    $database = [
        'host' => 'db.internal',
        'port' => 3306,
        'name' => 'ning',
        'user' => 'ning',
        'password' => 'secret',
        'driver' => 'mariadb',
        'server_version' => '10.11.5-MariaDB',
    ];

    $superAdmin = [
        'email' => 'owner@example.com',
        'password_hash' => 'hashed-super-admin',
    ];

    $networkAdmin = [
        'email' => 'admin@example.com',
        'password_hash' => 'hashed-network-admin',
    ];

    $network = [
        'slug' => 'community',
        'name' => 'Community Hub',
        'base_domain' => 'example.com',
        'use_subdomain' => false,
        'base_path' => '/community',
        'aliases' => ['community.example.net'],
        'auto_updates' => true,
        'allow_super_admin_updates' => true,
    ];

    $automation = [
        'auto_updates' => true,
        'updates_branch' => 'stable',
        'allow_network_override' => false,
        'scheduler_mode' => 'page_load',
        'scheduler_interval' => 10,
    ];

    $detected = [
        'php_version' => '8.4.0',
        'extensions' => ['pdo', 'pdo_mysql'],
        'https_detected' => true,
        'host' => 'example.com',
        'port' => 443,
        'base_url' => 'https://example.com/community',
        'base_path' => '/community',
    ];

    $config = $builder->build(
        $environment,
        $database,
        $superAdmin,
        $networkAdmin,
        $network,
        $automation,
        $detected,
        $timestamp
    );

    assertSame('Example Network', $config['app']['name'], 'App name should be retained');
    assertSame('https://example.com/community', $config['networks']['community']['primary_url'], 'Primary URL should include the base path');
    assertSame(['community.example.net'], $config['networks']['community']['aliases'], 'Aliases should be preserved');
    assertSame('2024-01-01T00:00:00+00:00', $config['app']['created_at'], 'Created timestamp should reflect the provided clock');
    assertSame('mysql:host=db.internal;port=3306;dbname=ning;charset=utf8mb4', $config['database']['dsn'], 'DSN should include host, port, and charset');
    assertSame('mariadb', $config['database']['driver'], 'Database driver should reflect the provided metadata');
    assertSame('10.11.5-MariaDB', $config['database']['server_version'], 'Database version should be preserved');
    assertSame($detected, $config['app']['detected'], 'Detected environment metadata should be embedded');
}

function testFallsBackToDetectedPort(): void
{
    $builder = new ConfigBuilder();

    $config = $builder->build(
        ['force_https' => true],
        [],
        [],
        [],
        [
            'slug' => 'alpha',
            'base_domain' => 'example.org',
            'use_subdomain' => true,
            'aliases' => [],
        ],
        [],
        [
            'php_version' => '8.4.0',
            'extensions' => [],
            'https_detected' => true,
            'host' => 'alpha.example.org',
            'port' => 8443,
            'base_url' => 'https://alpha.example.org',
            'base_path' => '/',
        ]
    );

    assertSame('https://alpha.example.org:8443', $config['networks']['alpha']['primary_url'], 'Primary URL should include the detected non-standard port');
    assertSame('mysql', $config['database']['driver'], 'Database driver should default to MySQL when unspecified');
}

$tests = [
    'Builds configuration structure' => 'testBuildsNetworkConfiguration',
    'Falls back to detected port' => 'testFallsBackToDetectedPort',
];

$failures = 0;
foreach ($tests as $label => $callable) {
    try {
        $callable();
        echo "[PASS] {$label}\n";
    } catch (Throwable $e) {
        $failures++;
        echo "[FAIL] {$label}: " . $e->getMessage() . "\n";
    }
}

if ($failures > 0) {
    exit(1);
}

echo "All configuration builder checks passed.\n";
