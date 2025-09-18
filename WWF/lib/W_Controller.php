<?php
/**
 * Extremely small controller implementation capable of invoking action
 * methods and rendering templates from the widget directory.
 */
class W_Controller extends NF_Controller
{
    /** @var W_Widget */
    protected $widget;

    public function __construct(W_Widget $widget)
    {
        $this->widget = $widget;
    }

    /**
     * Entry-point used by W_Widget to invoke controller actions.
     */
    public function execute($action, $args = [])
    {
        $this->_invokeAction($action, $args);
    }

    protected function _invokeAction($action, $args = null)
    {
        $callback = $this->_buildActionCallback($action);
        if (!is_callable($callback)) {
            throw new RuntimeException("Unknown action '$action' on " . get_class($this));
        }
        if ($args === null) {
            $args = [];
        } elseif (!is_array($args)) {
            $args = [$args];
        }
        return call_user_func_array($callback, $args);
    }

    protected function _buildActionCallback($action)
    {
        return [$this, 'action_' . $action];
    }

    /**
     * Very small view helper – it loads templates from
     * widgets/<root>/templates/<controller>/<template>.php when available.
     */
    protected function render($template = null, $controller = null)
    {
        if ($controller === null) {
            $controller = $this->_controllerToUrl(get_class($this));
        }
        if ($template === null) {
            $template = $controller;
        }
        $path = $this->widget->templatePath($controller, $template);
        if (is_readable($path)) {
            include $path;
        }
    }

    protected function renderPartial($template, $controller = null, $args = [])
    {
        $this->render($template, $controller);
    }

    protected function forwardTo($action, $controller = null, $args = [])
    {
        $controller = $controller ?? $this->_controllerToUrl(get_class($this));
        if ($controller === $this->_controllerToUrl(get_class($this))) {
            $this->_invokeAction($action, $args);
            return;
        }
        $this->widget->dispatch($controller, $action, $args);
    }

    protected function redirectTo($action, $controller = null, $query = null)
    {
        $url = $this->widget->buildUrl($controller ?? $this->_controllerToUrl(get_class($this)), $action, $query ?: []);
        header('Location: ' . $url);
        exit;
    }

    protected function _controllerToUrl($class)
    {
        $segments = explode('_', $class);
        $controller = end($segments);
        $controller = preg_replace('/Controller$/', '', $controller);
        return strtolower($controller);
    }
}
