<?php

if (!class_exists('XG_App')) {
    class XG_App
    {
        public static function includeFileOnce(string $path): void
        {
            // No-op in integration tests.
        }
    }
}

if (!class_exists('XG_DateHelper')) {
    class XG_DateHelper
    {
        public static function format(string $format, string $date, string $offset = ''): string
        {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                return $date;
            }

            if ($offset !== '') {
                $timestamp = strtotime($offset, $timestamp);
            }

            return date($format, $timestamp);
        }
    }
}

if (!class_exists('XG_HttpHelper')) {
    class XG_HttpHelper
    {
        public static function normalizeRedirectTarget($target): ?string
        {
            if (!is_scalar($target)) {
                return null;
            }

            $value = trim((string) $target);
            if ($value === '') {
                return null;
            }

            if (preg_match('#^https?://#i', $value)) {
                $parts = parse_url($value);
                if ($parts === false) {
                    return null;
                }

                $host = strtolower($parts['host'] ?? '');
                $currentHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
                if ($host === '' || ($currentHost !== '' && $host !== $currentHost)) {
                    return null;
                }

                $path = $parts['path'] ?? '/';
                $query = isset($parts['query']) ? '?' . $parts['query'] : '';
                $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
                return $path . $query . $fragment;
            }

            return str_starts_with($value, '/') ? $value : null;
        }
    }
}
require_once __DIR__ . '/../../widgets/events/lib/helpers/Events_RequestHelper.php';

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

function testReadStringTrimsAndClamps(): void
{
    assertSame('hello', Events_RequestHelper::readString(['name' => '  hello  '], 'name'), 'Strings should be trimmed by default');
    assertSame('fallback', Events_RequestHelper::readString([], 'name', 'fallback'), 'Missing values should return the provided default');
    assertSame('clip', Events_RequestHelper::readString(['value' => ' clipping '], 'value', '', true, 4), 'Max length should clamp the result');
}

function testReadOptionalString(): void
{
    assertNull(Events_RequestHelper::readOptionalString([], 'missing'), 'Missing optional strings should return null');
    assertSame('abc', Events_RequestHelper::readOptionalString(['value' => 'abc'], 'value'), 'Optional strings should return their value when present');
}

function testReadBoolean(): void
{
    assertTrue(Events_RequestHelper::readBoolean(['flag' => 'YES'], 'flag'), 'Truthy values should be accepted case-insensitively');
    assertFalse(Events_RequestHelper::readBoolean(['flag' => 'no'], 'flag'), 'Explicit false values should be rejected');
    assertFalse(Events_RequestHelper::readBoolean(['flag' => ['array']], 'flag'), 'Non-scalar values should be treated as false');
}

function testReadIntAndPage(): void
{
    assertSame(5, Events_RequestHelper::readInt(['count' => '5'], 'count'), 'Numeric strings should be converted to integers');
    assertSame(10, Events_RequestHelper::readInt(['count' => '20'], 'count', 0, 0, 10), 'Maximum bounds should be enforced');
    assertSame(1, Events_RequestHelper::readPage(['page' => '0']), 'Pages lower than one should clamp to one');
}

function testReadIdentifiers(): void
{
    assertSame('12345', Events_RequestHelper::readEventId(['id' => '12345']), 'Event identifiers should be returned as strings');
    assertNull(Events_RequestHelper::readEventId(['id' => []]), 'Non-scalar identifiers should be rejected');
    assertSame('inv-1', Events_RequestHelper::readInvitationId(['invitationId' => 'inv-1']), 'Invitation identifiers should pass through as strings');
    assertSame('abc', Events_RequestHelper::readEventId(['eventId' => 'abc'], 'eventId'), 'Event identifiers should support alternate keys');
    assertSame('user_name', Events_RequestHelper::readScreenName(['user' => ' user_name ']), 'Screen names should be trimmed');
}

function testReadDateAndMonth(): void
{
    assertSame('2024-05-10', Events_RequestHelper::readDate(['date' => '2024-05-10']), 'Valid dates should be formatted consistently');
    assertNull(Events_RequestHelper::readDate(['date' => 'invalid']), 'Invalid dates should return null');
    assertSame('2024-05', Events_RequestHelper::readMonth(['current' => '2024-05-20']), 'Months should be normalised to year-month format');
}

function testReadDirectionAndEmbed(): void
{
    assertSame('forward', Events_RequestHelper::readDirection([], 'direction'), 'Missing directions should default to forward');
    assertSame('backward', Events_RequestHelper::readDirection(['direction' => 'BACKWARD'], 'direction'), 'Direction values should be normalised');
    assertTrue(Events_RequestHelper::readEmbedFlag(['embed' => '1'], 'embed'), 'Embed flags should accept truthy values');
}

function testReadRedirectTarget(): void
{
    $originalHost = $_SERVER['HTTP_HOST'] ?? null;
    $_SERVER['HTTP_HOST'] = 'example.com';

    assertSame('/events', Events_RequestHelper::readRedirectTarget(['next' => '/events'], 'next'), 'Relative paths should be preserved');
    assertSame('/events', Events_RequestHelper::readRedirectTarget(['next' => 'https://example.com/events'], 'next'), 'Absolute URLs on the current host should be normalised to relative paths');
    assertNull(Events_RequestHelper::readRedirectTarget(['next' => 'https://attacker.test/evil'], 'next'), 'External hosts should be rejected');

    if ($originalHost === null) {
        unset($_SERVER['HTTP_HOST']);
    } else {
        $_SERVER['HTTP_HOST'] = $originalHost;
    }
}

function testReadRsvpAndMessage(): void
{
    assertSame('attending', Events_RequestHelper::readRsvp(['rsvp' => 'attending']), 'RSVP values should be returned as strings');
    assertSame('Message body', Events_RequestHelper::readMessage(['message' => "Message body\n"], 'message'), 'Messages should be trimmed and sanitised');
}

function testReadFieldKey(): void
{
    assertSame('type', Events_RequestHelper::readFieldKey(['field' => 'TYPE'], 'field'), 'Field keys should be normalised when supported');
    assertNull(Events_RequestHelper::readFieldKey(['field' => 'unsupported'], 'field'), 'Unsupported field keys should return null');
}

function runAllTests(): void
{
    testReadStringTrimsAndClamps();
    testReadOptionalString();
    testReadBoolean();
    testReadIntAndPage();
    testReadIdentifiers();
    testReadDateAndMonth();
    testReadDirectionAndEmbed();
    testReadRedirectTarget();
    testReadRsvpAndMessage();
    testReadFieldKey();
}

runAllTests();
