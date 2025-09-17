<?php
/* NF_APP_BASE should have been defined by the time this page is included.
 * This page just does everything else to initialize the app
 */

if (!defined('NF_BASE_URL')) {
    $baseUrl = '';

    $config = $GLOBALS['nf_app_config'] ?? null;
    if (is_array($config)) {
        $appConfig = $config['app'] ?? null;
        if (is_array($appConfig)) {
            $candidate = $appConfig['base_url'] ?? '';
            if (is_string($candidate)) {
                $trimmed = trim($candidate);
                if ($trimmed !== '') {
                    $baseUrl = rtrim($trimmed, '/');
                    if ($baseUrl === '') {
                        $baseUrl = $trimmed;
                    }
                }
            }
        }

        if ($baseUrl === '') {
            $networks = $config['networks'] ?? null;
            if (is_array($networks) && !empty($networks)) {
                $firstNetwork = reset($networks);
                if (is_array($firstNetwork)) {
                    $networkUrl = $firstNetwork['primary_url'] ?? '';
                    if (is_string($networkUrl)) {
                        $trimmed = trim($networkUrl);
                        if ($trimmed !== '') {
                            $baseUrl = rtrim($trimmed, '/');
                            if ($baseUrl === '') {
                                $baseUrl = $trimmed;
                            }
                        }
                    }
                }
            }
        }
    }

    if ($baseUrl === '') {
        $httpsEnabled = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $httpsEnabled ? 'https' : 'http';

        $host = (string) ($_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost'));
        $host = trim($host);
        if ($host === '') {
            $host = 'localhost';
        }

        $port = null;
        if (isset($_SERVER['SERVER_PORT']) && is_numeric($_SERVER['SERVER_PORT'])) {
            $port = (int) $_SERVER['SERVER_PORT'];
        }

        $defaultPort = $httpsEnabled ? 443 : 80;
        $authority = $host;
        if ($port !== null && $port > 0 && $port !== $defaultPort && !str_contains($host, ':')) {
            $authority .= ':' . $port;
        }

        $basePath = '/';
        if (!empty($_SERVER['SCRIPT_NAME'])) {
            $scriptName = str_replace('\\', '/', (string) $_SERVER['SCRIPT_NAME']);
            $scriptDir = trim(dirname($scriptName), '/');
            if ($scriptDir !== '' && $scriptDir !== '.') {
                $basePath = '/' . ltrim($scriptDir, '/');
            }
        }

        $baseUrl = $scheme . '://' . ltrim($authority, '/');
        if ($basePath !== '/' && $basePath !== '') {
            $baseUrl .= $basePath;
        }
        $baseUrl = rtrim($baseUrl, '/');
    }

    define('NF_BASE_URL', $baseUrl);
}

/* Bootstrap the lightweight Ning SDK compatibility layer if the legacy SDK is unavailable. */
if (!defined('XN_INCLUDE_PREFIX') || !file_exists(XN_INCLUDE_PREFIX . '/WWF/bot.php')) {
    require_once __DIR__ . '/ning/bootstrap.php';
}

/* Load the base WWF code when present. */
if (defined('XN_INCLUDE_PREFIX') && file_exists(XN_INCLUDE_PREFIX . '/WWF/bot.php')) {
    require XN_INCLUDE_PREFIX . '/WWF/bot.php';
}

/* Define the WWF include prefix appropriately: the parent directory
 * of wherever this file lives. If this file is shared, then it'll
 * be the shared dir (BAZ-2551) */
if (!defined('W_INCLUDE_PREFIX')) {
    define('W_INCLUDE_PREFIX', realpath(dirname(__FILE__) . '/..'));
}

/* Load our custom App class */
W_WidgetApp::includeFileOnce('/lib/XG_App.php');

/* Content and profile caching */
XG_App::includeFileOnce('/lib/XG_Cache.php');

/* Query result caching */
XG_App::includeFileOnce('/lib/XG_Query.php');

/* Don't show boundary comments by default */
NF_Controller::hideBoundaryComments();

/* Dispatch the request */
XG_App::go();
