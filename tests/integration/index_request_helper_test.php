<?php
require_once __DIR__ . '/../../widgets/index/lib/helpers/Index_RequestHelper.php';

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

function assertNull($value, string $message): void
{
    if ($value !== null) {
        throw new Exception($message . ' (expected null, got ' . var_export($value, true) . ')');
    }
}

function testReadStringTrimsAndLimits(): void
{
    $source = ['title' => '  Hello World  '];
    assertSame('Hello World', Index_RequestHelper::readString($source, 'title'), 'Strings should be trimmed');
    assertSame('fallback', Index_RequestHelper::readString([], 'title', 'fallback'), 'Missing keys should fall back to default values');
    assertSame('clip', Index_RequestHelper::readString(['value' => ' clipped '], 'value', '', true, 4), 'Max length should constrain the result');
}

function testReadContentPreservesWhitespace(): void
{
    $source = ['body' => "  <p>Content</p>  "];
    assertSame("  <p>Content</p>  ", Index_RequestHelper::readContent($source, 'body'), 'Content should not be trimmed');
}

function testReadBoolean(): void
{
    assertTrue(Index_RequestHelper::readBoolean(['flag' => 'YES'], 'flag'), 'Truthy values should be recognised case-insensitively');
    assertFalse(Index_RequestHelper::readBoolean(['flag' => 'no'], 'flag'), 'Explicit false values should be rejected');
    assertFalse(Index_RequestHelper::readBoolean(['flag' => ['array']], 'flag'), 'Non-scalar values should be treated as false');
}

function testReadIntBounds(): void
{
    assertSame(5, Index_RequestHelper::readInt(['count' => '5'], 'count'), 'Numeric strings should be cast to integers');
    assertSame(10, Index_RequestHelper::readInt(['count' => '20'], 'count', 0, 0, 10), 'Maximum bounds should be enforced');
    assertSame(0, Index_RequestHelper::readInt(['count' => '-2'], 'count', 3, 0), 'Minimum bounds should be enforced');
    assertSame(7, Index_RequestHelper::readInt([], 'count', 7), 'Missing values should fall back to the provided default');
}

function testReadEnum(): void
{
    assertSame('private', Index_RequestHelper::readEnum(['mode' => 'PRIVATE'], 'mode', ['public', 'private']), 'Valid options should be normalised to canonical casing');
    assertSame('public', Index_RequestHelper::readEnum([], 'mode', ['public', 'private'], 'public'), 'Defaults should be honoured when provided');
    assertNull(Index_RequestHelper::readEnum(['mode' => 'unsupported'], 'mode', ['public', 'private']), 'Unsupported values should return null when no default is supplied');
}

function testReadRange(): void
{
    [$start, $end] = Index_RequestHelper::readRange(['start' => '10', 'end' => '40'], 'start', 'end', 25, 50);
    assertSame(10, $start, 'Start should be parsed as an integer');
    assertSame(40, $end, 'Explicit end values should be preserved when within the window limit');

    [$start, $end] = Index_RequestHelper::readRange(['start' => '5'], 'start', 'end', 20, 15);
    assertSame(5, $start, 'Missing end values should start from zero');
    assertSame(20, $end, 'Missing end values should use the default window length');

    [$start, $end] = Index_RequestHelper::readRange(['start' => '0', 'end' => '500'], 'start', 'end', 50, 100);
    assertSame(100, $end, 'Windows larger than the allowed maximum should be clamped');
}

function testReadRedirectTarget(): void
{
    $originalHttpHost = $_SERVER['HTTP_HOST'] ?? null;
    $originalServerName = $_SERVER['SERVER_NAME'] ?? null;
    $_SERVER['HTTP_HOST'] = 'example.com';
    $_SERVER['SERVER_NAME'] = 'example.com';

    assertSame('/path', Index_RequestHelper::readRedirectTarget(['next' => '/path'], 'next'), 'Relative paths should be returned as-is when valid');
    assertSame('/secure', Index_RequestHelper::readRedirectTarget(['next' => 'https://example.com/secure'], 'next'), 'Absolute URLs on the current host should be normalised to relative paths');
    assertNull(Index_RequestHelper::readRedirectTarget(['next' => 'https://attacker.test/evil'], 'next'), 'External hosts should be rejected');

    if ($originalHttpHost === null) {
        unset($_SERVER['HTTP_HOST']);
    } else {
        $_SERVER['HTTP_HOST'] = $originalHttpHost;
    }

    if ($originalServerName === null) {
        unset($_SERVER['SERVER_NAME']);
    } else {
        $_SERVER['SERVER_NAME'] = $originalServerName;
    }
}

function testReadContentId(): void
{
    assertSame('123-abc:XYZ', Index_RequestHelper::readContentId(['id' => ' 123-abc:XYZ '], 'id'), 'Content identifiers should preserve allowed characters');
    assertSame('token', Index_RequestHelper::readContentId(['id' => "token<script>"], 'id'), 'Unsafe characters should be stripped out');
    assertSame('', Index_RequestHelper::readContentId([], 'id'), 'Missing identifiers should return an empty string');
}

function testReadStringStripsControlCharacters(): void
{
    $source = ['value' => "Line\x00break\x1Ftest"];
    assertSame('Linebreaktest', Index_RequestHelper::readString($source, 'value', '', false), 'Control characters should be removed from strings');
}

function testReadContentIdClampsLength(): void
{
    assertSame('short', Index_RequestHelper::readContentId(['id' => 'short-and-long'], 'id', 5), 'Content identifiers should respect the provided maximum length');
}

$tests = [
    'String trimming and limits' => 'testReadStringTrimsAndLimits',
    'Content whitespace handling' => 'testReadContentPreservesWhitespace',
    'Boolean parsing' => 'testReadBoolean',
    'Integer bounds enforcement' => 'testReadIntBounds',
    'Enum parsing' => 'testReadEnum',
    'Pagination range handling' => 'testReadRange',
    'Redirect target normalisation' => 'testReadRedirectTarget',
    'Content identifier sanitisation' => 'testReadContentId',
    'Control character stripping' => 'testReadStringStripsControlCharacters',
    'Content identifier length clamp' => 'testReadContentIdClampsLength',
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

echo "All Index request helper checks passed.\n";
