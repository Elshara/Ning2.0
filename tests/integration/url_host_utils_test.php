<?php
require_once __DIR__ . '/../../lib/NF/Url/HostUtils.php';

function assertSame($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new Exception($message . ' (expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . ')');
    }
}

function testBaseDomainDerivation(): void
{
    assertSame('example.com', nf_derive_base_domain('sub.example.com'), 'Second-level domains should collapse to the registrable domain');
    assertSame('bar.co.uk', nf_derive_base_domain('foo.bar.co.uk'), 'Multi-level public suffixes should retain the additional label');
    assertSame('example.co.nz', nf_derive_base_domain('example.co.nz'), 'Two-level registrable domains should be preserved');
    assertSame('localhost', nf_derive_base_domain('localhost'), 'Localhost should be returned as-is');
    assertSame('203.0.113.10', nf_derive_base_domain('203.0.113.10'), 'IP addresses should be returned as-is');
    assertSame('localhost', nf_derive_base_domain(''), 'Empty hosts default to localhost');
}

function testSlugDerivation(): void
{
    assertSame('network', nf_derive_slug_from_host('network.example.com', 'example.com'), 'Subdomain hosts should produce the final label');
    assertSame('foo', nf_derive_slug_from_host('foo.bar.co.uk', 'bar.co.uk'), 'Slugs should be derived from labels preceding the base domain');
    assertSame('network', nf_derive_slug_from_host('example.co.nz', 'example.co.nz'), 'Hosts that match the base domain should default to network');
    assertSame('network', nf_derive_slug_from_host('2001:db8::1', '2001:db8::1'), 'IP hosts should default to the network slug');
    assertSame('test', nf_derive_slug_from_host('TEST.Example.com', 'example.com'), 'Slugs should be normalised to lower-case');
    assertSame('custom-value', nf_derive_slug_from_host('custom_value.example.com', 'example.com'), 'Invalid characters should be normalised to hyphens');
}

function testIdnHostNormalisation(): void
{
    $idnHost = 'münich.example';
    $sanitised = nf_sanitize_detected_host($idnHost);

    if (function_exists('idn_to_ascii')) {
        assertSame('xn--mnich-kva.example', $sanitised, 'IDN hosts should be converted to punycode when intl is available');
        assertSame('xn--mnich-kva.example', nf_derive_base_domain($idnHost), 'Base domain detection should return punycode for IDN hosts');
        assertSame('community', nf_derive_slug_from_host('community.' . $idnHost, $idnHost), 'Slug detection should align with punycode base domains');
    } else {
        assertSame('localhost', $sanitised, 'Without IDN support the host falls back to localhost');
    }
}

$tests = [
    'Base domain derivation' => 'testBaseDomainDerivation',
    'Slug derivation' => 'testSlugDerivation',
    'IDN host normalisation' => 'testIdnHostNormalisation',
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

echo "All URL host utility checks passed.\n";
