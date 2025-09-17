<?php
class W_Widget
{
    public $dir;
    public $config = [];
    public $privateConfig = [];

    public function __construct(string $dir)
    {
        $this->dir = $dir;
        $this->config = ['launched' => true];
        $this->privateConfig = [];
    }

    public function buildUrl(string $controller, string $action, array $params = []): string
    {
        $query = $params ? '?' . http_build_query($params) : '';
        return '/' . $this->dir . '/' . $controller . '/' . $action . $query;
    }

    public function buildResourceUrl(string $path): string
    {
        return '/xn_resources/' . trim($this->dir, '/') . '/' . ltrim($path, '/');
    }

    public function includeFileOnce(string $path): void
    {
        W_WidgetApp::includeFileOnce('/widgets/' . $this->dir . $path, false);
    }

    public function dispatch($controller, $action, array $params = []): void
    {
        // No-op in compatibility mode.
    }

    public function saveConfig(): void
    {
        // Configuration changes live in memory only.
    }
}
