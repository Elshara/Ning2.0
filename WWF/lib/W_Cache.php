<?php
/**
 * Extremely small cache that keeps widget instances alive for the duration of
 * the request.
 */
class W_Cache
{
    /** @var array<string,W_Widget> */
    protected static array $widgets = [];

    /** @var array */
    protected static array $stack = [];

    /** @var array<string,string> */
    protected static array $classes = ['app' => 'XG_App'];

    public static function getWidget(string $name): W_Widget
    {
        if (!isset(self::$widgets[$name])) {
            self::$widgets[$name] = W_BaseWidget::factory($name);
        }
        return self::$widgets[$name];
    }

    public static function getClass(string $type): ?string
    {
        return self::$classes[$type] ?? null;
    }

    public static function putClass($type, $class): void
    {
        self::$classes[$type] = $class;
    }

    public static function push(W_BaseWidget $widget): void
    {
        self::$stack[] = ['W_Widget' => $widget];
    }

    public static function pop(): void
    {
        array_pop(self::$stack);
    }

    public static function current(string $type)
    {
        if (!$type) {
            return null;
        }
        $frame = end(self::$stack);
        if ($frame && isset($frame[$type])) {
            return $frame[$type];
        }
        return null;
    }

    public static function clear(): void
    {
        self::$widgets = [];
        self::$stack = [];
    }
}
