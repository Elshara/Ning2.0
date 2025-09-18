<?php
class W_Cache
{
    private static $widgets = [];
    private static $current = [];

    public static function getWidget(string $name): W_Widget
    {
        if (!isset(self::$widgets[$name])) {
            self::$widgets[$name] = new W_Widget($name);
        }
        self::$current['W_Widget'] = self::$widgets[$name];
        return self::$widgets[$name];
    }

    public static function current(?string $key = null)
    {
        if ($key === 'W_Widget') {
            if (!isset(self::$current['W_Widget'])) {
                self::$current['W_Widget'] = self::getWidget('main');
            }
            return self::$current['W_Widget'];
        }
        return self::$current[$key] ?? null;
    }

    public static function setWidget(string $name, W_Widget $widget): void
    {
        self::$widgets[$name] = $widget;
    }
}
