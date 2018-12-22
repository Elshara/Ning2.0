<?php

/**
 * Class allowing use of a single public/private key pair for a social network.
 *
 * Used in authentication back and forth between OpenSocial proxy and the network.
 */
class XG_CryptoHelper {

    /**
     * Encrypt the supplied data (any serializable PHP var) with the supplied key.
     *
     * @param   $key    String of an RSA private key .
     * @param   $data   PHP value to serialize and encrypt.
     */
     public static function encrypt($key, $data) {
         /* The data must be encrypted in chunks because the size of
          * the ciphertext can't be bigger than the size of the key */
         $chunks = str_split(serialize($data), 48);
         $encrypted_chunks = array();
         foreach ($chunks as $chunk) {
             openssl_private_encrypt($chunk, $encrypted_chunk, $key);
             $encrypted_chunks[] = base64_encode($encrypted_chunk);
         }
         /* Since the chunks are base64-encoded, "," is a good delimiter */
         return implode(',',$encrypted_chunks);
    }

    /**
     * Decrypt the supplied data and unserialize using the supplied key.
     *
     * @param   $key    String of an RSA public key
     * @param   $data   String of encrypted, serialized PHP value.
     * @return          Decrypted and unserialized PHP value.
     */
    public static function decrypt($key, $data) {
        $decrypted_chunks = array();
        foreach (explode(',',$data) as $chunk) {
            openssl_public_decrypt(base64_decode($chunk), $decrypted_chunk, $key);
            $decrypted_chunks[] = $decrypted_chunk;
        }
        return unserialize(implode('', $decrypted_chunks));
    }

    /**
     * Get this network's private key.  Generate new key pair if none exists.
     */
    public static function appPrivateKey() {
        $mainWidget = W_Cache::getWidget('main');
        if (! mb_strlen($mainWidget->privateConfig['key_private'])) {
            self::generateKeys();
        }
        return $mainWidget->privateConfig['key_private'];
    }

    /**
     * Get this network's public key.  Generate key pair if it does none exists.
     */
    public static function appPublicKey() {
        $mainWidget = W_Cache::getWidget('main');
        if (! mb_strlen($mainWidget->privateConfig['key_public'])) {
            self::generateKeys();
        }
        return $mainWidget->privateConfig['key_public'];
    }

    /**
     * Generate a new public/private key pair and save it in the main
     * widget's private configuration
     */
    private static function generateKeys() {
        $key = openssl_pkey_new();
        $info = openssl_pkey_get_details($key);
        openssl_pkey_export(openssl_pkey_get_private($key), $private);
        $public = $info['key'];
        $mainWidget = W_Cache::getWidget('main');
        $mainWidget->privateConfig['key_private'] = $private;
        $mainWidget->privateConfig['key_public'] = $public;
        $mainWidget->saveConfig();
    }


    public static function randomPrimes() {
        $f = file(NF_APP_BASE . '/lib/primes.txt');
	    $max = count($f) - 1;
        $x = $y = trim($f[mt_rand(0, $max)]);
	    while ($x == $y) {
            $y = trim($f[mt_rand(0, $max)]);
	    }
        return array($x, $y);
    }
}
