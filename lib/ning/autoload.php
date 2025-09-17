<?php
/**
 * Autoloader for the lightweight Ning compatibility layer.
 */
spl_autoload_register(function ($class) {
    if (strpos($class, 'Ning\\') !== 0) {
        return;
    }
    $relative = substr($class, strlen('Ning\\'));
    $relativePath = str_replace('\\', '/', $relative) . '.php';
    $paths = [__DIR__ . '/' . $relativePath];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});
