<?php

final class Notes_RequestHelper
{
    private const SORT_OPTIONS = ['updated', 'created', 'alpha'];
    private const FEED_TYPES = ['featured', 'all'];
    private const BOOLEAN_TRUE_VALUES = ['1', 'true', 'on', 'yes'];

    public static function readString(array $source, string $key, string $default = '', bool $trim = true): string
    {
        $value = self::readScalar($source, $key);
        if ($value === null) {
            return $default;
        }

        $stringValue = (string) $value;
        return $trim ? trim($stringValue) : $stringValue;
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

        $normalized = mb_strtolower(trim((string) $value));
        return in_array($normalized, self::BOOLEAN_TRUE_VALUES, true);
    }

    public static function readInt(array $source, string $key, int $default = 0, int $min = 0): int
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

        return $intValue;
    }

    public static function readNoteKey(array $source): string
    {
        return self::readString($source, 'noteKey');
    }

    public static function normalizeSort(?string $sort, string $default = 'updated'): string
    {
        $default = self::normalizeSortDefault($default);
        if ($sort === null) {
            return $default;
        }

        $normalized = mb_strtolower(trim($sort));
        if (in_array($normalized, self::SORT_OPTIONS, true)) {
            return $normalized;
        }

        return $default;
    }

    public static function normalizeFeedType(?string $type, string $default = 'recent'): string
    {
        $normalizedDefault = mb_strtolower(trim($default));
        if (!in_array($normalizedDefault, ['recent', ...self::FEED_TYPES], true)) {
            $normalizedDefault = 'recent';
        }

        if ($type === null) {
            return $normalizedDefault;
        }

        $normalized = mb_strtolower(trim($type));
        if (in_array($normalized, self::FEED_TYPES, true)) {
            return $normalized;
        }

        return $normalizedDefault;
    }

    public static function normalizeDisplay(?string $value, array $allowed, string $default): string
    {
        $allowedLower = array_map(static function ($item) {
            return mb_strtolower((string) $item);
        }, $allowed);
        $defaultNormalized = mb_strtolower(trim($default));
        if (!in_array($defaultNormalized, $allowedLower, true)) {
            $defaultNormalized = $allowedLower[0] ?? '';
        }

        if ($value === null) {
            return $defaultNormalized;
        }

        $normalized = mb_strtolower(trim($value));
        return in_array($normalized, $allowedLower, true) ? $normalized : $defaultNormalized;
    }

    public static function normalizeHomepageFrom(?string $value): string
    {
        $allowed = ['featured', 'updated', 'created'];
        if ($value === null) {
            return 'updated';
        }

        $normalized = mb_strtolower(trim($value));
        return in_array($normalized, $allowed, true) ? $normalized : 'updated';
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

    private static function normalizeSortDefault(string $default): string
    {
        $normalizedDefault = mb_strtolower(trim($default));
        if (in_array($normalizedDefault, self::SORT_OPTIONS, true)) {
            return $normalizedDefault;
        }

        return 'updated';
    }
}
