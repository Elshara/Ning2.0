<?php
if (!class_exists('XG_App')) {
    class XG_App
    {
        public static function includeFileOnce(string $path, bool $fatalOnMissing = true)
        {
            return W_WidgetApp::includeFileOnce($path, $fatalOnMissing);
        }
    }
}
