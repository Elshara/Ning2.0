<?php
declare(strict_types=1);

/**
 * Run `php -l` against repository PHP files and capture a JSON summary for audit review.
 *
 * The helper can be invoked either directly from the command line or by including the
 * function within integration tests. When executed it writes `tmp/audit/php_lint_audit.json`
 * so contributors can inspect failures after the fact.
 */
function nf_run_php_lint_audit(array $paths = []): array
{
    $root = realpath(__DIR__ . '/..');
    if ($root === false) {
        throw new RuntimeException('Unable to determine repository root for lint audit.');
    }

    if ($paths === []) {
        $paths = [$root];
    }

    $files = nf_collect_php_files($root, $paths);
    sort($files);

    $failures = [];
    $checked = 0;

    foreach ($files as $file) {
        [$exitCode, $output] = nf_execute_php_lint($file);
        ++$checked;

        if ($exitCode !== 0) {
            $failures[] = [
                'file' => $file,
                'output' => $output,
                'exit_code' => $exitCode,
            ];
        }
    }

    $status = $failures === [] ? 'ok' : 'failed';
    $logPath = nf_write_php_lint_audit_log($root, $checked, $failures);

    return [
        'status' => $status,
        'checked_files' => $checked,
        'failures' => $failures,
        'log_path' => $logPath,
    ];
}

/**
 * @param array<int,string> $paths
 * @return array<int,string>
 */
function nf_collect_php_files(string $root, array $paths): array
{
    $files = [];

    foreach ($paths as $path) {
        $normalized = nf_normalize_audit_path($root, $path);
        if ($normalized === null) {
            continue;
        }

        if (is_file($normalized)) {
            if (nf_should_include_file($normalized)) {
                $files[] = $normalized;
            }
            continue;
        }

        if (!is_dir($normalized)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $normalized,
                FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
            )
        );

        /** @var SplFileInfo $info */
        foreach ($iterator as $info) {
            $filePath = $info->getPathname();
            if (!nf_should_include_file($filePath)) {
                continue;
            }

            $files[] = $filePath;
        }
    }

    return $files;
}

function nf_should_include_file(string $path): bool
{
    if (str_contains($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
        return false;
    }

    if (str_contains($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR)) {
        return false;
    }

    if (str_contains($path, DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR)) {
        return false;
    }

    if (str_ends_with($path, DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php')) {
        return false;
    }

    return str_ends_with(strtolower($path), '.php');
}

function nf_normalize_audit_path(string $root, string $path): ?string
{
    if ($path === '') {
        return null;
    }

    if ($path[0] === DIRECTORY_SEPARATOR) {
        return realpath($path) ?: null;
    }

    $candidate = $root . DIRECTORY_SEPARATOR . $path;
    return realpath($candidate) ?: null;
}

/**
 * @return array{0:int,1:string}
 */
function nf_execute_php_lint(string $file): array
{
    $command = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file) . ' 2>&1';
    $output = [];
    $exitCode = 0;
    exec($command, $output, $exitCode);

    return [$exitCode, implode(PHP_EOL, $output)];
}

function nf_write_php_lint_audit_log(string $root, int $checked, array $failures): string
{
    $logDir = $root . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'audit';
    if (!is_dir($logDir) && !mkdir($logDir, 0777, true) && !is_dir($logDir)) {
        throw new RuntimeException('Unable to create audit log directory: ' . $logDir);
    }

    $logPath = $logDir . DIRECTORY_SEPARATOR . 'php_lint_audit.json';
    $payload = [
        'generated_at' => gmdate(DATE_ATOM),
        'checked_files' => $checked,
        'failures' => $failures,
    ];

    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        throw new RuntimeException('Failed to encode lint audit payload.');
    }

    file_put_contents($logPath, $json . PHP_EOL);

    return $logPath;
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['argv'][0] ?? '') === __FILE__) {
    $arguments = array_slice($_SERVER['argv'], 1);
    $summary = nf_run_php_lint_audit($arguments);

    if ($summary['status'] === 'ok') {
        fwrite(STDOUT, sprintf(
            "PHP lint audit passed: %d files checked. Log written to %s\n",
            $summary['checked_files'],
            $summary['log_path']
        ));
        exit(0);
    }

    fwrite(STDERR, "PHP lint audit detected failures:\n");
    foreach ($summary['failures'] as $failure) {
        fwrite(STDERR, sprintf("- %s\n%s\n", $failure['file'], $failure['output']));
    }
    fwrite(STDERR, sprintf(
        "See %s for the complete report.\n",
        $summary['log_path']
    ));
    exit(1);
}
