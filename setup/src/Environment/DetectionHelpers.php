<?php
declare(strict_types=1);

namespace Setup\Environment;

trait DetectionHelpers
{
    private function firstHeaderValue(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $parts = explode(',', $value);

        return trim((string) $parts[0]);
    }

    /**
     * @return array{0:string,1:int|null}
     */
    protected function extractHostAndPort(string $value): array
    {
        $value = trim($value);
        if ($value === '') {
            return ['', null];
        }

        if (str_starts_with($value, '[')) {
            $end = strpos($value, ']');
            if ($end !== false) {
                $host = substr($value, 1, $end - 1);
                $portPart = substr($value, $end + 1);
                if (str_starts_with($portPart, ':')) {
                    $portPart = substr($portPart, 1);
                }
                $port = ctype_digit($portPart) ? (int) $portPart : null;

                return [$host, $port];
            }
        }

        if (substr_count($value, ':') > 1) {
            return [$value, null];
        }

        $colonPos = strrpos($value, ':');
        if ($colonPos === false) {
            return [$value, null];
        }

        $host = substr($value, 0, $colonPos);
        $portPart = substr($value, $colonPos + 1);
        if ($host === '') {
            $host = $value;
            $portPart = '';
        }

        $port = ctype_digit($portPart) ? (int) $portPart : null;

        return [$host, $port];
    }

    /**
     * @return array{proto:string|null,host:string|null,port:int|null}
     */
    private function parseForwardedHeader(string $header): array
    {
        $result = [
            'proto' => null,
            'host' => null,
            'port' => null,
        ];

        $header = trim($header);
        if ($header === '') {
            return $result;
        }

        $segments = explode(',', $header);
        foreach ($segments as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            $directives = explode(';', $segment);
            foreach ($directives as $directive) {
                $directive = trim($directive);
                if ($directive === '') {
                    continue;
                }

                $equalsPos = strpos($directive, '=');
                if ($equalsPos === false) {
                    continue;
                }

                $name = strtolower(trim(substr($directive, 0, $equalsPos)));
                $value = trim(substr($directive, $equalsPos + 1));
                if ($value === '') {
                    continue;
                }

                if ($value[0] === '"' && str_ends_with($value, '"')) {
                    $value = substr($value, 1, -1);
                }

                if ($name === 'proto' && $result['proto'] === null) {
                    $result['proto'] = strtolower($value);
                } elseif ($name === 'host' && $result['host'] === null) {
                    [$host, $port] = $this->extractHostAndPort($value);
                    if ($host !== '') {
                        $result['host'] = $host;
                    }
                    if ($port !== null) {
                        $result['port'] = $port;
                    }
                }

                if ($result['proto'] !== null && $result['host'] !== null) {
                    break 2;
                }
            }
        }

        return $result;
    }

    private function extractCloudflareScheme(string $header): ?string
    {
        $header = trim($header);
        if ($header === '') {
            return null;
        }

        $data = json_decode($header, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return null;
        }

        $scheme = $data['scheme'] ?? $data['proto'] ?? null;
        if (!is_string($scheme) || $scheme === '') {
            return null;
        }

        return strtolower($scheme);
    }

    private function isTruthyProxyFlag(string $value): bool
    {
        $normalized = strtolower(trim($value));
        if ($normalized === '') {
            return false;
        }

        return in_array($normalized, ['on', 'true', '1', 'yes'], true);
    }

    private function detectBasePath(array $server): string
    {
        $candidates = [];

        $forwardedPrefix = $this->firstHeaderValue((string) ($server['HTTP_X_FORWARDED_PREFIX'] ?? ''));
        if ($forwardedPrefix !== '') {
            $candidates[] = $forwardedPrefix;
        }

        $forwardedPath = $this->firstHeaderValue((string) ($server['HTTP_X_FORWARDED_PATH'] ?? ''));
        if ($forwardedPath !== '') {
            $candidates[] = $forwardedPath;
        }

        $forwardedUri = $this->firstHeaderValue((string) ($server['HTTP_X_FORWARDED_URI'] ?? ''));
        if ($forwardedUri !== '') {
            $uriPath = parse_url($forwardedUri, PHP_URL_PATH);
            if (is_string($uriPath) && $uriPath !== '') {
                $candidates[] = $uriPath;
            }
        }

        $originalUrl = $this->firstHeaderValue((string) ($server['HTTP_X_ORIGINAL_URL'] ?? ''));
        if ($originalUrl !== '') {
            $originalPath = parse_url($originalUrl, PHP_URL_PATH);
            if (is_string($originalPath) && $originalPath !== '') {
                $candidates[] = $originalPath;
            }
        }

        $contextPrefix = (string) ($server['CONTEXT_PREFIX'] ?? '');
        if ($contextPrefix !== '') {
            $candidates[] = $contextPrefix;
        }

        foreach ($candidates as $candidate) {
            $normalizedCandidate = $this->normalizeBasePath($candidate);
            if ($normalizedCandidate !== null && $normalizedCandidate !== '/') {
                return $normalizedCandidate;
            }
        }

        $scriptName = (string) ($server['SCRIPT_NAME'] ?? '');
        if ($scriptName === '') {
            return '/';
        }

        $scriptName = str_replace('\\', '/', $scriptName);
        $scriptDir = trim(dirname($scriptName), '/');
        if ($scriptDir === '' || $scriptDir === '.') {
            $scriptDir = '';
        } else {
            $scriptDir = '/' . $scriptDir;
        }

        if ($scriptDir !== '' && str_ends_with($scriptDir, '/setup')) {
            $scriptDir = substr($scriptDir, 0, -strlen('/setup'));
            $scriptDir = rtrim($scriptDir, '/');
            if ($scriptDir !== '') {
                $scriptDir = '/' . ltrim($scriptDir, '/');
            }
        }

        if ($scriptDir === '' || $scriptDir === '/') {
            return '/';
        }

        return $scriptDir;
    }

    private function normalizeBaseUrl(string $baseUrl): ?string
    {
        $trimmed = trim($baseUrl);
        if ($trimmed === '') {
            return null;
        }

        $parts = parse_url($trimmed);
        if ($parts === false || !is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower((string) $parts['scheme']);
        $host = strtolower((string) $parts['host']);
        $https = $scheme === 'https';

        $port = null;
        if (isset($parts['port'])) {
            $port = (int) $parts['port'];
            if ($port <= 0) {
                $port = null;
            }
        }

        if ($port !== null && $this->isDefaultPort($https, $port)) {
            $port = null;
        }

        $path = (string) ($parts['path'] ?? '');
        if ($path !== '') {
            $path = rtrim($path, '/');
            if ($path === '' || $path === '/') {
                $path = '';
            } elseif ($path[0] !== '/') {
                $path = '/' . $path;
            }
        }

        $authority = $this->formatHostForUrl($host);
        if ($port !== null) {
            $authority .= ':' . $port;
        }

        return $scheme . '://' . $authority . $path;
    }

    private function normalizeBasePath(string $basePath): ?string
    {
        $trimmed = trim($basePath);
        if ($trimmed === '' || $trimmed === '/') {
            return '/';
        }

        if ($trimmed[0] !== '/') {
            $trimmed = '/' . $trimmed;
        }

        $normalized = rtrim($trimmed, '/');
        if ($normalized === '') {
            $normalized = '/';
        }

        if (!preg_match('~^/[A-Za-z0-9/_\-\.]*$~', $normalized)) {
            return null;
        }

        return $normalized === '' ? '/' : $normalized;
    }

    private function sanitizeDetectedHost(string $host): string
    {
        $host = strtolower(trim($host));

        if ($host === '' || $host === '.') {
            return 'localhost';
        }

        $host = rtrim($host, '.');
        if ($host === '') {
            return 'localhost';
        }

        if ($host === 'localhost' || $this->isIpAddress($host)) {
            return $host;
        }

        if (!$this->isValidHost($host)) {
            return 'localhost';
        }

        return $host;
    }

    private function isValidHost(string $host): bool
    {
        if ($host === '') {
            return false;
        }

        if ($host === 'localhost' || $this->isIpAddress($host)) {
            return true;
        }

        if (str_contains($host, '/')) {
            return false;
        }

        return (bool) preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)(?:\.(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?))*$/i', $host);
    }

    private function isIpAddress(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    private function isDefaultPort(bool $https, int $port): bool
    {
        return ($https && $port === 443) || (!$https && $port === 80);
    }

    private function formatHostForUrl(string $host): string
    {
        if ($host === '') {
            return 'localhost';
        }

        if ($this->isIpAddress($host) && str_contains($host, ':')) {
            return '[' . $host . ']';
        }

        return $host;
    }
}
