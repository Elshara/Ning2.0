<?php
if (defined('XN_INCLUDE_PREFIX')) {
    return;
}

$legacyIncludeRoot = dirname(__FILE__);
$wwfBot = $legacyIncludeRoot . '/WWF/bot.php';

if (!file_exists($wwfBot)) {
    throw new RuntimeException(
        sprintf(
            'Failed to locate legacy include path; expected to find "WWF/bot.php" at "%s".',
            $wwfBot
        )
    );
}

define('XN_INCLUDE_PREFIX', $legacyIncludeRoot);
