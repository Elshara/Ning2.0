<?php
/**
 * A Controller which awares of the browser-specific actions/templates.
 * If you need to make your controller browser-aware, extends it from this class instead of W_Controller.
 *
 * Replaces all actions/templates with browser-specific.
 */
abstract class XG_BrowserAwareController extends W_Controller {
    public function _buildActionCallback($action) {
        return parent::_buildActionCallback(XG_Browser::current()->findAction($this, $action));
    }
    public function _hasAction($action) {
        return method_exists($this, 'action_' . XG_Browser::current()->findAction($this, $action));
    }
    protected function _invokeAction($action, $args = null) {
        $ret = parent::_invokeAction($action, $args);
        if (is_array($this->_disposition)
                && '_doRender' == $this->_disposition[0]
                && isset($this->_disposition[3])
                && isset($this->_disposition[3][NF_Controller::CACHE_AT_LOCATION])) {
            // fixes the weird setCaching() behavior. Otherwise template name doesn't use the browser-specific mapping
            $this->_disposition[1] = XG_Browser::current()->template($this->_disposition[1]);
        }
        return $ret;
    }

    protected function forwardTo($action, $controller = null, $args = null) {
        //echo "forwardTo($action, $controller, $args);<br>\n";
        return parent::forwardTo(XG_Browser::current()->action($action), $controller, $args);
    }
    protected function redirectTo($action, $controller = null, $query_string = null) {
        //echo "redirectTo($action, $controller, $query_string);<br>\n";
        if (preg_match('@^https?://@u', $action)) {
            return parent::redirectTo(XG_Browser::current()->rewriteUrl($action, true));
        }
        return parent::redirectTo(XG_Browser::current()->action($action), $controller, $query_string);
    }
    protected function render($template = null, $controller = null) {
        if (is_null($controller) || is_null($template)) {
            $stack = debug_backtrace();
            $parentFrame = $stack[1];
            if (is_null($template)) {
                // Turn action_groan() function name into "groan" template name
                $template = mb_substr($parentFrame['function'], mb_strlen('action_'));
            }
            if (is_null($controller)) {
                $controller = $this->_controllerToUrl($parentFrame['class']);
            }
        }
        return parent::render(XG_Browser::current()->template($template), $controller);
    }
    protected function renderPartial($template, $controller = null, $args = null) {
        return parent::renderPartial(XG_Browser::current()->template($template), $controller, $args);
    }
}
