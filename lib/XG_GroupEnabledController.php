<?php

/**
 * A Controller with support functions for groups.
 * When adding a group-enabled controller to a widget, be sure to add the widget
 * to the list of groupEnabledWidgetInstanceNames.
 */
abstract class XG_GroupEnabledController extends XG_BrowserAwareController {

    protected function _buildUrl($controller, $action, $qs = null) {
        return XG_GroupHelper::buildUrl($this->_widget->dir, $controller, $action, $qs);
    }

    protected function redirectTo($action, $controller = null, $query_string = null) {
        XG_GroupHelper::checkGroupNotDeleted($this->_widget->dir == 'forum' && $controller == 'topic' && $action == 'show');  // BAZ-4097 [Jon Aquino 2007-08-17]
        if (mb_strpos($action, 'http://') !== false || mb_strpos($action, '/') !== false) {
            parent::redirectTo($action, $controller, $query_string);
            return;
        }
        $controller = $controller ? $controller : $this->_controllerToUrl(get_class($this));
        parent::redirectTo(XG_GroupHelper::buildUrl($this->_widget->dir, $controller, $action, $query_string));
    }

}
