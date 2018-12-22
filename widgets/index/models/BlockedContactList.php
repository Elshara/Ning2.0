<?php
/**
 * Information about blocked email senders.
 * See BAZ-7510 for details.
 */
class BlockedContactList extends W_Model {
    /**
     * The mozzle that created this object
     *
     * @var XN_Attribute::STRING
     */
    public $mozzle;

    /**
     * System attribute marking whether to make the content available on Ning search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Recipient's email. For unregistered user it's a raw email, for registered users it's screenName@users
     * (to avoid problems when users change their emails).
     *
     * When a new user joins the network, the title of the blocked contact list for his email is updated with his screenName.
     *
     * @var XN_Attribute::STRING
     */
    public $title;

    /**
     * Serialized list of blocked sender's emails
     *
     * @var XN_Attribute::STRING optional
     */
    public $senders;

    /**
     * Blocks all emails for unregistered users. Registered users use "my->emailNeverPref" attribute.
     *
     * @var XN_Attribute::NUMBER
     */
    public $blockEverything;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
    protected static $cipher = MCRYPT_BLOWFISH;

    /**
     *  Checks wether address is an alias.
     *
     *  @param      $address   string   Address to check
     *  @return     bool
     */
    public static function isAlias($address) {
        return preg_match('/^[^@]+@lists$/i', trim($address)); /** @non-mb */
    }

    /**
     *  Loads or creates BlockedContactList object by the recipient's email/screenName
     *
     *  @param      $recipient	string		Recipient's email/screenName
     *  @param		$create		bool		Create object if it doesn't exist
     *  @return     W_Content
     */
    public static function load($recipient, $create = false) {
        $recipient = self::_recipient($recipient);
        $q = XN_Query::create('Content')
            ->filter('owner')
            ->filter('type','=','BlockedContactList')
            ->filter('title','=', $recipient)
            ->begin(0)->end(1)->alwaysReturnTotalCount(FALSE)
            ->execute();
        if ($q) {
            return $q[0];
        }
        if (!$create) {
            return NULL;
        }
        $r = W_Content::create('BlockedContactList');
        $r->title = $recipient;
        $r->isPrivate = true;
        $r->my->mozzle = 'main';
        $r->my->blockEverything = 0;
        return $r;
    }

    /**
     *  Blocks all emails from the sender sent to the recipient.
     *
     *  @param      $recipient	string			Recipient's email/screenName
     *  @param		$sender		string|list		Sender's email(s)/screenName(s)
     *  @return     void
     */
    public static function blockSender($recipient, $sender) {
        if (self::isAlias($recipient)) {
            return;
        }
        $info = self::load($recipient, true);
        $data = $info->my->senders ? unserialize($info->my->senders) : array();
        foreach((array)$sender as $s) {
            if ($s) {
                $data[mb_strtolower($s)] = 1;
            }
        }
        $info->my->senders = serialize($data);
        $info->save();
    }


    /**
     *  Unblocks all emails from the sender sent to the recipient.
     *
     *  @param      $recipient  string          Recipient's email/screenName
     *  @param      $sender     string|list     Sender's email(s)/screenName(s)
     *  @return     void
     */
    public static function unblockSender($recipient, $sender) {
        if (self::isAlias($recipient)) {
            return;
        }
        $info = self::load($recipient, true);
        $data = $info->my->senders ? unserialize($info->my->senders) : array();
        foreach((array)$sender as $s) {
            if ($s) {
                unset($data[mb_strtolower($s)]);
            }
        }
        $info->my->senders = serialize($data);
        $info->save();
    }
    
    /**
     *  Block all emails to a registered/unregistered user.
     *
     *  @param      $recipient   string    Recipient's email/screenName
     *  @return     void
     */
    public static function blockAllEmails($recipient) {
        if (self::isAlias($recipient)) {
            return;
        }
        if (($profile = XG_Cache::profiles($recipient)) && ($user = User::load($profile->screenName))) {
            // For registered users also update the corresponding user attribute
            W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
            Index_NotificationHelper::stopAllFollowing();
            $user->my->emailNeverPref = 'Y';
            $user->save();
        }
        $info = self::load($recipient, true);
        $info->my->blockEverything = 1;
        $info->save();
    }

    /**
     *  Checks whether the sender is in the recipient's black list
     *
     *  @param      $recipient	string			Recipient's email/screenName
     *  @param		$sender		array|string	Sender's email(s)/screenName(s)
     *  @return     bool
     */
    public static function isSenderBlocked($recipient, $sender) {
        if (self::isAlias($recipient)) {
            return false; // aliases are unblockable: BAZ-8994
        }
        if (!$info = self::load($recipient)) {
            return false;
        }
        if (($profile = XG_Cache::profiles($recipient)) && ($user = User::load($profile->screenName))) {
            // blockEverything is ignored for registered users, User->my->emailNeverPref is used instead.
        } elseif ($info->my->blockEverything) {
            return true;
        }
        if ( $info->my->senders && is_array($senders = unserialize($info->my->senders)) ) {
            foreach ((array)$sender as $s) {
                if ( $s && isset($senders[mb_strtolower($s)]) ) {
                    return true;
                }
            }
            // nothing found
        }
        return false;
    }

    /**
     * 	Merges two blocked lists together and then removes the source.
     *
     *  When a new user joins the network and there are already some blocked contacts for his email,
     *  we need to merge them to screenName@users.
     *
     *  @param      $source			string		Source email/screenName
     *  @param		$destination	string		Destination email/screenName
     *  @param		$blockEverything int-ref	Receives the "block everything" flag value.
     *  										Can be used to set the default user email preferences.
     *  @return     void
     */
    public static function merge($source, $destination, &$blockEverything = null) {
        $blockEverything = 0;
        if (!$source || !$destination) {
            return;
        }

        // No profile lookup, just simple conversion
        $source = mb_strtolower($source);
        if (mb_strpos($source, '@') === false) {
            $source .= '@users';
        }
        // No profile lookup, just simple conversion
        $destination = mb_strtolower($destination);
        if (mb_strpos($destination, '@') === false) {
            $destination .= '@users';
        }

        $res = XN_Query::create('Content')
            ->filter('owner')
            ->filter('type','=','BlockedContactList')
            ->filter('title','in', array($source, $destination))
            ->alwaysReturnTotalCount(FALSE)
            ->execute();
        $src = $dst = NULL;
        foreach($res as $o) {
            if ($o->title == $source) {
                $src = $o;
            } elseif ($o->title == $destination) {
                $dst = $o;
            }
        }
        if (!$src) { // nothing to merge
            return;
        }
        $blockEverything = $src->my->blockEverything;
        if (!$dst) { // no info for screenName, just fix the title
            $src->title = $destination;
            $src->save();
        } else { // merge the data and remove old info
            $srcSenders = unserialize($src->my->senders);
            $dstSenders = unserialize($dst->my->senders);
            $dst->my->senders = serialize(array_merge($dstSenders ? $dstSenders : array(), $srcSenders ? $srcSenders : array()));
            $dst->my->blockEverything = (int)$src->my->blockEverything; // overwrite blockEverything
            $dst->save();
            XN_Content::delete($src);
        }
    }

    
    /**
     *  Creates ciphered out-out code that expires after some time.
     *
     *  @param      $recipient		string		Recipient's email/screenName
     *  @param		$sender			string		Senders' email
     *  @return     string
     */
    public static function createOptoutCode($recipient, $sender) {
        $decrypted = pack('N', time()) . "$recipient\0$sender\0yo";

        // 0-iv is ok
        $key = reset(self::_getCipherKeys());
        $iv = str_repeat( "\0", mcrypt_get_iv_size(self::$cipher, MCRYPT_MODE_ECB) );
        $code = mcrypt_encrypt(self::$cipher, $key, $decrypted, MCRYPT_MODE_ECB, $iv);

        // Remove optional '=' from the eol and translate '+' into a more url-friendly '-'.
        return strtr(trim(base64_encode($code),'='), '+','-');
    }

    /**
     *  Parses the opt-out code. Returns false if code is wrong or expired. Returns struct{recipient,sender} if code is ok.
     *  Recipient and sender are the same as for createOptoutCode().
     *
     *  @param      $code   string    Ciphered opt-out code
     *  @return     struct{recipient, sender}
     */
    public static function parseOptoutCode($code, $expire = 604800 /* 7*86400 */) {
        $code = base64_decode(strtr($code,'-','+'));

        // 0-iv is ok
        $iv = str_repeat( "\0", mcrypt_get_iv_size(self::$cipher, MCRYPT_MODE_ECB) );
        // Try all existing keys
        foreach (self::_getCipherKeys() as $key) {
            $decrypted = mcrypt_decrypt(self::$cipher, $key, $code, MCRYPT_MODE_ECB, $iv);
            list($time) = array_values(unpack('Ntm', substr($decrypted, 0, 4)));
            $data = array_filter(explode("\0", substr($decrypted, 4)));
            list($recipient, $sender, $kw) = $data;

            if( count($data) == 3 && $recipient && $sender && $kw == 'yo' && $time > (time() - $expire) ) {
                return array('recipient' => $recipient, 'sender' => $sender);
            }
        }
        return false;
    }

    // Canonical recipient name
    protected static function _recipient($recipient) { # string
        $recipient = mb_strtolower($recipient);
        if (mb_strpos($recipient, '@') === false) {
            return $recipient . '@users';
        }
        return ($profile = XG_Cache::profiles($recipient)) ? $profile->screenName . '@users' : $recipient;
    }

    protected static function _getCipherKeys() {
        $keys = self::_getCipherKeysProper(W_Cache::getWidget('main'), 'optOutCipher', 86400*14, 3, 300, new XG_Cache, new XN_Cache);
        return $keys;
    }

    /**
     *  Returns the list of valid keys. The first key is the current key and the rest of the list is
     *  the previous keys. New keys are generated automatically.
     *
     *	@param		$widget			W_Widget	Current widget
     *	@param		$configParam    string		Configuration parameter to save the keys
     *	@param		$maxAge			int			Period when keys expire
     *	@param		$keysToKeep		int			Number of old keys to keep
     *	@param		$locker			object      Locker (testing purposes)
     *	@param		$cacher			object		Cacher (testing purposes)
     *  @return     list<key>
     */
    protected static function _getCipherKeysProper($widget, $configParam, $maxAge, $keysToKeep, $lockMaxAge, $locker, $cacher) {
        $keyInfo = unserialize($widget->privateConfig[$configParam]);
        if ($keyInfo && $keyInfo['tm'][0] > time() - $maxAge) {
            return $keyInfo['keys'];
        }
        $cacheKey = "cipher-key-$configParam-$keysToKeep-$maxAge";
        if ( !$locker->lock("$cacheKey:lock", $lockMaxAge) ) { // Cannot obtain the write lock
            if ($keyInfo) { // Use staled key, it's ok
                return $keyInfo['keys'];
            }
            // Because of NFS caching/latency it's possible that data is already in the config file
            // but config is not synced yet, so we use cache to get the new values immediately
            if ( !$newKey = $cacher->get($cacheKey, $lockMaxAge) ) {
                usleep(250*1000); // Give them another chance
                $newKey = $cacher->get($cacheKey, $lockMaxAge);
            }
            if ($newKey) { // We have the new key
                $keyInfo['keys'] = $keyInfo['keys'] ? array_slice($keyInfo['keys'], 0, $keysToKeep-1) : array();
                array_unshift($keyInfo['keys'], $newKey);
                return $keyInfo['keys'];
            }
            // No new key in the cache, generate new one...
        }

        $newKey = md5(uniqid(rand(), true));

        $keyInfo['tm'] = $keyInfo['tm'] ? array_slice($keyInfo['tm'], 0, $keysToKeep-1) : array();
        array_unshift($keyInfo['tm'], time());

        $keyInfo['keys'] = $keyInfo['keys'] ? array_slice($keyInfo['keys'], 0, $keysToKeep-1) : array();
        array_unshift($keyInfo['keys'], $newKey);

        $widget->privateConfig[$configParam] = serialize($keyInfo);
        $widget->saveConfig();

        $cacher->put($cacheKey, $newKey); // Put it into cache
        return $keyInfo['keys'];
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/
}
