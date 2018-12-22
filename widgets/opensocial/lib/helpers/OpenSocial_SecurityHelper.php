<?php

class OpenSocial_SecurityHelper {

    /**
     * Default implementation of the SecureToken generator. @TODO
     */
    public static function generateSecureToken($gadget) {
      $data = array('o' => $gadget->ownerName,
            'v' => $gadget->viewerName,
            'd' => $gadget->domain,
            'u' => $gadget->appUrl,
            'm' => $gadget->index);

        $json = new NF_JSON();
        return self::getOsocEncryptedToken(OpenSocial_SecurityHelper::encrypt($json->encode($data)), $gadget->appUrl);
    }
    
    /**
     * Get final token, encoded by the core
     *
     * @param   $st     string      our security token that will be wrapped by the osoc
     * @param   $appUrl string      URI of the gadget XML
     * @return          string      final security token
     */
    public static function getOsocEncryptedToken($st, $appUrl) {
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        try {
            $wrappedSt = XN_REST::post("http://" . OpenSocial_GadgetHelper::getOsocDomain() . "/xn/rest/1.0/token(url='" . rawurlencode($appUrl) . "')",
                                 $st, 'text/plain', array('X-Ning-RequestToken' => XN_REST::$SECURITY_TOKEN));
        } catch (Exception $e) {
            // OpenSocial Apps will not load in this case, but that doesn't mean we shouldn't render the page
            $wrappedSt = "";
        }
        return $wrappedSt;
    }

    /**
     * Encrypt the supplied data using the opensocial app key.
     *
     * @param   $data   String to encrypt.
     */
    public static function encrypt($data) {

        $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CBC,'');

        $ivSize = mcrypt_enc_get_iv_size($cipher);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM);

        mcrypt_generic_init($cipher, OpenSocial_SecurityHelper::appKey(), $iv);

        $encrypt = mcrypt_generic($cipher, gzdeflate($data, 9));

        mcrypt_generic_deinit($cipher);
        mcrypt_module_close($cipher);

        return bin2hex($iv.$encrypt);
    }

    /**
     * Decrypt the supplied data and unserialize using the supplied key.
     *
     * @param   $data   String to decrypt.
     * @return          Decrypted String.
     */
    public static function decrypt($data) {

        $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CBC,'');
        $ivSize = mcrypt_enc_get_iv_size($cipher);

        $encrypt = pack('H*', $data);
        $iv = substr($encrypt, 0, $ivSize);

        $encrypt = substr($encrypt, $ivSize);

        mcrypt_generic_init($cipher, OpenSocial_SecurityHelper::appKey(), $iv);

        $decrypt = trim(gzinflate(mdecrypt_generic($cipher, $encrypt)), "\0");

        mcrypt_generic_deinit($cipher);
        mcrypt_module_close($cipher);

        return $decrypt;
    }

    /**
     * Get this network's security token.
     */
    public static function appKey() {
        $opensocialWidget = W_Cache::getWidget('opensocial');

        if (! mb_strlen($opensocialWidget->privateConfig['secure_token'])) {
            self::generateKey();
        }
        return pack('H*', $opensocialWidget->privateConfig['secure_token']);
    }

    /**
     * Generate a new security token.
     */
    private static function generateKey() {
        $opensocialWidget = W_Cache::getWidget('opensocial');

        // our key is just a random 32 bytes string;
        $key = mcrypt_create_iv(32, MCRYPT_DEV_URANDOM);

        $opensocialWidget->privateConfig['secure_token'] = bin2hex($key);
        $opensocialWidget->saveConfig();
    }
    
    /**
     * Can the current user delete review?
     *
     * @param OpenSocialAppReview $review  review that needs to be deleted
     * @return boolean  true if it can, false if it cannot.
     */
    public static function currentUserCanDeleteReview($review) {
        return XG_SecurityHelper::userIsAdmin() || $review->my->user == XN_Profile::current()->screenName;
    }
}
