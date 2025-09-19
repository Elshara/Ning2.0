<?php
declare(strict_types=1);

namespace Setup\Config;

use DateTimeImmutable;
use DateTimeInterface;
use Setup\Environment\DetectionHelpers;

require_once dirname(__DIR__) . '/Environment/DetectionHelpers.php';

/**
 * Builds the configuration structure consumed by the installer writer
 * using the wizard state that has been collected across steps.
 */
final class ConfigBuilder
{
    use DetectionHelpers;

    /**
     * @param array<string,mixed> $environment
     * @param array<string,mixed> $database
     * @param array<string,mixed> $superAdmin
     * @param array<string,mixed> $networkAdmin
     * @param array<string,mixed> $network
     * @param array<string,mixed> $automation
     * @param array<string,mixed> $detected
     */
    public function build(
        array $environment,
        array $database,
        array $superAdmin,
        array $networkAdmin,
        array $network,
        array $automation,
        array $detected,
        ?DateTimeInterface $createdAt = null
    ): array {
        $baseUrl = $this->environmentBaseUrl($environment);
        $scheme = $this->deriveScheme($environment, $baseUrl);
        $port = $this->derivePort($detected, $baseUrl);

        $useSubdomain = !empty($network['use_subdomain']);
        $baseDomain = $network['base_domain'] ?? 'localhost';
        $slug = $network['slug'] ?? 'network';
        $primaryHost = $useSubdomain
            ? $slug . '.' . $baseDomain
            : $baseDomain;

        $basePath = $useSubdomain ? '/' : ($network['base_path'] ?? '/');
        $normalizedPath = $this->normalizeBasePath((string) $basePath);
        $basePath = $normalizedPath ?? '/';

        $primaryUrl = $this->buildPrimaryUrl($scheme, $primaryHost, $port, $basePath);

        $aliases = $network['aliases'] ?? [];
        if (!is_array($aliases)) {
            $aliases = [];
        }

        $createdAt ??= new DateTimeImmutable();

        return [
            'app' => [
                'name' => $environment['site_name'] ?? 'My Network',
                'base_url' => $environment['base_url'] ?? '',
                'force_https' => (bool) ($environment['force_https'] ?? false),
                'detected' => $detected,
                'created_at' => $createdAt->format(DATE_ATOM),
            ],
            'database' => [
                'host' => $database['host'] ?? 'localhost',
                'port' => (int) ($database['port'] ?? 3306),
                'name' => $database['name'] ?? '',
                'user' => $database['user'] ?? '',
                'password' => $database['password'] ?? '',
                'driver' => strtolower((string) ($database['driver'] ?? 'mysql')),
                'server_version' => (string) ($database['server_version'] ?? ''),
                'dsn' => sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $database['host'] ?? 'localhost',
                    (int) ($database['port'] ?? 3306),
                    $database['name'] ?? ''
                ),
            ],
            'super_admin' => $superAdmin,
            'networks' => [
                $slug => [
                    'id' => $slug,
                    'name' => $network['name'] ?? 'My Network',
                    'primary_host' => $primaryHost,
                    'primary_url' => $primaryUrl,
                    'base_domain' => $baseDomain,
                    'use_subdomain' => (bool) $useSubdomain,
                    'base_path' => $basePath,
                    'aliases' => array_values($aliases),
                    'auto_updates' => [
                        'enabled' => (bool) ($network['auto_updates'] ?? true),
                        'allow_super_admin_deployments' => (bool) ($network['allow_super_admin_updates'] ?? true),
                    ],
                    'admins' => [
                        [
                            'email' => $networkAdmin['email'] ?? '',
                            'password_hash' => $networkAdmin['password_hash'] ?? '',
                            'role' => 'owner',
                        ],
                    ],
                ],
            ],
            'updates' => [
                'auto_deploy' => (bool) ($automation['auto_updates'] ?? true),
                'provider' => 'github',
                'branch' => $automation['updates_branch'] ?? 'main',
                'allow_network_opt_out' => (bool) ($automation['allow_network_override'] ?? true),
            ],
            'scheduler' => [
                'mode' => $automation['scheduler_mode'] ?? 'system_cron',
                'interval_minutes' => (int) ($automation['scheduler_interval'] ?? 15),
                'last_run' => null,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $environment
     */
    private function deriveScheme(array $environment, ?string $baseUrl): string
    {
        if (!empty($environment['force_https'])) {
            return 'https';
        }

        if ($baseUrl !== null) {
            $parsed = parse_url($baseUrl, PHP_URL_SCHEME);
            if (is_string($parsed) && $parsed !== '') {
                return strtolower($parsed);
            }
        }

        return 'http';
    }

    /**
     * @param array<string,mixed> $detected
     */
    private function derivePort(array $detected, ?string $baseUrl): ?int
    {
        if ($baseUrl !== null) {
            $parsedPort = parse_url($baseUrl, PHP_URL_PORT);
            if (is_int($parsedPort)) {
                return $parsedPort;
            }
        }

        if (isset($detected['port'])) {
            $port = (int) $detected['port'];
            if ($port > 0) {
                return $port;
            }
        }

        return null;
    }

    private function buildPrimaryUrl(string $scheme, string $host, ?int $port, string $basePath): string
    {
        $https = $scheme === 'https';
        $url = $scheme . '://' . $this->formatHostForUrl($host);

        if ($port !== null && !$this->isDefaultPort($https, $port)) {
            $url .= ':' . $port;
        }

        if ($basePath !== '/' && $basePath !== '') {
            $url .= $basePath;
        }

        return $url;
    }

    /**
     * @param array<string,mixed> $environment
     */
    private function environmentBaseUrl(array $environment): ?string
    {
        $baseUrl = $environment['base_url'] ?? null;
        if (!is_string($baseUrl)) {
            return null;
        }

        $trimmed = trim($baseUrl);
        return $trimmed === '' ? null : $trimmed;
    }
}
