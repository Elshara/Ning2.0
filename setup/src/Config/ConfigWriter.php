<?php

declare(strict_types=1);

namespace Setup\Config;

/**
 * Writes the generated configuration array to disk while capturing
 * filesystem failures as readable error messages for the setup wizard.
 */
final class ConfigWriter
{
    private string $configPath;

    public function __construct(string $configPath)
    {
        $this->configPath = $configPath;
    }

    /**
     * @param array<string,mixed> $config
     * @return true|string Returns true on success or a descriptive error message on failure.
     */
    public function write(array $config)
    {
        $directoryCheck = $this->ensureDirectoryWritable();
        if ($directoryCheck !== true) {
            return $directoryCheck;
        }

        $contents = "<?php\nreturn " . var_export($config, true) . ";\n";

        $writeError = null;
        set_error_handler(static function (int $severity, string $message) use (&$writeError): bool {
            $writeError = $message;
            return true;
        });

        try {
            $result = file_put_contents($this->configPath, $contents);
        } finally {
            restore_error_handler();
        }

        if ($result === false) {
            return $this->formatError('Unable to write configuration file', $this->configPath, $writeError);
        }

        $this->applyPermissions();

        return true;
    }

    /**
     * @return true|string
     */
    private function ensureDirectoryWritable()
    {
        $configDir = dirname($this->configPath);
        if (!is_dir($configDir)) {
            $mkdirError = null;
            set_error_handler(static function (int $severity, string $message) use (&$mkdirError): bool {
                $mkdirError = $message;
                return true;
            });

            try {
                $created = mkdir($configDir, 0755, true);
            } finally {
                restore_error_handler();
            }

            if (!$created && !is_dir($configDir)) {
                return $this->formatError('Unable to create configuration directory', $configDir, $mkdirError);
            }
        }

        if (is_file($this->configPath)) {
            if (!is_writable($this->configPath)) {
                return 'The existing configuration file is not writable: ' . $this->configPath;
            }

            return true;
        }

        if (!is_writable($configDir)) {
            return 'The configuration directory is not writable: ' . $configDir;
        }

        return true;
    }

    private function applyPermissions(): void
    {
        set_error_handler(static function (): bool {
            return true;
        });

        try {
            chmod($this->configPath, 0640);
        } finally {
            restore_error_handler();
        }
    }

    private function formatError(string $prefix, string $path, ?string $detail = null): string
    {
        $message = $prefix . ': ' . $path;
        if ($detail !== null && $detail !== '') {
            $message .= ' (' . $detail . ')';
        }

        return $message;
    }
}
