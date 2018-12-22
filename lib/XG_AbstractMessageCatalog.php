<?php
/**
 * Default implementations of helper functions for message catalogs.
 * Valid only for English dialects, but also useful for preventing compilation errors
 * in custom (decentralized) message catalogs that try to use these functions.
 */
abstract class XG_AbstractMessageCatalog {

    /**
     * Returns the given word pluralized e.g. 1 comment, 5 comments, etc.
     *
     * @param $count integer the number of the item
     * @param $word string a description of the item
     * @param $includeCount boolean whether to include the count in the text returned
     * @return the pluralized text
     * @deprecated  Handle pluralization in the text() function instead. This function is kept around for any custom catalogs that still use it.
     */
    protected static function pluralize($count, $word, $includeCount=TRUE) {
        if ($word == 'person') { $plural = $count == 1 ? 'person' : 'people'; }
        else if ($word == 'is') { $plural = $count == 1 ? 'is' : 'are'; }
        else if ($word == 'has been') { $plural = $count == 1 ? 'has been' : 'have been'; }
        else { $plural = $count == 1 ? $word : $word.'s'; }
        return $includeCount ? $count . ' ' . $plural : $plural;
    }

    /**
     * Places "a" or "an" before the given word.
     *
     * @param $word string  the word
     * @return string  "a" or "an", then the word
     * @deprecated  Handle pluralization in the text() function instead. This function is kept around for any custom catalogs that still use it.
     */
    protected static function an($word) {
        //  check for special cases - override the vowel-based rule if the provided
        //    term STARTS WITH one of these:
        $anWords = array('hour');
        $aWords = array('one', 'uni');

        $lowerWord = mb_strtolower($word);
        foreach ($anWords as $token) {
            if (mb_substr($lowerWord, 0, mb_strlen($token)) == $token) {
                return 'an ' . $word;
            }
        }

        foreach ($aWords as $token) {
            if (mb_substr($lowerWord, 0, mb_strlen($token)) == $token) {
                return 'a ' . $word;
            }
        }

        return (preg_match('@[aeiou]@iu', $word[0]) ? 'an' : 'a') . ' ' . $word;
    }

    /**
     * Returns a locale-dependent version of a url. The first argument is the url name, e.g., 'FLICKR_SUMMARY_SCREENSHOT'.
     * Subsequent arguments are substitution values (if the url contains sprintf format elements). Please note that these urls
     * are not xhtml encoded, rather it is up to the user to do that. Likewise, any argument needs to be properly
     * url encoded by the user before passing them in.
     *
     * @param $args array the url name, plus optional substitution values
     * @return string the url for this catalog
     */
    public static function url($args) {
        $text = self::$urls[$args[0]];
        if ($text) {
            $args[0] = $text;
        }

        return @call_user_func_array('sprintf', $args);
    }

    /**
     * Default URLs that might be different depending on the locale. Please note that these are not supposed to be xhtml encoded,
     * rather it is up to the user to do that. Likewise, any argument needs to be properly url encoded by the user before passing them in.
     */
    private static $urls = array(
        'FLICKR_SCREENSHOT_GETKEY' => '/xn_resources/widgets/index/gfx/flickr/getkey.png',
        'FLICKR_SCREENSHOT_KEY' => '/xn_resources/widgets/index/gfx/flickr/key.png',
        'FLICKR_SCREENSHOT_KEYINFO' => '/xn_resources/widgets/index/gfx/flickr/keyinfo.png',
        'FLICKR_SCREENSHOT_SETUPKEY' => '/xn_resources/widgets/index/gfx/flickr/setupkey.png'
    );

    /**
     * Default number formats.
     *
     * @deprecated
     */
    public static function numformat() {
        // TODO: Move these values into the $messages array of the message catalogs.
        // I don't think they warrant their own function. Also, moving them into $messages
        // will allow them to be edited in the Language Editor. Remove from XG_NullMessageCatalog as well. [Jon Aquino 2008-08-28]
        return array('THOUSAND_SEPARATOR' => ',', 'DECIMAL_POINT' => '.');
    }
}