<?php
/**
 * Simplified boot strap for the WWF layer used in the exercises.  The
 * original Ning platform shipped a large framework of helper classes; the
 * implementation here only recreates the pieces that are required to exercise
 * the sample application code.
 */
if (!defined('WWF_BOOTSTRAPPED')) {
    define('WWF_BOOTSTRAPPED', true);

    if (!defined('XN_INCLUDE_PREFIX')) {
        define('XN_INCLUDE_PREFIX', dirname(__DIR__));
    }
    if (!defined('W_INCLUDE_PREFIX')) {
        define('W_INCLUDE_PREFIX', dirname(__DIR__));
    }

    require_once __DIR__ . '/lib/NF.php';
    require_once __DIR__ . '/lib/NF_Exception.php';
    require_once __DIR__ . '/lib/NF_Controller.php';
    require_once __DIR__ . '/lib/NF_JSON.php';
    require_once __DIR__ . '/lib/W_BaseWidget.php';
    require_once __DIR__ . '/lib/W_Cache.php';
    require_once __DIR__ . '/lib/W_Controller.php';
    require_once __DIR__ . '/lib/W_WidgetApp.php';

    if (is_dir(XN_INCLUDE_PREFIX . '/XN')) {
        $eventStub = XN_INCLUDE_PREFIX . '/XN/Event.php';
        if (file_exists($eventStub)) {
            require_once $eventStub;
        }
    }

    spl_autoload_register(function ($class) {
        if (strpos($class, 'XN_') === 0) {
            $path = XN_INCLUDE_PREFIX . '/XN/' . substr($class, 3) . '.php';
            if (file_exists($path)) {
                require_once $path;
            }
        }
    });

    $_GET += [
        'xgsi' => null,
        'router' => null,
        'xgi' => null,
        'groupUrl' => null,
        'groupId' => null,
    ];
    if (!isset($_SERVER['HTTP_X_NING_REQUEST_URI'])) {
        $_SERVER['HTTP_X_NING_REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
    }
    if (!isset($_SERVER['HTTP_HOST'])) {
        $_SERVER['HTTP_HOST'] = 'localhost';
    }

    if (!class_exists('User')) {
        class User
        {
            public static function isMember($profile): bool { return true; }
            public static function isPending($profile): bool { return false; }
            public static function isBanned($profile): bool { return false; }
            public static function load($profile) { return $profile; }
            public static function canSendBannedMessage($profile): bool { return false; }
            public static function sentBannedMessage($profile): void {}
        }
    }
}
