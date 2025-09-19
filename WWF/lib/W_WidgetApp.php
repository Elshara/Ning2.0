<?php
/**
 * Bootstrapper responsible for loading widget configuration and dispatching
 * requests.  The implementation is intentionally lightweight – it only
 * provides the behaviour needed by the development exercises.
 */
class W_WidgetApp
{
    protected static array $included = [];
    protected static array $definitions = [];

    public static function includePrefix()
    {
        if (defined('W_INCLUDE_PREFIX')) {
            return W_INCLUDE_PREFIX;
        }
        return dirname(__DIR__, 1);
    }

    public static function includeFileOnce($path, $usePrefix = true)
    {
        $fullPath = $usePrefix ? self::includePrefix() . $path : $path;
        if (!isset(self::$included[$fullPath])) {
            require_once $fullPath;
            self::$included[$fullPath] = true;
        }
    }

    public static function includeFile($path, $usePrefix = true)
    {
        $fullPath = $usePrefix ? self::includePrefix() . $path : $path;
        require $fullPath;
    }

    public static function composeRequest($widget, $controller, $action, $query = null)
    {
        $url = sprintf('/%s/%s/%s', rawurlencode($widget), rawurlencode($controller), rawurlencode($action));
        if (is_array($query) && $query) {
            $url .= '?' . http_build_query($query);
        } elseif (is_string($query) && strlen($query)) {
            $url .= '?' . ltrim($query, '?');
        }
        return $url;
    }

    public static function routeRequest()
    {
        $widget = $_GET['widget'] ?? null;
        $controller = $_GET['controller'] ?? null;
        $action = $_GET['action'] ?? null;

        if (!$widget || !$controller || !$action) {
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
            $segments = array_values(array_filter(explode('/', $path)));
            $widget = $widget ?: ($segments[0] ?? 'sample');
            $controller = $controller ?: ($segments[1] ?? 'index');
            $action = $action ?: ($segments[2] ?? 'index');
        }

        return [
            'widgetName' => $widget,
            'controllerName' => $controller,
            'actionName' => $action,
            'args' => [],
        ];
    }

    public static function dispatchRequest($route)
    {
        $widget = W_Cache::getWidget($route['widgetName']);
        $widget->dispatch($route['controllerName'], $route['actionName'], $route['args'] ?? []);
    }

    public static function go($route = null)
    {
        $route = $route ?? static::routeRequest();
        static::dispatchRequest($route);
    }

    public static function loadWidgets()
    {
        foreach (self::getInstances() as $instance) {
            self::getWidgetDefinition($instance);
        }
    }

    public static function getInstances()
    {
        $base = defined('NF_APP_BASE') ? NF_APP_BASE : self::includePrefix();
        $dirs = glob(rtrim($base, '/') . '/instances/*', GLOB_ONLYDIR) ?: [];
        $instances = array_map('basename', $dirs);
        sort($instances);
        return $instances;
    }

    public static function getInstanceIdentifier($dir)
    {
        return $dir;
    }

    public static function getInstanceDirectory($identifier)
    {
        $base = defined('NF_APP_BASE') ? NF_APP_BASE : self::includePrefix();
        return rtrim($base, '/') . '/instances/' . $identifier;
    }

    public static function getWidgetPublicConfig($identifier)
    {
        $path = self::getInstanceDirectory($identifier) . '/widget-configuration.xml';
        if (!file_exists($path)) {
            return false;
        }
        return simplexml_load_file($path);
    }

    public static function getWidgetPrivateConfig(W_BaseWidget $widget)
    {
        $base = defined('NF_APP_BASE') ? NF_APP_BASE : self::includePrefix();
        $path = rtrim($base, '/') . '/xn_private/' . $widget->name . '-private-configuration.xml';
        if (!file_exists($path)) {
            return false;
        }
        return simplexml_load_file($path);
    }

    public static function putWidgetPublicConfig(W_BaseWidget $widget, $xml)
    {
        $path = self::getInstanceDirectory($widget->name) . '/widget-configuration.xml';
        file_put_contents($path, $xml);
        unset(self::$definitions[$widget->name]);
    }

    public static function getWidgetDefinition($identifier)
    {
        if (!isset(self::$definitions[$identifier])) {
            $xml = self::getWidgetPublicConfig($identifier);
            if ($xml === false) {
                return null;
            }
            $root = (string) ($xml['root'] ?? $identifier);
            $config = [];
            if (isset($xml->config)) {
                foreach ($xml->config->children() as $child) {
                    $config[$child->getName()] = (string) $child;
                }
            }
            if (!isset($config['domainName'])) {
                $config['domainName'] = 'localhost';
            }
            if (!array_key_exists('router', $config)) {
                $config['router'] = null;
            }
            $private = [];
            $base = defined('NF_APP_BASE') ? NF_APP_BASE : self::includePrefix();
            $privatePath = rtrim($base, '/') . '/xn_private/' . $identifier . '-private-configuration.xml';
            if (file_exists($privatePath)) {
                $privateXml = simplexml_load_file($privatePath);
                if ($privateXml instanceof SimpleXMLElement) {
                    foreach ($privateXml->children() as $child) {
                        $private[$child->getName()] = (string) $child;
                    }
                }
            }
            self::$definitions[$identifier] = [
                'root' => $root ?: $identifier,
                'config' => $config,
                'privateConfig' => $private,
            ];
        }
        return self::$definitions[$identifier];
    }

}
