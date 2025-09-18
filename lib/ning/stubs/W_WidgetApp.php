<?php
class W_WidgetApp
{
    protected static $included = [];

    public static function includeFileOnce($path, $fatalOnMissing = true)
    {
        $base = defined('NF_APP_BASE') ? NF_APP_BASE : dirname(__DIR__, 3);
        $fullPath = $base . $path;
        if ($path === '/lib/XG_App.php' && class_exists('XG_App', false)) {
            return true;
        }
        if (!file_exists($fullPath)) {
            if ($fatalOnMissing) {
                throw new Exception("Unable to include required file: {$fullPath}");
            }
            return false;
        }
        if (!isset(self::$included[$fullPath])) {
            self::$included[$fullPath] = true;
            require_once $fullPath;
        }
        return true;
    }

    public static function includePrefix(): string
    {
        return defined('NF_APP_BASE') ? NF_APP_BASE : dirname(__DIR__, 3);
    }

    public static function getInstances(): array
    {
        return [];
    }

    public static function getInstanceIdentifier($dir)
    {
        return $dir;
    }

    public static function getInstanceDirectory($identifier)
    {
        return self::includePrefix() . '/instances/' . $identifier . '/';
    }

    public static function go(): void
    {
        // No-op in the compatibility layer.
    }
}
