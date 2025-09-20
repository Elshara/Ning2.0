<?php
require_once __DIR__ . '/../../lib/XG_HttpHelper.php';

function assertSame($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new Exception($message . ' (expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . ')');
    }
}

function assertNull($actual, string $message): void
{
    if (! is_null($actual)) {
        throw new Exception($message . ' (expected null, got ' . var_export($actual, true) . ')');
    }
}

function setServerHost(string $host): void
{
    $_SERVER['HTTP_HOST'] = $host;
    $hostParts = explode(':', $host, 2);
    $_SERVER['SERVER_NAME'] = $hostParts[0];
    $_SERVER['SERVER_ADDR'] = '203.0.113.5';
}

function testRelativeNormalization(): void
{
    setServerHost('example.com');
    assertSame('/dashboard', XG_HttpHelper::normalizeRedirectTarget('/dashboard'), 'Relative paths should be preserved');
    assertSame('/settings', XG_HttpHelper::normalizeRedirectTarget('settings'), 'Paths without a leading slash should be prefixed');
    assertSame('/?saved=1', XG_HttpHelper::normalizeRedirectTarget('?saved=1'), 'Query-only targets should default to the root path');
    assertSame('/inbox/compose', XG_HttpHelper::normalizeRedirectTarget("/inbox/compose\n"), 'Control characters should be stripped');
}

function testAbsoluteNormalization(): void
{
    setServerHost('example.com');
    assertSame('/members', XG_HttpHelper::normalizeRedirectTarget('https://example.com/members'), 'Absolute URLs on the current host should become relative');
    assertSame('/members?sort=recent', XG_HttpHelper::normalizeRedirectTarget('HTTP://EXAMPLE.COM/members?sort=recent'), 'Absolute URLs should be case-insensitive for scheme and host');
    assertNull(XG_HttpHelper::normalizeRedirectTarget('https://example.net/members'), 'External hosts should be rejected');

    setServerHost('example.com:8080');
    assertSame('/updates', XG_HttpHelper::normalizeRedirectTarget('https://example.com:8080/updates'), 'Absolute URLs matching the configured port should be accepted');
    assertNull(XG_HttpHelper::normalizeRedirectTarget('https://example.com:9090/updates'), 'Ports not matching the current host should be rejected');
}

function testInvalidTargets(): void
{
    setServerHost('example.com');
    assertNull(XG_HttpHelper::normalizeRedirectTarget('javascript:alert(1)'), 'Javascript URLs must be rejected');
    assertNull(XG_HttpHelper::normalizeRedirectTarget('data:text/plain,hi'), 'Data URLs must be rejected');
    assertNull(XG_HttpHelper::normalizeRedirectTarget(array('path')), 'Non-scalar values should be rejected');
    assertNull(XG_HttpHelper::normalizeRedirectTarget(''), 'Empty strings should be rejected');
}

$tests = [
    'Relative redirects' => 'testRelativeNormalization',
    'Absolute redirects' => 'testAbsoluteNormalization',
    'Invalid redirect inputs' => 'testInvalidTargets',
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

echo "All HTTP helper redirect checks passed.\n";
