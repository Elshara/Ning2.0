<?php
XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');

class Index_FeatureHelper {

    /**
     *  Update mozzle enabled status based on the supplied layout.  First order
     *    non-permanent mozzles will be enabled IFF they have an embed in the
     *    layout.  Non first order mozzles will be enabled.
     *
     * @param $layoutDoc DOMDocument representing the layout
     */
    public static function updateMozzleStatusFromLayout($layoutDoc) {
        // TODO: Use XG_LayoutHelper::widgetNamesInLayout, which is similar to this code. [Jon Aquino 2008-08-28]
        $xpath = new DOMXPath($layoutDoc);
        $mozzlesInLayout = array();
        $embedList = $xpath->query('//module');
        for ($n = 0; $n < $embedList->length; $n++) {
            $module = $embedList->item($n);
            $modulesInLayout[$module->getAttribute('widgetName')] = TRUE;
        }

        //  Mark widgets as enabled IFF they're in the layout
        $modules = XG_ModuleHelper::getAllModules();
        foreach ($modules as $module) {
            if ($module->config['isPermanent'] || !$module->config['isFirstOrderFeature']) {
                $name = $module->dir;
                if ($name == 'page') {
                    //  'page' is internal and should remain disabled
                    $module->privateConfig['isEnabled'] = FALSE;
                } else {
                    //  other permanent or non-first order mozzles should be enabled
                    $module->privateConfig['isEnabled'] = TRUE;
                }
            }
            else {
                //  non-permanent first order mozzles should be enabled IFF they
                //    have one or more embeds in the layout
                $name = $module->dir;
                if ($name != "opensocial") {
                    $module->privateConfig['isEnabled'] = (in_array($name, array_keys($modulesInLayout)) ? 1 : 0);
                }
            }
            $module->saveConfig();
        }
    }

    /**
     * Fires feature/add/after and feature/remove/after events.
     *
     * @param $initialWidgetNames array  names of the widgets before a change to the homepage layout
     * @param $finalWidgetNames array  names of the widgets after a change to the homepage layout
     */
    public static function fireEvents($initialWidgetNames, $finalWidgetNames, $addEventName = 'feature/add/after', $removeEventName = 'feature/remove/after') {
        XG_App::includeFileOnce('/lib/XG_TabLayout.php'); // XG_TabLayout listens for these events [Jon Aquino 2008-08-28]
        foreach (array_diff($finalWidgetNames, $initialWidgetNames) as $widgetName) {
            XN_Event::fire($addEventName, array($widgetName));
        }
        foreach (array_diff($initialWidgetNames, $finalWidgetNames) as $widgetName) {
            XN_Event::fire($removeEventName, array($widgetName));
        }
    }

}
