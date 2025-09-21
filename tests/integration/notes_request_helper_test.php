<?php
require_once __DIR__ . '/../../widgets/notes/lib/helpers/Notes_RequestHelper.php';

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
    $source = ['title' => '  Example  '];
    assertSame('Example', Notes_RequestHelper::readString($source, 'title'), 'Strings should be trimmed by default');
    assertSame('fallback', Notes_RequestHelper::readString([], 'title', 'fallback'), 'Missing keys should return the provided default');
}

function testReadContentPreservesWhitespace(): void
{
    $source = ['content' => "  <p>Body</p>  "];
    assertSame("  <p>Body</p>  ", Notes_RequestHelper::readContent($source, 'content'), 'Content should preserve surrounding whitespace');
}

function testReadBoolean(): void
{
    assertTrue(Notes_RequestHelper::readBoolean(['flag' => 'YES'], 'flag'), 'Truthy values should be recognised case-insensitively');
    assertFalse(Notes_RequestHelper::readBoolean(['flag' => '0'], 'flag'), 'String zero should evaluate to false');
    assertFalse(Notes_RequestHelper::readBoolean(['flag' => ['unexpected']], 'flag'), 'Non-scalar values should be treated as false');
}

function testReadInt(): void
{
    assertSame(5, Notes_RequestHelper::readInt(['count' => '5'], 'count', 0, 0), 'Numeric strings should be cast to integers');
    assertSame(3, Notes_RequestHelper::readInt(['count' => '3.9'], 'count', 0, 0), 'Decimal strings should be truncated to integers');
    assertSame(1, Notes_RequestHelper::readInt(['count' => '-4'], 'count', 1, 1), 'The provided minimum should be enforced');
    assertSame(2, Notes_RequestHelper::readInt([], 'count', 2, 0), 'Missing values should return the default');
    assertSame(0, Notes_RequestHelper::readInt(['count' => 'invalid'], 'count', 0, 0), 'Non-numeric values should fall back to the default');
}

function testNormalizeSort(): void
{
    assertSame('alpha', Notes_RequestHelper::normalizeSort('Alpha'), 'Valid sorts should be normalised to lowercase');
    assertSame('created', Notes_RequestHelper::normalizeSort('created', 'updated'), 'Allowed values should be returned unchanged');
    assertSame('updated', Notes_RequestHelper::normalizeSort('unexpected'), 'Unexpected values should fall back to the default');
    assertSame('created', Notes_RequestHelper::normalizeSort(null, 'created'), 'Null values should return the supplied default when valid');
}

function testNormalizeFeedType(): void
{
    assertSame('featured', Notes_RequestHelper::normalizeFeedType('FEATURED'), 'Feed types should be normalised to lowercase');
    assertSame('recent', Notes_RequestHelper::normalizeFeedType('unsupported'), 'Unsupported feed types should fall back to "recent"');
    assertSame('all', Notes_RequestHelper::normalizeFeedType(null, 'all'), 'Null values should honour the supplied default when allowed');
    assertSame('recent', Notes_RequestHelper::normalizeFeedType(null, 'invalid'), 'Invalid defaults should be coerced to "recent"');
}

function testNormalizeDisplay(): void
{
    $allowed = ['details', 'titles', 'note'];
    assertSame('titles', Notes_RequestHelper::normalizeDisplay(' Titles ', $allowed, 'details'), 'Display values should be trimmed and validated');
    assertSame('details', Notes_RequestHelper::normalizeDisplay('unknown', $allowed, 'details'), 'Invalid display values should fall back to the default');
    assertSame('details', Notes_RequestHelper::normalizeDisplay(null, $allowed, 'invalid'), 'Invalid defaults should fall back to the first allowed value');
}

function testNormalizeHomepageFrom(): void
{
    assertSame('featured', Notes_RequestHelper::normalizeHomepageFrom(' FEATURED '), 'Homepage origin values should be trimmed and validated');
    assertSame('updated', Notes_RequestHelper::normalizeHomepageFrom('unsupported'), 'Unsupported origin values should fall back to "updated"');
    assertSame('updated', Notes_RequestHelper::normalizeHomepageFrom(null), 'Missing origin values should fall back to "updated"');
}

function testReadNoteKey(): void
{
    assertSame('Note-One', Notes_RequestHelper::readNoteKey(['noteKey' => ' Note-One ']), 'Note keys should be trimmed when read');
    assertSame('', Notes_RequestHelper::readNoteKey(['noteKey' => ['invalid']]), 'Non-scalar note keys should fall back to an empty string');
}

$tests = [
    'String trimming' => 'testReadStringTrims',
    'Content whitespace handling' => 'testReadContentPreservesWhitespace',
    'Boolean parsing' => 'testReadBoolean',
    'Integer parsing' => 'testReadInt',
    'Sort normalization' => 'testNormalizeSort',
    'Feed type normalization' => 'testNormalizeFeedType',
    'Display normalization' => 'testNormalizeDisplay',
    'Homepage origin normalization' => 'testNormalizeHomepageFrom',
    'Note key reading' => 'testReadNoteKey',
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

echo "All Notes request helper checks passed.\n";
