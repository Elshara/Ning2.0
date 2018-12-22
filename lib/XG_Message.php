<?php

XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');

/**
 * The XG_Message class represents a message that the site would like to send
 * to a user. An XG_Message instance just contains the content (subject, body)
 * of a message -- the source and destination info are supplied when the message
 * is sent.
 */
abstract class XG_Message {
//** Static part
    /** This is controlled by the logMessaging config variable in the admin
      * widget. Set that config variable to 1 to enable message logging (BAZ-1936) */
    protected static $_log = null;

    /** Whether the message tester is being used. */
    public static $testing = false;

    /** Whether to store messages (for testing) instead of sending them. */
    public static $storeInsteadOfSend = false;
    public static $saveMessages = false;
    public static $allowCaching = true;

    /** Messages stored for the message tester. */
    public static $storedMessages = array();

    /**
     * Returns a To header based on the given screen-name or email address.
     *
     * @param $screenNameOrEmail string  an email address or Ning username
     * @return string  a To header, e.g., "Jonathan Aquino" <jon@example.com>
     * @see "New Solution for No-Reply Emails" in Clearspace
     */
    public static function formatEmailAddress($screenNameOrEmail) {
        return self::formatEmailAddressProper($screenNameOrEmail, XG_Cache::profiles($screenNameOrEmail));
    }

    /**
     * Returns whether the given email address looks like a screen-name-based
     * pseudo-email-address of the form screenname@users. The messaging core
     * converts these email addresses to real email addresses.
     *
     * @param $emailAddress  the email address to check
     * @return boolean  whether the email address is of the form screenname@users
     */
    public static function isPseudoEmailAddress($emailAddress) {
        return mb_substr($emailAddress, -6) == '@users';
    }

    /**
     * Converts the screen name to a pseudo-email-address that the messaging core
     * knows to convert to a real email address.
     *
     * @param $screenName string  the screen name of the recipient
     * @return string  the screen name with "@users" appended
     */
    public static function pseudoEmailAddress($screenName) {
        return $screenName . '@users';
    }

    // TODO: Perhaps someday turn this class into a small messaging service, rather than
    // a large repository of all emails. See Martin's www app for an example. [Jon Aquino 2007-10-04]
    protected static function logMessage($s) {
        if (is_null(self::$_log)) {
            try {
                $w = W_Cache::getWidget('admin');
                if ($w) {
                    self::$_log = ($w->config['logMessaging'] == 1);
                } else {
                    self::$_log = false;
                }
            } catch (Exception $e) {
                self::$_log = false;
            }
        }
        if (self::$_log) { error_log($s); }
    }

    /**
     * Returns a To header based on a profile.
     *
     * @param $screenNameOrEmail string  an email address or Ning username
     * @param XN_Profile  the corresponding profile object, if any
     * @return string  a To header, e.g., "Jonathan Aquino" <jon@users>
     */
    protected static function formatEmailAddressProper($screenNameOrEmail, $profile) {
        $email = mb_strpos($screenNameOrEmail, '@') === false ? self::pseudoEmailAddress($screenNameOrEmail) : $screenNameOrEmail;
        if (! $profile) { return $email; }
        if (get_class($profile) == 'TestProfile') {
            $fullName = $profile->fullName;
        } else {
            $fullName = XG_UserHelper::getFullName($profile);
        }
        $screenName = $profile->screenName;
        if (! $fullName || $fullName == $screenName) { return $email; }
        $hasAt = (false !== mb_strpos($email, '@'));
        list($mbox, $domain) = $hasAt ? explode('@', $email, 2) : array('x','x.com');
        $addr = imap_rfc822_write_address($mbox, $domain, mb_encode_mimeheader($fullName,'utf-7','Q'));
        if (!$hasAt) {
            $addr = str_replace('x@x.com', $email, $addr);
        }
        return $addr;
    }

    /**
     * Returns a properly formatted email address which points to the current server
     *
     * @param $name 	string  	a descriptive name of the sender
     * @param $mailbox	string 		mailbox on the current server. Default is "mail"
     * @return string  the From header, e.g., "John Smith" <mail@networkname.ning.com>
     */
    public static function localEmail($name, $mailbox = 'mail') {
        return imap_rfc822_write_address($mailbox, preg_replace('@^www\.@u','',$_SERVER['HTTP_HOST']), mb_encode_mimeheader($name,'utf-7','Q'));
    }

    /** Allow passing in an optional name for easier testing */
    public static function siteReturnAddress($name = null) {
        return self::localEmail($name === NULL ? XN_Application::load()->name . ' ' . xg_text('NOTIFICATIONS') : $name, 'noreply');
    }

    protected static function prepareLogoImageUrl($url, $maxWidth, $maxHeight) {
        $images = XG_Query::create('Content')
                      ->filter('owner')
                      ->filter('type','eic','ProfileCustomizationImage')
                      ->filter('title','=','logoImage')
                      ->filter('contributorName','=',XN_Application::load()->ownerName)
                      ->order('createdDate', 'desc')
                      ->end(1)
                      ->setCaching(XG_Cache::key('type','ProfileCustomizationImage'))
                      ->execute();
        if (count($images) > 0) {
            $image = $images[0];
            list($width,$height) = $image->imageDimensions('data');
            $widthScale = $width / $maxWidth;
            $heightScale = $height / $maxHeight;
            // Only add a scaling parameter if at least one dimension is too big
            if (($widthScale > 1) || ($heightScale > 1)) {
                $scaleToUse = max($widthScale, $heightScale);
                $newHeight = $height / $scaleToUse;
                $newWidth = $width / $scaleToUse;
                $url = XG_HttpHelper::addParameter($url, 'width',(integer) $newWidth);
                $url = XG_HttpHelper::addParameter($url, 'height',(integer) $newHeight);
            }
        }
        return $url;
    }

    protected static function prepareThumbnailUrl($url, $width = 150) {
        if (isset($url)) {
            $url = XG_HttpHelper::addParameters($url, array('width' => $width, 'xn_auth' => 'no'));
        }
        return $url;
    }

//** Dynamic part
    /**
     * Properties of a single message instance
     */
    protected $_data = array('subject' => null, 'body' => null, 'url' => null, 'anchor' => null);
    protected $_message;

    /**
     * Which message template to use
     */
    protected $_template = 'message';

    protected $_includedImages;

    /**
     * 	Forces mail to be rendered in HTML + alternative text copy.
     * 	This parameter could be redefined in sub-classes.
     * 	When "ignoreForceHtml" parameter is present in the private config and has true value, this flag is ignored and old logic
     * 	is used (when the format depends on "sendHtmlMessages" flag).
     *
     *  @var  bool
     */
    protected $_forceHtml = false;

    /**
     * Build a new message. At a minimum a message needs a subject and a body
     * @param $subject string
     * @param $body string
     */
    public function __construct($subjectOrParams = array(), $body = null) {
        if (is_array($subjectOrParams)) {
            foreach ($subjectOrParams as $k => $v) {
                $this->_data[$k] = $subjectOrParams[$k];
            }
        } else {
            $this->_data['subject'] = $subjectOrParams;
            $this->_data['body'] = $body;
        }
        // BAZ-2399
        $this->_data['body'] = html_entity_decode($this->_data['body'],ENT_QUOTES, 'UTF-8');
        $this->_data['subject'] = html_entity_decode($this->_data['subject'],ENT_QUOTES, 'UTF-8');
    }

    /**
     * Any property that's been put into $_data is available
     * @param $property string
     */
    public function __get($property) {
        if (array_key_exists($property, $this->_data)) {
            return $this->_data[$property];
        } else {
            throw new Exception("Unknown " . get_class($this) . "property: $property");
        }
    }
    /**
     * Produce a nice, stringified version useful in error messages
     */
    public function summary() {
        return "[$this->subject][" . mb_substr($this->body, 0, 20) . ']';
    }

    public function setTemplate($template) {
        $this->_template = $template;
    }

    /**
     *  Adds image data from URL. Depends on the settings, data can be either linked or embedded.
     *  Returns value that must be used as <img src>.
     *  (already html encoded)
     *
     *  @param      $url   string	Source URL.
     *  @return     string
     */
    public function addImageByUrl($url) {
        if (1) {
            return xnhtmlentities($url);
        } else {
            if (isset($this->_includedImages[$url])) {
                return $this->_includedImages[$url]['name'];
            }
            $name = uniqid();
            $ch = curl_init();
            curl_setopt_array($ch, array(CURLOPT_URL => $url, CURLOPT_HEADER => 0, CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_RETURNTRANSFER => 1));
            $content = curl_exec($ch);
            if (!strlen($content)) {
                curl_close($ch);
                return xnhtmlentities($url); // cannot fetch it, return as is
            }
            $type = strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
            curl_close($ch);
            // Basic types support is enough..
            if (!strncmp($type,'image/jpeg',10)) { $name .= '.jpg'; }
            elseif (!strncmp($type,'image/gif',9)) { $name .= '.gif'; }
            elseif (!strncmp($type,'image/png',9)) { $name .= '.png'; }
            elseif (!strncmp($type,'image/bmp',9)) { $name .= '.bmp'; }
            $this->_includedImages[$url] = array('content' => $content, 'type' => $type, 'name' => $name);
            return $name;
        }
    }

    protected function canSendMessage($user) {
        if ((($user instanceof XN_Content) || ($user instanceof W_Content)) && ($user->type == 'User')) {
            $userObject = $user;
        } else {
            // If the destination appears to be an e-mail address, send the message
            if (mb_strpos($user, '@') !== false) {
                self::logMessage("Can send {$this->summary()} to email $user");
                return true;
            }
            $userObject = User::load($user);
        }

        // Don't send the message if the user's preference says not to
        $canSend = ($userObject->my->emailNeverPref != 'Y');
        self::logMessage("Can send {$this->summary()} to {$userObject->contributorName} ? " . intval($canSend));
        return $canSend;
    }
//** Overloads
    /** public function send() -- implement it with any arguments. See examples in XG_Messages.php */

    /**
     *  Returns cached message. Currently does nothing. Overload it if you want.
     *  You can post-process cached message (for example you can do str_replace()).
     *
     *	@param		$to		string
     *	@param		$from	string
     *	@return		struct{headers,text_body,html_body,images}
     */
    protected function _getCachedMessage($to, $from) {
        // do nothing.
    }

    /**
     *  Add rendered message to cache for reusing. Currently does nothing. Overload it if you want.
     *  After adding message to cache message can be post-processed.
     *
     *	@param		$to		string
     *	@param		$from	string
     *	@param		$message struct{headers,text_body,html_body,images}
     *  @return     struct{headers,text_body,html_body,images}
     */
    protected function _setCachedMessage($to, $from, $message) {
        return $message;
    }

    /**
     *  Initialize Message-specific data. Overload it if you need to prepare some data for templates.
     *
     *	@param		$to		string
     *	@param		$from	string
     *  @return     void
     */
    protected function _initTemplateData($to, $from) {
        // do nothing.
    }

//** Implementation
    /**
     *  Creates common data that is used across all templates.
     *  Accessible via $message variable inside templates.
     *
     *  @return     void
     */
    protected function _initCommonData($to, $from) {
        $this->_message['to'] = $to;
        $this->_message['from'] = $from;
        $this->_message['appDescription'] = XG_MetatagHelper::appDescription();
        $this->_message['appName'] = XN_Application::load()->name;
        $this->_message['appTagline'] = XG_MetatagHelper::appTagline();
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
        Index_AppearanceHelper::getAppearanceSettings(null, $this->_message['cssDefaults'], $this->_message['imagePaths']);
        foreach ($this->_message['imagePaths'] as $__k => $__v) {
            $this->_message['imagePaths'][$__k] = XG_HttpHelper::addParameter($__v, 'xn_auth','no');
        }
        // If a custom logo image is set, figure out its dimensions so it can
        // be scaled properly (BAZ-1471)
        if (isset($this->_message['imagePaths']['logoImage'])) {
            $this->_message['imagePaths']['logoImage'] = self::prepareLogoImageUrl($this->_message['imagePaths']['logoImage'], 480, 120);
        }
    }

    /**
     *  Renders message.
     *
     *  @param      $to 	string	Recipient
     *  @param		$from	string	Sender
     *  @return     struct{headers,text_body,html_body,images}
     */
    protected function _renderMessage($to, $from) {
    	$this->_message = array();
		// unsubscribeUrl MUSTN'T be included into the cached message and must be updated every time
		$this->_data['unsubscribeUrl'] = BlockedContactList::isAlias($to)
			? W_Cache::getWidget('profiles')->buildUrl('profile','emailSettings')
			: xg_url('/') . '?xgo='. BlockedContactList::createOptoutCode($to, XN_Profile::current()->screenName);

        if (self::$allowCaching && $cached = $this->_getCachedMessage($to, $from)) {
            return $cached;
        }
        $this->_includedImages = array();
        $this->_initCommonData($to, $from);
        $this->_initTemplateData($to, $from);

		$this->_message['unsubscribeUrl'] = $this->_data['unsubscribeUrl']; // small hack because unsubscribeUrl can be replaced inside the caching logic

        $message = array('html_body' => '', 'text_body' => '');

        // If new "_forceHtml" logic is used, build both text and html parts [Andrey 2008-04-14]
        if ($this->_forceHtml && !W_Cache::getWidget('main')->privateConfig['ignoreForceHtml']) {
            $message['text_body'] = $this->_build('text', 'new');
            $message['html_body'] = $this->_build('html', 'new');
        } else {
            if (W_Cache::getWidget('main')->privateConfig['sendHtmlMessages'] == 'Y') {
                $message['html_body'] = $this->_build('html');
            } else {
                $message['text_body'] = $this->_build('text');
            }
        }
        $message['html_body'] = preg_replace('@…@u', '...',$message['html_body']);
        $message['text_body'] = preg_replace('@…@u', '...',$message['text_body']);
        $message['images'] = $this->_includedImages;
        $this->_includedImages = array();
        return self::$allowCaching ? $this->_setCachedMessage($to, $from, $message) : $message;
    }

    /**
     * Send! By default, a sender and a recipient must be specified
     * @param $to string
     * @param $from string
     * @see "New Solution for No-Reply Emails" in Clearspace
     */
    protected function _sendProper($to, $from, $addlHeaders = NULL) {
        $defaultFrom = self::localEmail(XN_Application::load()->name);
        $defaultReplyTo = self::localEmail('','do-not-reply');
        if (self::$testing) { $to = XN_Profile::current()->screenName; }

        // For BAZ-4702, I don't have time to check whether all the From addresses are correctly set,
        // so just force the From to the default (current behavior) and override the From header in the few
        // places where needed. [Jon Aquino 2007-10-10]
        $from = $defaultFrom;
        if (is_array($addlHeaders) && $addlHeaders['From']) {
            $from = $addlHeaders['From'];
        }

		if ($fromEmail = imap_rfc822_parse_adrlist($from,'')) {
			$fromEmail = $fromEmail[0]->mailbox.'@'.$fromEmail[0]->host;
		} else {
			$fromEmail = $from;
		}

		if (BlockedContactList::isSenderBlocked($to, array(XN_Profile::current()->screenName, XN_Profile::current()->email))) {
			return false;
		}
        // Don't send the message if the user's pref's forbid it
		if (! self::$testing && ! $this->canSendMessage($to)) {
            return false;
        }

        $headers = array(
            'From' => $from,
            'To' => self::formatEmailAddress($to),
            'Subject' => $this->subject,
            'Reply-To' => $defaultReplyTo,
            'X-XN_SECURITY_TOKEN' => XN_REST::$SECURITY_TOKEN,
        );
        if (is_array($addlHeaders)) {
            $headers = array_merge($headers, $addlHeaders);
        }

        XG_App::includeFileOnce('Mail.php', false);
        XG_App::includeFileOnce('Mail/mime.php', false);
        $message = $this->_renderMessage($to, $from);
        $mime = new Mail_mime();
        if ($message['text_body']) {
            $mime->setTXTBody($message['text_body']);
        }
        if ($message['html_body']) {
            $mime->setHTMLBody($message['html_body']);
        }
        foreach($message['images'] as $img) {
            $mime->addHTMLImage($img['content'], $img['type'], $img['name'], false);
		}
        $body = $mime->get(array('html_charset' => 'UTF-8', 'text_charset' => 'UTF-8', 'head_charset' => 'UTF-8'));

        $headers['Subject'] = preg_replace('/[\r\n]+/u',' ',preg_replace('@…@u', '...', $headers['Subject']));

        $headers = $mime->headers($headers);
        if (self::$saveMessages) {
            $hdrString = '';
            foreach($headers as $k=>$v) {
                $hdrString .= "$k: $v\r\n";
            }
            file_put_contents(NF_APP_BASE . '/xn_private/'.date('Ymdhis').'-'.$this->_template.'-'.intval(rand(0,1000)).'.msg', $hdrString . "\r\n" . $body);
        }
        if (self::$storeInsteadOfSend) {
            self::$storedMessages[] = array(
                'body' => $body,
                'headers' => $headers,
                'text_body' => $message['text_body'],
                'html_body' => $message['html_body'],
            );
        } else {
            self::logMessage("Sending {$this->summary()} to $to from $from");
			// See DOC-2026 for the log lines format. Syncronize this document with the actual message format.
			self::logSendAttempt(array(
				'##%##',
				date('c'),
				$fromEmail,
				$to,
				urlencode($this->subject),
				XN_Application::load()->relativeUrl,
				XN_Profile::current()->screenName,
				'', // status
				get_class($this),
			));
            $smtp = Mail::factory('smtp', array('host' => 'xnsmtp' . XN_AtomHelper::$DOMAIN_SUFFIX, 'port' => 9025, 'auth' => false));
            $res = $smtp->send(self::formatEmailAddress($to), $headers, $body);
            if (PEAR::isError($res)) {
				self::logSendResult("ERROR:".$res->getMessage());
                /* BAZ-2257: don't throw exception on messaging error, just return false */
                error_log("Couldn't send {$this->summary()}} from $from to $to: " . $res->getMessage());
                return false;
			} else {
				self::logSendResult("OK");
			}
        }
        return true;
    }

	static protected $logMsg = NULL, $catcherInited = 0;

    /**
	 *  Saves the current message information but doesn't log it until logSendResult() is called
	 *
	 *  See DOC-2026 for the log lines format.
     *
     *  @param      $messageParts   list    Log line.
     *  @return     void
     */
	static public function logSendAttempt($messageParts) { # void
		if (!self::$catcherInited) {
			XG_App::includeFileOnce('/lib/XG_LogHelper.php');
			register_shutdown_function(array(__CLASS__,'failuresCatcher'));
			self::$catcherInited = 1;
		}
		self::$logMsg = $messageParts;
    }

    /**
	 *  Writes the log line set by the last call of logSendAttempt() with the result equal to the passed value.
	 *
	 *  See DOC-2026 for the log lines format.
     *
	 *  @param      $result   string	Message send status
     *  @return     void
     */
	static public function logSendResult($result) { # void
		self::$logMsg[7] = $result;
		XG_LogHelper::logCentrally(join("\t", self::$logMsg), false);
		self::$logMsg = NULL;
    }

	static public function failuresCatcher() { # void
		if (NULL !== self::$logMsg) {
			self::logSendResult('SCRIPT_DIED');
		}
    }

    /**
     * Construct a message body from the data stored in the message and the
     * object's message template
     *
     * @param $__displayMode text|html 	whether to create an HTML message or a plain-text message
     * @param $__subDir		 string		Additional subdirectory for templates
     */
    protected function _build($__displayMode, $__subDir = '') {
        $__templateDir  = W_INCLUDE_PREFIX . '/lib/XG_Message' . ($__subDir ? "/$__subDir" : "");
        $__templateFile = $this->_template . ($__displayMode == 'html' ? '' : '_text') . '.php';
        if (! is_readable($__templateDir . '/' . $__templateFile)) {
            throw new Exception("Can't load template file $__templateDir/$__templateFile");
        }
        extract($this->_data);		// Bring the variables defined in $this->_data into local scope
        $helper = $__displayMode == 'html' ? new XG_MessageHelperHtml($this, $this->_message) : new XG_MessageHelperText($this, $this->_message);
        $message = $this->_message; // Set up local variables that we want the templates to have access to
        $msgtype = $this->_template . (!empty($type) ? ('.'.$type) : ''); // type of message: 'share.photo' etc. $type set above from $this->_data

        ob_start();
        if ($__displayMode == 'html') {
            include $__templateDir . '/_header.php'; 		/* No XG_App::includeFile() -- need to preserve scope */
            include $__templateDir . "/$__templateFile"; 	/* No XG_App::includeFile() -- need to preserve scope */
            include $__templateDir . '/_footer.php'; 		/* No XG_App::includeFile() -- need to preserve scope */
        } else {
            include $__templateDir . "/$__templateFile"; 	/* No XG_App::includeFile() -- need to preserve scope */
        }
        return ob_get_clean();
    }
}

/**
 * An in-memory cache to avoid making lots of API requests when
 * several messages are sent.
 */
class XG_MessageCacher {
    protected $cache = array();
	protected $stubs = array('url' => '<@invite_link@>', 'unsubscribeUrl' => '<@unsubscribe_link@>');
	protected $values = array();

    public function getMessage($to, $from, array&$data) {
        // $data['body'] is not the whole message body but a custom message specified by the user. [Jon Aquino 2008-06-19]
        $key = md5($data['subject'] . ':' . $data['body'] . ':' . $from);

        // Replace some attributes with stubs
		foreach($this->stubs as $k=>$v) {
			$this->values[$k] = $data[$k];
			$data[$k] = $v;
		}

        return isset($this->cache[$key]) ? $this->_postProcessMessage($this->cache[$key]) : NULL;
    }

    public function setMessage($to, $from, $message, array&$data) {
        $key = md5($data['subject'] . ':' . $data['body'] . ':' . $from);
        $this->cache[$key] = $message;
        return $this->_postProcessMessage($message);
    }

    protected function _postProcessMessage($message) { # void
		$s = array_values($this->stubs);
		$v = array_values($this->values);

		$message['html_body'] = str_replace( array_merge($s, array_map('xnhtmlentities', $s)), array_merge($v, array_map('xnhtmlentities', $v)), $message['html_body']);
        $message['text_body'] = str_replace( $s, $v, $message['text_body']);
        return $message;
    }
}
XG_App::includeFileOnce('/lib/XG_Messages.php');
XG_App::includeFileOnce('/lib/XG_MessageHelper.php');
