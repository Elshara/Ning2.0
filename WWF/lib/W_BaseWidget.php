<?php
/**
 * Very small widget abstraction that knows how to resolve widget specific
 * include paths and dispatch controller actions.
 */
class W_BaseWidget
{
    /** Instance name, e.g. "main". */
    public string $name;

    /** Directory holding the widget implementation, e.g. "index". */
    public string $dir;

    /** Parsed public configuration values. */
    public array $config = [];

    /** Parsed private configuration values. */
    public array $privateConfig = [];

    protected string $basePath;

    public function __construct(string $name, string $dir, array $config = [], array $privateConfig = [])
    {
        $this->name = $name;
        $this->dir = $dir;
        $this->config = $config;
        $this->privateConfig = $privateConfig;
        $this->basePath = rtrim(W_WidgetApp::includePrefix(), '/') . '/widgets/' . $dir;
    }

    public static function factory(string $identifier): W_Widget
    {
        $definition = W_WidgetApp::getWidgetDefinition($identifier);
        if (!$definition) {
            throw new NF_Exception("Cannot load widget '$identifier'");
        }
        return new W_Widget(
            $identifier,
            $definition['root'],
            $definition['config'],
            $definition['privateConfig']
        );
    }

    public function includeFileOnce(string $path, bool $useWidgetPrefix = true): void
    {
        $fullPath = $this->resolvePath($path, $useWidgetPrefix);
        if ($fullPath && file_exists($fullPath)) {
            require_once $fullPath;
        }
    }

    public function includeFile(string $path, bool $useWidgetPrefix = true): void
    {
        $fullPath = $this->resolvePath($path, $useWidgetPrefix);
        if ($fullPath && file_exists($fullPath)) {
            require $fullPath;
        }
    }

    protected function resolvePath(string $path, bool $useWidgetPrefix): string
    {
        if ($useWidgetPrefix) {
            return $this->basePath . $path;
        }
        return $path;
    }

    public function buildUrl(string $controller = 'index', string $action = 'index', $query = null): string
    {
        return W_WidgetApp::composeRequest($this->name, $controller, $action, $query);
    }

    public function buildResourceUrl(string $resourcePath): string
    {
        return '/widgets/' . $this->dir . '/' . ltrim($resourcePath, '/');
    }

    public function templatePath(string $controller, string $template): string
    {
        return $this->basePath . '/templates/' . $controller . '/' . $template . '.php';
    }

    public function dispatch(string $controller, string $action, array $args = []): void
    {
        $className = $this->classPrefix() . '_' . self::pascalCase($controller) . 'Controller';
        $file = $this->basePath . '/controllers/' . self::pascalCase($controller) . 'Controller.php';
        if (file_exists($file)) {
            require_once $file;
        }
        if (!class_exists($className)) {
            throw new RuntimeException("Controller '$className' not found for widget '{$this->name}'");
        }
        $controllerInstance = new $className($this);
        W_Cache::push($this);
        try {
            $controllerInstance->execute($action, $args);
        } finally {
            W_Cache::pop();
        }
    }

    protected function classPrefix(): string
    {
        return self::pascalCase($this->dir);
    }

    protected static function pascalCase(string $value): string
    {
        $parts = preg_split('/[_\-]+/', $value);
        $parts = array_map(fn ($part) => ucfirst(strtolower($part)), $parts);
        return implode('', $parts);
    }
}

class W_Widget extends W_BaseWidget
{
}
