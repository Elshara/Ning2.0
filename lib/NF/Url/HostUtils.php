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

if (!function_exists('nf_normalize_idn_host')) {
    /**
     * Converts internationalised domain names into their ASCII representation
     * when the intl extension is available. This mirrors the punycode
     * normalisation helpers used by platforms such as PHPFox and Dolphin so
     * multi-network aliases can safely store non-Latin characters.
     */
    function nf_normalize_idn_host(string $host): string
    {
        $host = strtolower(trim($host));

        if ($host === '' || $host === 'localhost' || nf_is_ip_address($host)) {
            return $host;
        }

        if (preg_match('/[^\x20-\x7e]/', $host) !== 1) {
            return $host;
        }

        if (function_exists('idn_to_ascii')) {
            $flags = defined('IDNA_NONTRANSITIONAL_TO_ASCII')
                ? IDNA_NONTRANSITIONAL_TO_ASCII
                : (defined('IDNA_DEFAULT') ? IDNA_DEFAULT : 0);
            $ascii = idn_to_ascii($host, $flags);
            if (is_string($ascii) && $ascii !== '') {
                return strtolower($ascii);
            }
        }

        return $host;
    }
}

if (!function_exists('nf_sanitize_detected_host')) {
    function nf_sanitize_detected_host(string $host): string
    {
        $host = nf_normalize_idn_host($host);

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

if (!function_exists('nf_multi_level_tld_suffixes')) {
    /**
     * @return list<string>
     */
    function nf_multi_level_tld_suffixes(): array
    {
        static $suffixes = [
            'com.au',
            'net.au',
            'org.au',
            'edu.au',
            'gov.au',
            'asn.au',
            'id.au',
            'com.br',
            'com.cn',
            'co.jp',
            'or.jp',
            'ne.jp',
            'ac.jp',
            'go.jp',
            'co.nz',
            'org.nz',
            'govt.nz',
            'ac.nz',
            'co.uk',
            'org.uk',
            'gov.uk',
            'ac.uk',
            'net.uk',
            'sch.uk',
        ];

        return $suffixes;
    }
}

if (!function_exists('nf_derive_base_domain')) {
    /**
     * Returns the registrable domain for a given host while accounting for
     * multi-level public suffixes (for example `example.co.uk`).
     */
    function nf_derive_base_domain(string $host): string
    {
        $host = nf_normalize_idn_host($host);

        if ($host === '' || $host === 'localhost' || nf_is_ip_address($host)) {
            return $host === '' ? 'localhost' : $host;
        }

        $parts = explode('.', $host);
        if (count($parts) < 2) {
            return $host;
        }

        $tld = array_pop($parts);
        $secondLevel = array_pop($parts);
        if ($secondLevel === null) {
            return $host;
        }

        $candidate = $secondLevel . '.' . $tld;

        if (!empty($parts)) {
            $suffix = strtolower($candidate);
            if (in_array($suffix, nf_multi_level_tld_suffixes(), true)) {
                $thirdLevel = array_pop($parts);
                if ($thirdLevel !== null && $thirdLevel !== '') {
                    return $thirdLevel . '.' . $candidate;
                }
            }
        }

        return $candidate;
    }
}

if (!function_exists('nf_derive_slug_from_host')) {
    /**
     * Derives a network slug from a host by stripping the base domain and
     * normalising the remaining label for safe URL usage.
     */
    function nf_derive_slug_from_host(string $host, string $baseDomain): string
    {
        $host = nf_normalize_idn_host($host);
        $baseDomain = nf_normalize_idn_host($baseDomain);

        if ($host === '' || $host === $baseDomain || nf_is_ip_address($host)) {
            return 'network';
        }

        $prefix = $host;
        if ($baseDomain !== '') {
            $suffix = '.' . $baseDomain;
            if (str_ends_with($host, $suffix)) {
                $prefix = substr($host, 0, -strlen($suffix));
            }
        }

        $segments = array_filter(explode('.', $prefix));
        if (empty($segments)) {
            return 'network';
        }

        $candidate = (string) end($segments);
        if ($candidate === '') {
            return 'network';
        }

        $candidate = preg_replace('/[^a-z0-9-]/i', '-', $candidate) ?? '';
        $candidate = trim($candidate, '-');
        if ($candidate === '') {
            return 'network';
        }

        return strtolower($candidate);
    }
}
