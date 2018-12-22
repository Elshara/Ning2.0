<?php
/**
 *  Handles URL generation and parsing process within the module.
 *
 *  For notes URLs use:
 *  	Notes_UrlHelper::noteUrl($note->title [,$action]);
 *	For general purpose URLs use:
 *		Notes_UrlHelper::url($action);
 *
 *	Notes_UrlHelper::reroute must be installed as route-handler for Notes widget.
 *
 *  This class is in lib rather than lib/helpers - it needs to be auto-loaded
 *  because it is the route handler for the Notes widget [Jon Aquino 2008-03-21]
 **/
class Notes_UrlHelper {
	protected static $baseUrl;

	/**
     * @param $route 	array 		the route as calculated by default.
	 * @return array
     */
    public static function reroute($route) {
    	self::_initBaseUrl($route);

		if ($route['controllerName'] == 'embed' && $route['actionName'] == 'setValues') {
			return $route;
		}
		if ($route['controllerName'] == 'index') {
			 if ($route['actionName'] != 'index') {
				return $route; // process request as usually
			 }
			 $route['controllerName'] = $route['actionName'] = ''; // Notes home request; falling back to NOTE_TITLE processing
		}

		$_GET['noteKey'] = $route['controllerName'];
		if (isset($_GET['edit']) && $_GET['edit']) {
			$route['actionName'] = 'edit';
		} elseif (isset($_POST['save']) && $_POST['save']) {
			$route['actionName'] = 'update';
		} else {
			$route['actionName'] = 'show';
		}
		$route['controllerName'] = 'index';
        return $route;
    }

    /**
	 *  Generates URL to a note (or to some note related action)
     *
	 *  @param      $note 		string|Note 	Note object, note title or note key. NULL - return only base url w/o any note keys
	 *  @param		$action 	string			Optional action (default is "show")
	 *  @param		$args 		string|hash		Optional arguments
     *  @return     string
     */
	public static function noteUrl($note, $action = '', $args = '') {
		if (!self::$baseUrl) {
			self::_initBaseUrl(NULL);
		}
		if (NULL === $note) {
			return self::$baseUrl;
		}
		$srcKey = Note::key(is_object($note) ? $note->my->noteKey : $note);
		$key = rawurlencode($srcKey);
		if (is_array($args)) {
			$args = http_build_query($args);
		}
		if ($action == 'delete' || $action == 'setFeatured' || $action == 'update') {
			return self::$baseUrl . "index/$action?noteKey=$key" . ($args ? '&' . $args : '');
		}
		// Workaround for the characters that are prohibited in the URLs.
		// The same logic is in the AddNoteLink.js and NoteEditor.js files.
		if (preg_match('@[|?#/%.]@',$srcKey)) {
			return self::$baseUrl . "index/".($action?$action:"show")."?noteKey=$key" . ($args ? '&' . $args : '');
		}
		if ($action) {
			$args = "$action=true" . ($args ? '&' . $args : '');
		}
		return self::$baseUrl . $key . ($args ? '?' . $args : '');
    }

    /**
	 *  Generates URL to the specific action (not the specific note related)
     *
	 *  @param		$action string		Optional action (default is "show")
	 *  @param		$args string|hash	Optional arguments
     *  @return     string
     */
	public static function url($action, $args = '') {
		if (!self::$baseUrl) {
			self::_initBaseUrl(NULL);
		}
		if (is_array($args)) {
			$args = http_build_query($args);
		}
		return self::$baseUrl . "index/$action" . ($args ? '?' . $args : '');
    }

    //
	protected static function _initBaseUrl ($route) { # void
		self::$baseUrl = ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]/".($route?$route['widgetName']:W_Cache::getWidget('notes')->dir)."/";
    }
}
?>
