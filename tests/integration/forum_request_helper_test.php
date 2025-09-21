<?php
require_once __DIR__ . '/../../widgets/forum/lib/helpers/Forum_RequestHelper.php';

function assertSame($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new Exception($message . ' (expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . ')');
    }
}

function assertTrue($condition, string $message): void
{
    if (!$condition) {
        throw new Exception($message . ' (expected true)');
    }
}

function assertFalse($condition, string $message): void
{
    if ($condition) {
        throw new Exception($message . ' (expected false)');
    }
}

function testReadStringTrims(): void
{
    $source = ['title' => "  Example  "];
    assertSame('Example', Forum_RequestHelper::readString($source, 'title'), 'Strings should be trimmed by default');
    assertSame('fallback', Forum_RequestHelper::readString([], 'title', 'fallback'), 'Missing keys should return the provided default');
}

function testReadContentPreservesWhitespace(): void
{
    $source = ['body' => "Line 1\nLine 2"];
    assertSame("Line 1\nLine 2", Forum_RequestHelper::readContent($source, 'body'), 'Content should preserve internal newlines');
}

function testReadBoolean(): void
{
    assertTrue(Forum_RequestHelper::readBoolean(['flag' => 'YES'], 'flag'), 'Truthy values should be detected case-insensitively');
    assertFalse(Forum_RequestHelper::readBoolean(['flag' => '0'], 'flag'), 'Numeric zero should evaluate to false');
    assertFalse(Forum_RequestHelper::readBoolean(['flag' => ['unexpected']], 'flag'), 'Non-scalar values should be treated as false');
}

function testReadInt(): void
{
    assertSame(5, Forum_RequestHelper::readInt(['count' => '5'], 'count', 0, 0), 'Numeric strings should be cast to integers');
    assertSame(3, Forum_RequestHelper::readInt(['count' => '3.9'], 'count', 0, 0), 'Decimal strings should be truncated to integers');
    assertSame(1, Forum_RequestHelper::readInt(['count' => '-4'], 'count', 1, 1), 'The provided minimum should be enforced');
    assertSame(10, Forum_RequestHelper::readInt(['count' => '50'], 'count', 0, 0, 10), 'The provided maximum should be enforced');
    assertSame(2, Forum_RequestHelper::readInt([], 'count', 2, 0), 'Missing values should return the default');
}

function testReadPage(): void
{
    assertSame(1, Forum_RequestHelper::readPage(['page' => '0']), 'Pages less than one should normalise to 1');
    assertSame(3, Forum_RequestHelper::readPage(['page' => '3']), 'Valid page numbers should be returned unchanged');
}

function testReadIdentifiers(): void
{
    assertSame('Topic-123', Forum_RequestHelper::readTopicId(['topicId' => ' Topic-123 ']), 'Topic identifiers should be trimmed and sanitised');
    assertSame('Comment_ABC', Forum_RequestHelper::readCommentId(['id' => 'Comment_ABC!!']), 'Comment identifiers should drop unsupported characters');
    $longId = str_repeat('a', 80);
    assertSame(str_repeat('a', 64), Forum_RequestHelper::readCommentId(['id' => $longId]), 'Identifiers should be clamped to 64 characters');
}

function testFeedFlagAndJsonDetection(): void
{
    assertTrue(Forum_RequestHelper::readFeedFlag(['feed' => 'YES']), 'Feed flags should compare case-insensitively to the truthy value');
    assertFalse(Forum_RequestHelper::readFeedFlag(['feed' => 'no']), 'Feed flags should respect non-truthy values');
    assertTrue(Forum_RequestHelper::wantsJson(['xn_out' => 'JSON']), 'JSON detection should be case-insensitive');
    assertFalse(Forum_RequestHelper::wantsJson(['xn_out' => 'html']), 'Non-JSON formats should return false');
}

function testReadRedirectTarget(): void
{
    $originalHost = $_SERVER['HTTP_HOST'] ?? null;
    $_SERVER['HTTP_HOST'] = 'example.com';

    assertSame('/forum', Forum_RequestHelper::readRedirectTarget(['next' => ' https://example.com/forum '], 'next'), 'Matching hosts should return a relative path');
    assertSame('/forum/topic', Forum_RequestHelper::readRedirectTarget(['next' => '/forum/topic'], 'next'), 'Relative paths should be preserved');
    assertSame(null, Forum_RequestHelper::readRedirectTarget(['next' => 'https://attacker.test/path'], 'next'), 'External hosts should be rejected');

    if ($originalHost === null) {
        unset($_SERVER['HTTP_HOST']);
    } else {
        $_SERVER['HTTP_HOST'] = $originalHost;
    }
}

$tests = [
    'String trimming' => 'testReadStringTrims',
    'Content whitespace handling' => 'testReadContentPreservesWhitespace',
    'Boolean parsing' => 'testReadBoolean',
    'Integer parsing' => 'testReadInt',
    'Page normalisation' => 'testReadPage',
    'Identifier sanitisation' => 'testReadIdentifiers',
    'Feed flag and JSON detection' => 'testFeedFlagAndJsonDetection',
    'Redirect target normalisation' => 'testReadRedirectTarget',
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

echo "All forum request helper checks passed.\n";
