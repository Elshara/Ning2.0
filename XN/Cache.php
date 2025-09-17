<?php
class XN_Cache
{
    private static array $store = [];

    public static function get($key, $ttl = null)
    {
        return self::$store[$key] ?? null;
    }

    public static function insert($key, $value, $labels = null): void
    {
        self::$store[$key] = $value;
    }

    public static function put($key, $value, $labels = null): void
    {
        self::$store[$key] = $value;
    }

    public static function remove($key): void
    {
        unset(self::$store[$key]);
    }
}
