<?php
/** $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *	Browser-dependent logic. Currently supports:
 *		- desktop browsers
 *		- iphone browser
 *
 **/
abstract class XG_Browser {
    /**
     *  Current XG_Browser instance.
     */
    protected static $current;

    /**
	 *  Returns the current XG_Browser instance (initialized inside initFromRequest())
     *
     *  @return     XG_Browser
     */
    public static function current() {
        return self::$current;
    }

    /**
     *  Picks up the appropriate current browser instance.
     *
     *  @return     void
     */
    public static function initFromRequest() {
        $browser = 'desktop'; // good default
        if ($_SERVER['PATH_INFO'] == '/m' || mb_substr($_SERVER['PATH_INFO'],0,3) == '/m/') { // /m/ style urls
            $_SERVER['PATH_INFO'] = mb_substr($_SERVER['PATH_INFO'],2);
            $browser = 'iphone';
        }
        if (isset($_GET['xg_browser'])) { // set browser implicitly
            $browser = $_GET['xg_browser'];
        }
        self::$current = self::_browserByName($browser);
    }

    /**
	 *  Returns the URL to a browser-specific page
     *
	 *  @param      $browser	string		Browser type (desktop/iphone for now)
	 *  @param		$url		string		URL (absolute or relative)
     *  @return     string
     */
	public static function browserUrl($browser, $url) {
		if ($browser == 'desktop' && !preg_match('#/m/|xg_browser#', $url)) {
			return $url; // short circuit for desktop=>desktop.
		}
        return xg_url($url,"xg_browser=$browser");
    }

    /**
	 *  Executes the callback with the browser proper for the email context. Returns the callback return value.
	 *  Within the email context all URLs are generated for a proper browser (currently desktop).
	 *  See BAZ-10057.
     *
	 *  @param      $callback	callback	PHP callback
	 *  @param		...			list		Callback positional parameters
     *  @return     mixed
     */
	public static function execInEmailContext($callback /*, ...*/) {
		$args = func_get_args();
		array_shift($args);
		$orig = self::$current;
		self::$current = self::_browserByName('desktop');
		try {
			$res = call_user_func_array($callback, $args);
		} catch (Exception $e) {
			self::$current = $orig;
			throw $e;
		}
		self::$current = $orig;
		return $res;
    }


    /**
	 *  Returns browser instance by name.
     *
     *  @param      $name		string		Browser type (desktop/iphone for now)
	 *  @return     XG_Browser instance
     */
    protected static function _browserByName($name) {
        if ($name == 'iphone') {
            return new XG_Browser_Iphone();
        }
        return new XG_Browser_Desktop();
    }

// Browser Interface
    /**
	 *  Initializes the XG_Browser instance. You can include any browser-specific files here or do some initialization.
     */
	abstract public function __construct ();

    /**
	 *  Rewrites the URL to make it current browser specific.
     *
	 *  @param      $url		string		URL to rewrite
	 *  @param		$fixedUrl	bool		If TRUE, you cannot modify the path part of URL (like adding suffixes/prefixes).
	 *  									and if you want to add something, add query parameter.
     *  @return     string
     */
	abstract public function rewriteUrl ($url, $fixedUrl = false);

    /**
	 *  Returns the browser-specific template name.
     *
     *  @param      $template	string		Template name
     *  @return     string
     */
	abstract public function template ($template);

    /**
	 *  Returns the browser-specific action name
     *
     *  @param      $action		string		Action name
     *  @return     string
     */
	abstract public function action ($action);

    /**
	 *  Returns the most appropriate action in the controller. Can fallback to the generic action
	 *  if browser-specifc action is not found. Returns the action name
     *
	 *  @param      $controller W_Controller	Controller instance
	 *  @param		$action		string			Action name
	 *  @return     string
     */
    abstract public function findAction($controller, $action);
}

class XG_Browser_Desktop extends XG_Browser {
    public function __construct () {
    }

    public function rewriteUrl ($url, $fixedUrl = false) {
        return preg_replace('#^/m(/|$)#u','',$url);
    }

    public function template ($template) {
        return $template;
    }

    public function action ($action) {
        return $action;
    }

    public function findAction($controller, $action) {
        return $action;
    }
}

class XG_Browser_Iphone extends XG_Browser {
    public function __construct () {
        XG_App::includeFileOnce('/lib/XG_IPhoneHelper.php');
    }

    public function findAction($controller, $action) {
        //echo "looking for action `$action'<br>\n";
        $action = preg_replace('/_iphone$/u', '', $action);
        if (method_exists($controller, $action . '_iphone')) {
            $action .= '_iphone';
        } elseif ($action == 'detail' || $action == 'overridePrivacy') {
            // allow some special cases
        } else {
            $action .= '_iphone'; // otherwise force iphone suffix
        }
        return $action;
    }

    public function rewriteUrl ($url, $fixedUrl = false) { # hash
        if ($fixedUrl) {
			if (mb_strpos($url, 'xg_browser')) {
				$url = XG_HttpHelper::removeParameter($url, 'xg_browser');
			}
            return $url . (false === mb_strpos($url,'?') ? '?' : '&') . 'xg_browser=iphone';
        }
        if (false === mb_strpos($url, 'xg_browser=')) { //!! replace to smth else ...
            return $url[0] == '/' ? '/m' . $url : $url;
        }
        return $url; // leave as is
    }

    public function template ($template) {
        //echo "accessing template `$template'<br>\n";
        return preg_replace('/_iphone$/u', '', $template) . '_iphone';
    }

    public function action ($action) {
        //echo "accessing action `$action'<br>\n";
        /*if (false !== mb_strpos($action, '/')) { // url?
            return $this->rewriteUrl($url, true);
        }*/
        return preg_replace('/_iphone$/u', '', $action) . '_iphone';
    }
}
?>
