<?php

final class Events_RequestHelper
{
    private const BOOLEAN_TRUE_VALUES = ['1', 'true', 'yes', 'on'];
    private const SCREEN_NAME_MAX_LENGTH = 64;
    private const EVENT_ID_MAX_LENGTH = 64;
    private const INVITATION_ID_MAX_LENGTH = 64;
    private const MAX_QUERY_LENGTH = 255;

    public static function readString(
        array $source,
        string $key,
        string $default = '',
        bool $trim = true,
        int $maxLength = 255
    ): string {
        $value = self::readScalar($source, $key);
        if ($value === null) {
            return $default;
        }

        $normalized = self::sanitizeString($value, $trim, $maxLength);
        if ($normalized === '') {
            return $default;
        }

        return $normalized;
    }

    public static function readOptionalString(
        array $source,
        string $key,
        bool $trim = true,
        int $maxLength = 255
    ): ?string {
        $value = self::readScalar($source, $key);
        if ($value === null) {
            return null;
        }

        $normalized = self::sanitizeString($value, $trim, $maxLength);
        return $normalized === '' ? null : $normalized;
    }

    public static function readBoolean(array $source, string $key): bool
    {
        $value = self::readScalar($source, $key);
        if ($value === null) {
            return false;
        }

        $normalized = mb_strtolower(trim($value));
        return in_array($normalized, self::BOOLEAN_TRUE_VALUES, true);
    }

    public static function readInt(array $source, string $key, int $default = 0, int $min = 0, ?int $max = null): int
    {
        $value = self::readScalar($source, $key);
        if ($value === null || $value === '') {
            return $default;
        }

        if (!is_numeric($value)) {
            return $default;
        }

        $intValue = (int) $value;
        if ($intValue < $min) {
            return $min;
        }

        if ($max !== null && $intValue > $max) {
            return $max;
        }

        return $intValue;
    }

    public static function readPage(array $source, string $key = 'page'): int
    {
        $page = self::readInt($source, $key, 1, 1);
        return $page > 0 ? $page : 1;
    }

    public static function readEventId(array $source, string $key = 'id'): ?string
    {
        return self::readOptionalString($source, $key, true, self::EVENT_ID_MAX_LENGTH);
    }

    public static function readInvitationId(array $source, string $key = 'invitationId'): ?string
    {
        return self::readOptionalString($source, $key, true, self::INVITATION_ID_MAX_LENGTH);
    }

    public static function readScreenName(array $source, string $key = 'user'): ?string
    {
        return self::readOptionalString($source, $key, true, self::SCREEN_NAME_MAX_LENGTH);
    }

    public static function readDate(array $source, string $key = 'date'): ?string
    {
        $value = self::readOptionalString($source, $key);
        if ($value === null) {
            return null;
        }

        XG_App::includeFileOnce('/lib/XG_DateHelper.php');
        $formatted = XG_DateHelper::format('Y-m-d', $value);
        return preg_match('/^\d{4}-\d{2}-\d{2}$/u', $formatted) ? $formatted : null;
    }

    public static function readMonth(array $source, string $key = 'current'): ?string
    {
        $value = self::readOptionalString($source, $key);
        if ($value === null) {
            return null;
        }

        XG_App::includeFileOnce('/lib/XG_DateHelper.php');
        $formatted = XG_DateHelper::format('Y-m', $value);
        return preg_match('/^\d{4}-\d{2}$/u', $formatted) ? $formatted : null;
    }

    public static function readDirection(array $source, string $key = 'direction', string $default = 'forward'): string
    {
        $value = self::readOptionalString($source, $key);
        if ($value === null) {
            return in_array($default, ['forward', 'backward'], true) ? $default : 'forward';
        }

        $normalized = mb_strtolower($value);
        if (!in_array($normalized, ['forward', 'backward'], true)) {
            return in_array($default, ['forward', 'backward'], true) ? $default : 'forward';
        }

        return $normalized;
    }

    public static function readRedirectTarget(array $source, string $key, ?string $default = null): ?string
    {
        $value = self::readOptionalString($source, $key, true, self::MAX_QUERY_LENGTH);
        if ($value === null) {
            return $default;
        }

        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        $normalized = XG_HttpHelper::normalizeRedirectTarget($value);
        return $normalized !== null ? $normalized : $default;
    }

    public static function readQuery(array $source, string $key = 'q'): string
    {
        return self::readString($source, $key, '', true, self::MAX_QUERY_LENGTH);
    }

    public static function readFieldKey(array $source, string $key = 'field'): ?string
    {
        $value = self::readOptionalString($source, $key);
        if ($value === null) {
            return null;
        }

        $normalized = mb_strtolower($value);
        $allowed = ['type', 'location'];
        foreach ($allowed as $option) {
            if ($normalized === $option) {
                return $option;
            }
        }

        return null;
    }

    public static function readEmbedFlag(array $source, string $key = 'embed'): bool
    {
        return self::readBoolean($source, $key);
    }

    public static function readRsvp(array $source, string $key = 'rsvp'): ?string
    {
        return self::readOptionalString($source, $key, true, 32);
    }

    public static function readMessage(array $source, string $key = 'message', int $maxLength = 200): string
    {
        return self::readString($source, $key, '', true, $maxLength);
    }

    private static function readScalar(array $source, string $key): ?string
    {
        if (!array_key_exists($key, $source)) {
            return null;
        }

        $value = $source[$key];
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return null;
    }

    private static function sanitizeString(string $value, bool $trim, int $maxLength): string
    {
        $sanitized = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        if ($sanitized === null) {
            $sanitized = '';
        }

        if ($trim) {
            $sanitized = trim($sanitized);
        }

        if ($maxLength > 0 && mb_strlen($sanitized) > $maxLength) {
            $sanitized = mb_substr($sanitized, 0, $maxLength);
        }

        return $sanitized;
    }
}
