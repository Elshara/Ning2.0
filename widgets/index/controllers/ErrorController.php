<?php
XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');

class Index_ErrorController extends XG_BrowserAwareController {

    protected $setResponseCodeInBefore;

    protected static $descriptions = array(
        403 => 'Forbidden',
        404 => 'Not Found',
    );

    public function _before() {
        $route = XG_App::getRequestedRoute();
        $this->setResponseCodeInBefore = false;
        if ($route['actionName']) {
            $code = intval($route['actionName']);
            if ($code) {
                if (!$this->_hasAction($code)) {
                    $this->redirectTo('error', 'index');
                    return;
                }
                $header = "HTTP/1.0 $code";
                if (isset(self::$descriptions[$code])) {
                    $header .= ' ' . self::$descriptions[$code];
                }
                header($header);
                $this->setResponseCodeInBefore = true;
            }
        }
    }

    public function action_301() {
        header("Location: http://{$_SERVER['HTTP_HOST']}/", true, 301);
        exit();
    }

    public function action_403() {
        header("Location: http://{$_SERVER['HTTP_HOST']}/", true, 301); // Should this be 403? [Jon Aquino 2008-04-29]
        exit();
    }

    public function action_404() {
        if (! $this->setResponseCodeInBefore) {
            header("HTTP/1.0 404 Not Found");
        }
        $this->links = array(xg_html('MAIN_PAGE') => '/');
        $this->links[xg_html('MEMBERS')] = W_Cache::getWidget('profiles')->buildUrl('members', '');
        $_enabledModules = XG_ModuleHelper::getEnabledModules();
        foreach ($_enabledModules as $name => $module) {
            if (isset($module->config['isFirstOrderFeature']) && $module->config['isFirstOrderFeature']) {
                if ('profiles' !== $module->dir && 'music' !== $module->dir  && 'activity' !== $module->dir && 'opensocial' !== $module->dir) {
                    $this->links[$module->title] = $module->buildUrl('index', 'index');
                }
            }
        }
    }


}
