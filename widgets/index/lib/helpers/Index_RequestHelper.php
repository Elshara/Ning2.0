<?php

final class Index_RequestHelper
{
    private const BOOLEAN_TRUE_VALUES = ['1', 'true', 'on', 'yes'];

    public static function readString(
        array $source,
        string $key,
        string $default = '',
        bool $trim = true,
        ?int $maxLength = null
    ): string {
        $value = self::readScalar($source, $key);
        if ($value === null) {
            return $default;
        }

        $stringValue = $trim ? trim($value) : $value;
        $stringValue = self::stripControlCharacters($stringValue);
        if ($maxLength !== null && $maxLength >= 0) {
            $stringValue = mb_substr($stringValue, 0, $maxLength);
        }

        return $stringValue;
    }

    public static function readContent(array $source, string $key, string $default = ''): string
    {
        return self::readString($source, $key, $default, false);
    }

    public static function readBoolean(array $source, string $key): bool
    {
        $value = self::readScalar($source, $key);
        if ($value === null) {
            return false;
        }

        return in_array(mb_strtolower(trim($value)), self::BOOLEAN_TRUE_VALUES, true);
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

    public static function readEnum(array $source, string $key, array $allowed, ?string $default = null): ?string
    {
        if ($allowed === []) {
            return $default;
        }

        $normalisedAllowed = [];
        foreach ($allowed as $option) {
            $stringOption = (string) $option;
            $normalisedAllowed[mb_strtolower($stringOption)] = $stringOption;
        }

        $value = self::readScalar($source, $key);
        if ($value !== null) {
            $candidate = mb_strtolower(trim($value));
            if (array_key_exists($candidate, $normalisedAllowed)) {
                return $normalisedAllowed[$candidate];
            }
        }

        if ($default === null) {
            return null;
        }

        $defaultKey = mb_strtolower($default);
        if (array_key_exists($defaultKey, $normalisedAllowed)) {
            return $normalisedAllowed[$defaultKey];
        }

        $firstKey = array_key_first($normalisedAllowed);
        return $firstKey === null ? null : $normalisedAllowed[$firstKey];
    }

    public static function readRange(
        array $source,
        string $startKey,
        string $endKey,
        int $defaultWindow = 100,
        int $maxWindow = 500
    ): array {
        $start = self::readInt($source, $startKey, 0, 0);
        $window = max($defaultWindow, 0);
        $endDefault = $start + $window;
        $end = self::readInt($source, $endKey, $endDefault, 0);

        if ($end < $start) {
            $end = $start;
        }

        $maxWindow = max($maxWindow, 0);
        if ($maxWindow > 0 && ($end - $start) > $maxWindow) {
            $end = $start + $maxWindow;
        }

        return [$start, $end];
    }

    public static function readRedirectTarget(array $source, string $key): ?string
    {
        $value = self::readScalar($source, $key);
        if ($value === null) {
            return null;
        }

        if (!class_exists('XG_HttpHelper')) {
            require_once dirname(__DIR__, 4) . '/lib/XG_HttpHelper.php';
        }
        return XG_HttpHelper::normalizeRedirectTarget($value);
    }

    public static function readContentId(array $source, string $key, int $maxLength = 64): string
    {
        $value = self::readString($source, $key);
        if ($value === '') {
            return '';
        }

        if (!preg_match('/^([A-Za-z0-9:_-]+)/u', $value, $matches)) {
            return '';
        }

        $sanitised = $matches[1];

        if ($maxLength > 0) {
            $sanitised = mb_substr($sanitised, 0, $maxLength);
        }

        return $sanitised;
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

    private static function stripControlCharacters(string $value): string
    {
        $clean = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);

        return $clean === null ? '' : $clean;
    }
}
