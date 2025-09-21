<?php

final class Forum_RequestHelper
{
    private const BOOLEAN_TRUE_VALUES = ['1', 'true', 'on', 'yes'];
    private const IDENTIFIER_MAX_LENGTH = 64;
    private const QUERY_MAX_LENGTH = 255;

    public static function readString(
        array $source,
        string $key,
        string $default = '',
        bool $trim = true,
        ?int $maxLength = null
    ): string {
        $value = self::readOptionalString($source, $key, $trim, $maxLength);
        return $value ?? $default;
    }

    public static function readOptionalString(
        array $source,
        string $key,
        bool $trim = true,
        ?int $maxLength = null,
        bool $preserveWhitespace = false
    ): ?string {
        $value = self::readScalar($source, $key);
        if ($value === null) {
            return null;
        }

        $stringValue = $trim ? trim($value) : $value;
        $stringValue = self::stripControlCharacters($stringValue, $preserveWhitespace);
        if ($maxLength !== null && $maxLength >= 0) {
            $stringValue = mb_substr($stringValue, 0, $maxLength);
        }

        return $stringValue === '' ? null : $stringValue;
    }

    public static function readContent(array $source, string $key, string $default = ''): string
    {
        $value = self::readOptionalString($source, $key, false, null, true);
        return $value ?? $default;
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

    public static function readInt(
        array $source,
        string $key,
        int $default = 0,
        int $min = 0,
        ?int $max = null
    ): int {
        $value = self::readScalar($source, $key);
        if ($value === null || $value === '') {
            return $default;
        }

        if (!is_numeric($value)) {
            return $default;
        }

        $intValue = (int) $value;
        if ($intValue < $min) {
            $intValue = $min;
        }

        if ($max !== null && $intValue > $max) {
            $intValue = $max;
        }

        return $intValue;
    }

    public static function readPage(array $source, string $key = 'page'): int
    {
        $page = self::readInt($source, $key, 1, 1);
        return $page > 0 ? $page : 1;
    }

    public static function readTopicId(array $source, string $key = 'topicId'): ?string
    {
        return self::readIdentifier($source, $key);
    }

    public static function readCommentId(array $source, string $key = 'id'): ?string
    {
        return self::readIdentifier($source, $key);
    }

    public static function readCategoryId(array $source, string $key = 'categoryId'): ?string
    {
        return self::readIdentifier($source, $key);
    }

    public static function readFeedFlag(array $source, string $key = 'feed', string $truthyValue = 'yes'): bool
    {
        $value = self::readOptionalString($source, $key, true, 16);
        if ($value === null) {
            return false;
        }

        return mb_strtolower($value) === mb_strtolower($truthyValue);
    }

    public static function wantsJson(array $source, string $key = 'xn_out'): bool
    {
        $value = self::readOptionalString($source, $key, true, 16);
        if ($value === null) {
            return false;
        }

        return mb_strtolower($value) === 'json';
    }

    public static function readRedirectTarget(array $source, string $key): ?string
    {
        $value = self::readOptionalString($source, $key, true, self::QUERY_MAX_LENGTH);
        if ($value === null) {
            return null;
        }

        if (!class_exists('XG_HttpHelper')) {
            require_once dirname(__DIR__, 4) . '/lib/XG_HttpHelper.php';
        }

        return XG_HttpHelper::normalizeRedirectTarget($value);
    }

    private static function readIdentifier(array $source, string $key): ?string
    {
        $value = self::readOptionalString($source, $key, true, self::IDENTIFIER_MAX_LENGTH);
        if ($value === null) {
            return null;
        }

        $sanitized = preg_replace('/[^A-Za-z0-9:_-]/u', '', $value);
        if ($sanitized === null) {
            return null;
        }

        $sanitized = mb_substr($sanitized, 0, self::IDENTIFIER_MAX_LENGTH);
        return $sanitized === '' ? null : $sanitized;
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

    private static function stripControlCharacters(string $value, bool $preserveWhitespace): string
    {
        $pattern = $preserveWhitespace ? '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u' : '/[\x00-\x1F\x7F]/u';
        $clean = preg_replace($pattern, '', $value);

        return $clean === null ? '' : $clean;
    }
}
