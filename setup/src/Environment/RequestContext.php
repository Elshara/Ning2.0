<?php
declare(strict_types=1);

namespace Setup\Environment;

final class RequestContext
{
    use DetectionHelpers;

    private string $phpVersion = PHP_VERSION;

    /**
     * @var list<string>
     */
    private array $extensions = [];

    private bool $httpsDetected = false;

    private string $host = 'localhost';

    private int $port = 80;

    private string $baseUrl = 'http://localhost';

    private string $basePath = '/';

    /**
     * @param array<string,mixed> $server
     */
    public static function fromGlobals(array $server): self
    {
        $instance = new self();
        $instance->initialise($server);

        return $instance;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'php_version' => $this->phpVersion(),
            'extensions' => $this->extensions(),
            'https_detected' => $this->httpsDetected(),
            'host' => $this->host(),
            'port' => $this->port(),
            'base_url' => $this->baseUrl(),
            'base_path' => $this->basePath(),
        ];
    }

    public function phpVersion(): string
    {
        return $this->phpVersion;
    }

    /**
     * @return list<string>
     */
    public function extensions(): array
    {
        return $this->extensions;
    }

    public function httpsDetected(): bool
    {
        return $this->httpsDetected;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function port(): int
    {
        return $this->port;
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    /**
     * @param array<string,mixed> $server
     */
    private function initialise(array $server): void
    {

        $metadata = \nf_detect_request_metadata($server);



        $metadata = \nf_detect_request_metadata($server);

        $forwardedHeader = $this->parseForwardedHeader((string) ($server['HTTP_FORWARDED'] ?? ''));

        $forwardedProto = $this->firstHeaderValue((string) ($server['HTTP_X_FORWARDED_PROTO'] ?? ''));
        if ($forwardedProto === '') {
            $forwardedProto = $this->firstHeaderValue((string) ($server['HTTP_X_ORIGINAL_PROTO'] ?? ''));
        }
        if ($forwardedProto === '' && $forwardedHeader['proto'] !== null) {
            $forwardedProto = $forwardedHeader['proto'];
        }

        $cloudflareScheme = $this->extractCloudflareScheme((string) ($server['HTTP_CF_VISITOR'] ?? ''));
        if ($forwardedProto === '' && $cloudflareScheme !== null) {
            $forwardedProto = $cloudflareScheme;
        }

        $forwardedHost = $this->firstHeaderValue((string) ($server['HTTP_X_FORWARDED_HOST'] ?? ''));
        if ($forwardedHost === '') {
            $forwardedHost = $this->firstHeaderValue((string) ($server['HTTP_X_ORIGINAL_HOST'] ?? ''));
        }
        if ($forwardedHost === '' && $forwardedHeader['host'] !== null) {
            $forwardedHost = $forwardedHeader['host'];
        }

        $forwardedPort = $this->firstHeaderValue((string) ($server['HTTP_X_FORWARDED_PORT'] ?? ''));
        if ($forwardedPort === '') {
            $forwardedPort = $this->firstHeaderValue((string) ($server['HTTP_X_ORIGINAL_PORT'] ?? ''));
        }
        if ($forwardedPort === '' && $forwardedHeader['port'] !== null) {
            $forwardedPort = (string) $forwardedHeader['port'];
        }

        $forwardedSsl = (string) ($server['HTTP_X_FORWARDED_SSL'] ?? '');
        $frontEndHttps = (string) ($server['HTTP_FRONT_END_HTTPS'] ?? '');

        $https = (!empty($server['HTTPS']) && $server['HTTPS'] !== 'off')
            || $this->isTruthyProxyFlag($forwardedSsl)
            || $this->isTruthyProxyFlag($frontEndHttps)
            || (isset($server['SERVER_PORT']) && (int) $server['SERVER_PORT'] === 443)
            || (($server['REQUEST_SCHEME'] ?? '') === 'https')
            || ($forwardedProto !== '' && strtolower($forwardedProto) === 'https')
            || ($cloudflareScheme !== null && $cloudflareScheme === 'https');

        $hostHeader = $forwardedHost !== ''
            ? $forwardedHost
            : ($server['HTTP_HOST'] ?? ($server['SERVER_NAME'] ?? 'localhost'));

        [$host, $portFromHeader] = $this->extractHostAndPort((string) $hostHeader);
        $host = $this->sanitizeDetectedHost($host);

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
        $baseUrl = $scheme . '://' . $this->formatHostForUrl($host);
        if (!$this->isDefaultPort($https, $port)) {
            $baseUrl .= ':' . $port;
        }

        $basePath = $this->detectBasePath($server);
        if ($basePath !== '/' && $basePath !== '') {
            $baseUrl .= $basePath;
        }

        $normalizedBaseUrl = $this->normalizeBaseUrl($baseUrl);



        $extensions = get_loaded_extensions();
        $extensions = is_array($extensions) ? array_map('strval', $extensions) : [];
        if ($extensions !== []) {
            natcasesort($extensions);
            $extensions = array_values($extensions);
        }

        $this->extensions = $extensions;





        $this->httpsDetected = $metadata['https'];
        $this->host = $metadata['host'];
        $this->port = $metadata['port'];
        $this->baseUrl = $metadata['base_url'];
        $this->basePath = $metadata['base_path'];



        $this->httpsDetected = $https;
        $this->host = $host;
        $this->port = $port;
        $this->baseUrl = $normalizedBaseUrl ?? $baseUrl;
        $this->basePath = $basePath;


    }
}
