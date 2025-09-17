<?php
declare(strict_types=1);

if (!function_exists('nf_base_url_from_config')) {
    /**
     * @param array<string,mixed>|null $config
     */
    function nf_base_url_from_config(?array $config): ?string
    {
        if ($config === null) {
            return null;
        }

        $candidates = [];

        $appConfig = $config['app'] ?? null;
        if (is_array($appConfig)) {
            $candidate = $appConfig['base_url'] ?? null;
            if (is_string($candidate)) {
                $candidates[] = $candidate;
            }
        }

        if (empty($candidates)) {
            $networks = $config['networks'] ?? null;
            if (is_array($networks)) {
                foreach ($networks as $network) {
                    if (!is_array($network)) {
                        continue;
                    }

                    $candidate = $network['primary_url'] ?? null;
                    if (is_string($candidate) && trim($candidate) !== '') {
                        $candidates[] = $candidate;
                        break;
                    }
                }
            }
        }

        foreach ($candidates as $candidate) {
            $normalized = nf_normalize_base_url($candidate);
            if ($normalized !== null) {
                return $normalized;
            }

            $trimmed = trim($candidate);
            if ($trimmed !== '') {
                return rtrim($trimmed, '/');
            }
        }

        return null;
    }

    /**
     * @param array<string,mixed> $server
     */
    function nf_detect_base_url_from_server(array $server): string
    {
        $forwarded = nf_parse_forwarded_header((string) ($server['HTTP_FORWARDED'] ?? ''));

        $forwardedProto = nf_first_header_value((string) ($server['HTTP_X_FORWARDED_PROTO'] ?? ''));
        if ($forwardedProto === '' && $forwarded['proto'] !== null) {
            $forwardedProto = $forwarded['proto'];
        }

        $cloudflareScheme = nf_extract_cloudflare_scheme((string) ($server['HTTP_CF_VISITOR'] ?? ''));
        if ($forwardedProto === '' && $cloudflareScheme !== null) {
            $forwardedProto = $cloudflareScheme;
        }

        $forwardedHost = nf_first_header_value((string) ($server['HTTP_X_FORWARDED_HOST'] ?? ''));
        if ($forwardedHost === '' && $forwarded['host'] !== null) {
            $forwardedHost = $forwarded['host'];
        }

        $forwardedPort = nf_first_header_value((string) ($server['HTTP_X_FORWARDED_PORT'] ?? ''));
        if ($forwardedPort === '' && $forwarded['port'] !== null) {
            $forwardedPort = (string) $forwarded['port'];
        }

        $https = (!empty($server['HTTPS']) && $server['HTTPS'] !== 'off')
            || nf_is_truthy_proxy_flag((string) ($server['HTTP_X_FORWARDED_SSL'] ?? ''))
            || nf_is_truthy_proxy_flag((string) ($server['HTTP_FRONT_END_HTTPS'] ?? ''))
            || ((isset($server['SERVER_PORT']) && (int) $server['SERVER_PORT'] === 443))
            || (($server['REQUEST_SCHEME'] ?? '') === 'https')
            || ($forwardedProto !== '' && strtolower($forwardedProto) === 'https')
            || ($cloudflareScheme !== null && $cloudflareScheme === 'https');

        $hostHeader = $forwardedHost !== ''
            ? $forwardedHost
            : ($server['HTTP_HOST'] ?? ($server['SERVER_NAME'] ?? 'localhost'));

        [$host, $portFromHeader] = nf_extract_host_and_port((string) $hostHeader);
        $host = nf_sanitize_detected_host($host);

        $port = null;
        if ($forwardedPort !== '' && ctype_digit($forwardedPort)) {
            $port = (int) $forwardedPort;
        } elseif ($portFromHeader !== null) {
            $port = $portFromHeader;
        } elseif (isset($server['SERVER_PORT']) && ctype_digit((string) $server['SERVER_PORT'])) {
            $port = (int) $server['SERVER_PORT'];
        }

        if ($port === null || $port <= 0) {
            $port = $https ? 443 : 80;
        }

        $scheme = $https ? 'https' : 'http';
        $baseUrl = $scheme . '://' . nf_format_host_for_url($host);
        if (!nf_is_default_port($https, $port)) {
            $baseUrl .= ':' . $port;
        }

        $basePath = nf_detect_base_path($server);
        if ($basePath !== '/' && $basePath !== '') {
            $baseUrl .= $basePath;
        }

        $normalized = nf_normalize_base_url($baseUrl);
        if ($normalized !== null) {
            return $normalized;
        }

        $fallback = rtrim($baseUrl, '/');

        return $fallback === '' ? ($scheme . '://localhost') : $fallback;
    }

    /**
     * @param array<string,mixed>|null $config
     * @param array<string,mixed> $server
     */
    function nf_resolve_base_url(?array $config, array $server): string
    {
        $fromConfig = nf_base_url_from_config($config);
        if ($fromConfig !== null) {
            return $fromConfig;
        }

        return nf_detect_base_url_from_server($server);
    }

    function nf_first_header_value(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $parts = explode(',', $value);

        return trim((string) $parts[0]);
    }

    /**
     * @return array{proto:string|null,host:string|null,port:int|null}
     */
    function nf_parse_forwarded_header(string $header): array
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
                    [$host, $port] = nf_extract_host_and_port($value);
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

    /**
     * @return array{0:string,1:int|null}
     */
    function nf_extract_host_and_port(string $value): array
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

    function nf_extract_cloudflare_scheme(string $header): ?string
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

    function nf_is_truthy_proxy_flag(string $value): bool
    {
        $normalized = strtolower(trim($value));
        if ($normalized === '') {
            return false;
        }

        return in_array($normalized, ['on', 'true', '1', 'yes'], true);
    }

    /**
     * @param array<string,mixed> $server
     */
    function nf_detect_base_path(array $server): string
    {
        $candidates = [];

        $forwardedPrefix = nf_first_header_value((string) ($server['HTTP_X_FORWARDED_PREFIX'] ?? ''));
        if ($forwardedPrefix !== '') {
            $candidates[] = $forwardedPrefix;
        }

        $forwardedPath = nf_first_header_value((string) ($server['HTTP_X_FORWARDED_PATH'] ?? ''));
        if ($forwardedPath !== '') {
            $candidates[] = $forwardedPath;
        }

        $forwardedUri = nf_first_header_value((string) ($server['HTTP_X_FORWARDED_URI'] ?? ''));
        if ($forwardedUri !== '') {
            $uriPath = parse_url($forwardedUri, PHP_URL_PATH);
            if (is_string($uriPath) && $uriPath !== '') {
                $candidates[] = $uriPath;
            }
        }

        $contextPrefix = (string) ($server['CONTEXT_PREFIX'] ?? '');
        if ($contextPrefix !== '') {
            $candidates[] = $contextPrefix;
        }

        foreach ($candidates as $candidate) {
            $normalizedCandidate = nf_normalize_base_path($candidate);
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

    function nf_normalize_base_path(string $basePath): ?string
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

    function nf_normalize_base_url(string $baseUrl): ?string
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

        if ($port !== null && nf_is_default_port($https, $port)) {
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

        $authority = nf_format_host_for_url($host);
        if ($port !== null) {
            $authority .= ':' . $port;
        }

        return $scheme . '://' . $authority . $path;
    }

    function nf_sanitize_detected_host(string $host): string
    {
        $host = strtolower(trim($host));

        if ($host === '' || $host === '.') {
            return 'localhost';
        }

        $host = rtrim($host, '.');
        if ($host === '') {
            return 'localhost';
        }

        if ($host === 'localhost' || nf_is_ip_address($host)) {
            return $host;
        }

        if (!nf_is_valid_host($host)) {
            return 'localhost';
        }

        return $host;
    }

    function nf_is_valid_host(string $host): bool
    {
        if ($host === '') {
            return false;
        }

        if ($host === 'localhost' || nf_is_ip_address($host)) {
            return true;
        }

        if (str_contains($host, '/')) {
            return false;
        }
        return (bool) preg_match(
            '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)(?:\.(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?))*$/i/',
            $host
        );
    }

    function nf_is_ip_address(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    function nf_is_default_port(bool $https, int $port): bool
    {
        return ($https && $port === 443) || (!$https && $port === 80);
    }

    function nf_format_host_for_url(string $host): string
    {
        if ($host === '') {
            return 'localhost';
        }

        if (nf_is_ip_address($host) && str_contains($host, ':')) {
            return '[' . $host . ']';
        }

        return $host;
    }
}
