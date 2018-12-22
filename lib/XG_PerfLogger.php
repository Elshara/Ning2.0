<?php

class XG_PerfLogger {

    /**
     * If PHP execution time is more than this many milliseconds
     * then an error will be logged in the centralized error log.
     *
     * @var integer
     */
    protected static $phpRuntimeThreshold = 5000;

    /**
     * Whether the perflogger is activated. Used to prevent
     * multiple mechanisms (query string, config variable)
     * from turning it on more than once (@see BAZ-5787)
     */
    protected static $activated = false;

    protected static $totalTime = 0.0;
	protected static $totalCount = 0;
    protected static $totalSize = 0;

    protected static $types = array();

    protected static $reqInfo = '';
	protected static $token = '';

	protected static $lastSeq = 0, $lastBodyIsNull = false;

	public static $usefulTime = 0;

	public static function before($seq, $curl, $headers, $body) {
		self::$lastSeq = $seq;
		self::$lastBodyIsNull = ($body === NULL);
	}

    public static function after($seq, $curl, $code, $headers, $body) {
        $info = curl_getinfo($curl);
        $parts = parse_url($info['url']);
        $url = urldecode($parts['path'] . (mb_strlen($parts['query']) ? ('?'.$parts['query']) : ''));
        if (strpos($url,'/xn/rest/1.0/cache') === 0) {
            $url = urldecode($url);
        }
		$type = preg_match('@^/xn/[^/]+/\d\.\d/([^/\(\:]+)@', $url, $matches) ? $matches[1] : 'other';
		// try to guess the request method: need a better way to pass it here.
		$method = self::$lastSeq == $seq ? (self::$lastBodyIsNull ? 'GET' : 'POST' ) : 'GET?';

		self::$types[$type]['cnt']++;
		self::$types[$type]['time'] += $info['total_time'];
		//self::$types[$type]['size'] += $info['size_download'];

        self::$totalCount++;
        self::$totalTime += $info['total_time'];
        self::$totalSize += $info['size_download'];

		error_log('perflog '.sprintf('%d %d %.06f %d %s %s %s',
			$seq, $info['http_code'], $info['total_time'], $info['size_download'],
			$method, $parts['host'].':'.$parts['port'], str_replace(' ','',$url)) . ' ' . self::$reqInfo);
    }

    public static function total() {
        $perType = '';
        $i = 0;
        foreach (self::$types as $k => $v) {
			$perType .= ($i++ ? ',' : '') . "$k=$v[cnt](t".intval(100*$v['time']/self::$totalTime)."%)";
        }
        error_log('perflog '.sprintf('%d %s %.06f %d %dkb %.06f %.06f %s',
			self::$totalCount, 'TOT', self::$totalTime, self::$totalSize,
			memory_get_peak_usage()/1024, microtime(true) - XN_PHP_START_TIME, microtime(true)-self::$usefulTime, '('.$perType.')') . ' ' . self::$reqInfo);
    }

    /* For manipulating $phpRuntimeThreshold */
    public static function getPhpRuntimeThreshold() {
        static $setFromAdminWidget = null;
        /* Override with value from the admin widget if provided */
        if (is_null($setFromAdminWidget)) {
            $setFromAdminWidget = true;
            try {
                $widget = W_Cache::getWidget('admin');
                if (isset($widget->config['phpRuntimeThreshold'])) {
                    self::$phpRuntimeThreshold = $widget->config['phpRuntimeThreshold'];
                }
            } catch (Exception $e) {
            }
        }
        return self::$phpRuntimeThreshold;
    }

    /**
     * This function is intended to be called only as a registered shutdown
     * function so it can determine how long the php execution to generate
     * the page took and log some info if it was too long
     */
    public static function measurePhpRuntime() {
        /* Calculate runtime in millis */
        $elapsed = floor(1000 * (microtime(true) - XN_PHP_START_TIME));
        /* The threshold for how long is "too long" can come from a few places,
         * listed in priority order here (higher overrides lower):
         *
         * - The phpRuntimeThreshold config variable in the admin widget
         * - XG_PerfLogger::$phpRuntimeThreshold
         */
        if ($elapsed > self::getPhpRuntimeThreshold()) {
            $err = sprintf("[%s] Runtime Threshold: %d %s %s %s %s %s\n",
                           gmdate('D M d H:i:s Y'),
                           $elapsed,
                           $_SERVER['HTTP_X_NING_REQUEST_URI'],
                           XN_Application::$CURRENT_URL,
                           XN_Profile::$VIEWER ? XN_Profile::$VIEWER : '-',
                           XN_REST::$APPCORE_IP,
                           $_SERVER['SERVER_ADDR']);
            if ($fp = @fopen('php://stderr','a')) {
                @fputs($fp, $err);
                @fclose($fp);
            }
        }
    }

    public static function activate() {
        if (self::$activated) { return; }
        self::$activated = true;
        $headers = getallheaders();
		if (!self::$token = XN_REST::$TRACE) {
			if (!self::$token = $headers['X-XN-Trace-Token']) {
				self::$token = 'xg-'.rand();
			}
		}
		self::$reqInfo = self::$token . ' ' . (XN_Profile::$VIEWER ? XN_Profile::$VIEWER : '-') . ' ' . $_SERVER['REQUEST_METHOD'] . ' ' . urldecode($_SERVER['REQUEST_URI']);

		XN_Event::listen('xn/rest/request/before', array('XG_PerfLogger','before'));
        XN_Event::listen('xn/rest/request/after', array('XG_PerfLogger','after'));
        register_shutdown_function(array('XG_PerfLogger','total'));
    }

}

if (isset($_GET['xg_perflog'])) {
    XG_Perflogger::activate();
}
