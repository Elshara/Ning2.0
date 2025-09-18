<?php
declare(strict_types=1);

if (!function_exists('nf_extract_host_and_port')) {
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
}

if (!function_exists('nf_sanitize_detected_host')) {
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
}

if (!function_exists('nf_is_valid_host')) {
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
}

if (!function_exists('nf_is_ip_address')) {
    function nf_is_ip_address(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}

if (!function_exists('nf_is_default_port')) {
    function nf_is_default_port(bool $https, int $port): bool
    {
        return ($https && $port === 443) || (!$https && $port === 80);
    }
}

if (!function_exists('nf_format_host_for_url')) {
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
