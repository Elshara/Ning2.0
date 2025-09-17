<?php
declare(strict_types=1);

namespace Setup\Environment;

require_once dirname(__DIR__, 3) . '/lib/NF/UrlHelpers.php';

trait DetectionHelpers
{
    private function firstHeaderValue(string $value): string
    {
        return \nf_first_header_value($value);
    }

    /**
     * @return array{0:string,1:int|null}
     */
    protected function extractHostAndPort(string $value): array
    {
        return \nf_extract_host_and_port($value);
    }

    /**
     * @return array{proto:string|null,host:string|null,port:int|null}
     */
    private function parseForwardedHeader(string $header): array
    {
        return \nf_parse_forwarded_header($header);
    }

    private function extractCloudflareScheme(string $header): ?string
    {
        return \nf_extract_cloudflare_scheme($header);
    }

    private function isTruthyProxyFlag(string $value): bool
    {
        return \nf_is_truthy_proxy_flag($value);
    }

    private function detectBasePath(array $server): string
    {
        return \nf_detect_base_path($server);
    }

    private function normalizeBaseUrl(string $baseUrl): ?string
    {
        return \nf_normalize_base_url($baseUrl);
    }

    private function normalizeBasePath(string $basePath): ?string
    {
        return \nf_normalize_base_path($basePath);
    }

    private function sanitizeDetectedHost(string $host): string
    {
        return \nf_sanitize_detected_host($host);
    }

    private function isValidHost(string $host): bool
    {
        return \nf_is_valid_host($host);
    }

    private function isIpAddress(string $value): bool
    {
        return \nf_is_ip_address($value);
    }

    private function isDefaultPort(bool $https, int $port): bool
    {
        return \nf_is_default_port($https, $port);
    }

    private function formatHostForUrl(string $host): string
    {
        return \nf_format_host_for_url($host);
    }
}
