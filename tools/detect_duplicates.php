#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Detect repeated lines and code blocks that frequently indicate copy/paste duplication.
 */

$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "Unable to resolve repository root.\n");
    exit(1);
}

$inputPaths = array_slice($argv, 1);
$failOnDuplicates = false;
$inputPaths = array_values(array_filter($inputPaths, static function (string $value) use (&$failOnDuplicates): bool {
    if ($value === '--fail-on-duplicates') {
        $failOnDuplicates = true;
        return false;
    }
    return true;
}));

if ($inputPaths === []) {
    $inputPaths = [$root];
} else {
    $inputPaths = array_map(static function (string $path) use ($root): string {
        $resolved = realpath($path);
        if ($resolved === false) {
            return $root . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        }
        return $resolved;
    }, $inputPaths);
}

$extensions = [
    'php', 'phtml', 'phps', 'inc',
    'js', 'ts', 'jsx', 'tsx',
    'css', 'scss', 'less',
    'html', 'htm', 'twig',
    'md', 'markdown', 'txt',
    'json', 'yml', 'yaml', 'xml', 'ini', 'sql'
];

$skipDirs = ['.git', 'vendor', 'node_modules', 'tmp', 'config', 'storage'];
$minLineLength = 32; // characters
$blockSize = 3;      // consecutive lines per block
$minBlockLength = 48;

$hasDuplicates = false;

foreach ($inputPaths as $path) {
    scanPath($path);
}

echo $hasDuplicates ? "Duplicate patterns detected.\n" : "No duplicate patterns detected.\n";
if ($hasDuplicates && $failOnDuplicates) {
    exit(1);
}
exit(0);

function scanPath(string $path): void
{
    global $extensions, $skipDirs;

    if (is_dir($path)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
                static function (SplFileInfo $fileInfo) use ($skipDirs): bool {
                    if ($fileInfo->isDir()) {
                        return !in_array($fileInfo->getFilename(), $skipDirs, true);
                    }
                    return true;
                }
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->isFile()) {
                scanFile($file->getPathname());
            }
        }
        return;
    }

    if (is_file($path)) {
        scanFile($path);
    }
}

function scanFile(string $filePath): void
{
    global $extensions, $minLineLength, $blockSize, $minBlockLength, $hasDuplicates;

    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if ($extension === '' || !in_array($extension, $extensions, true)) {
        return;
    }

    $contents = @file_get_contents($filePath);
    if ($contents === false || $contents === '') {
        return;
    }

    $lines = preg_split('/\r\n|\r|\n/', $contents);
    if ($lines === false) {
        return;
    }

    $lineMap = [];
    foreach ($lines as $index => $line) {
        $normalized = normalizeLine($line);
        if ($normalized === null) {
            continue;
        }
        $lineMap[$normalized][] = $index + 1;
    }

    $duplicates = array_filter($lineMap, static function (array $occurrences): bool {
        return count($occurrences) > 1;
    });

    $blockMap = [];
    $normalizedLines = array_map('normalizeForBlock', $lines);
    for ($i = 0; $i <= count($normalizedLines) - $blockSize; $i++) {
        $segment = array_slice($normalizedLines, $i, $blockSize);
        if (in_array(null, $segment, true)) {
            continue;
        }
        $joined = implode("\n", $segment);
        if (strlen($joined) < $minBlockLength) {
            continue;
        }
        $blockMap[$joined][] = $i + 1;
    }

    $duplicateBlocks = array_filter($blockMap, static function (array $occurrences): bool {
        return count($occurrences) > 1;
    });

    if ($duplicates === [] && $duplicateBlocks === []) {
        return;
    }

    $hasDuplicates = true;

    echo "\n>> " . relativePath($filePath) . "\n";
    if ($duplicates !== []) {
        echo "   Duplicate lines:\n";
        foreach ($duplicates as $line => $occurrences) {
            echo sprintf(
                '    - "%s" (lines %s)' . PHP_EOL,
                trim($line),
                implode(', ', $occurrences)
            );
        }
    }
    if ($duplicateBlocks !== []) {
        echo "   Duplicate blocks (size {$blockSize}):\n";
        foreach ($duplicateBlocks as $block => $occurrences) {
            $preview = substr(str_replace("\n", ' / ', trim($block)), 0, 120);
            echo sprintf(
                '    - "%s" (starting at lines %s)' . PHP_EOL,
                $preview,
                implode(', ', $occurrences)
            );
        }
    }
}

function normalizeLine(string $line): ?string
{
    global $minLineLength;
    $trimmed = trim($line);
    if ($trimmed === '' || strlen($trimmed) < $minLineLength) {
        return null;
    }
    $prefix = substr($trimmed, 0, 2);
    if (in_array($prefix, ['//', '/*', '* ', '# '], true)) {
        return null;
    }
    return $trimmed;
}

function normalizeForBlock(string $line): ?string
{
    $trimmed = trim($line);
    if ($trimmed === '') {
        return null;
    }
    $prefix = substr($trimmed, 0, 2);
    if (in_array($prefix, ['//', '/*', '* ', '# '], true)) {
        return null;
    }
    return $trimmed;
}

function relativePath(string $absolute): string
{
    global $root;
    if (strpos($absolute, $root) === 0) {
        return ltrim(substr($absolute, strlen($root)), DIRECTORY_SEPARATOR);
    }
    return $absolute;
}
