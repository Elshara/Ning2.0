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
    }
}
