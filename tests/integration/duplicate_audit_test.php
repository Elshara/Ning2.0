<?php

function assertCommandSucceeded(string $command, string $context): void
{
    exec($command, $output, $exitCode);
    if ($exitCode !== 0) {
        throw new RuntimeException(
            $context . ' failed with exit code ' . $exitCode . '\n' . implode("\n", $output)
        );
    }
}

function buildDuplicateScannerCommand(string $target): string
{
    $script = realpath(__DIR__ . '/../../tools/detect_duplicates.php');
    if ($script === false) {
        throw new RuntimeException('Unable to locate duplicate scanner script.');
    }

    return escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($script) . ' --fail-on-duplicates ' . escapeshellarg($target);
}

function testSetupConfigIsDuplicateFree(): void
{
    $command = buildDuplicateScannerCommand(__DIR__ . '/../../setup/src/Config');
    assertCommandSucceeded($command, 'Setup configuration duplicate audit');
}

function testSetupEnvironmentIsDuplicateFree(): void
{
    $command = buildDuplicateScannerCommand(__DIR__ . '/../../setup/src/Environment');
    assertCommandSucceeded($command, 'Setup environment duplicate audit');
}

$tests = [
    'Setup configuration helpers remain deduplicated' => 'testSetupConfigIsDuplicateFree',
    'Setup environment helpers remain deduplicated' => 'testSetupEnvironmentIsDuplicateFree',
];

$failures = 0;
foreach ($tests as $label => $callable) {
    try {
        $callable();
        echo "[PASS] {$label}\n";
    } catch (Throwable $e) {
        $failures++;
        echo "[FAIL] {$label}: " . $e->getMessage() . "\n";
    }
}

if ($failures > 0) {
    exit(1);
}

echo "Duplicate audits completed successfully.\n";
