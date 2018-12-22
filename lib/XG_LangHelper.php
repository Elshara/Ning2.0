<?php

/**
 * Useful functions for working with strings, arrays, and other basic PHP constructs.
 */
class XG_LangHelper {

    /**
     * Adds the item to the space-delimited string
     *
     * @param $needle string  the item
     * @param $haystack string  the space-delimited string
     * @param $ignoreDuplicates string  whether to ignore the item if it already exists
     * @return string  the result
     */
    public static function addToDelimitedString($needle, $haystack, $ignoreDuplicates = false) {
        if ($ignoreDuplicates && mb_strpos(' ' . $haystack . ' ', ' ' . $needle . ' ') !== false) { return $haystack; }
        return trim($haystack . ' ' . $needle);
    }

    /**
     * Removes the item from the space-delimited string
     *
     * @param $needle string  the item
     * @param $haystack string  the space-delimited string
     * @return string  the result
     */
    public static function removeFromDelimitedString($needle, $haystack) {
        if (is_null($haystack)) { return null; }
        return trim(str_replace(' ' . $needle . ' ', ' ', ' ' . $haystack . ' '));
    }

    /**
     * Replaces the first occurrence of the given string.
     *
     * @param $search string  the string to replace
     * @param $replace string  the replacement
     * @param $content string  the original string
     * @return string  the string with the replacement
     */
    public static function replaceOnce($search, $replace, $content){
        // By davidwhthomas@gmail.com, http://php.net/str_replace  [Jon Aquino 2007-08-07]
        $pos = mb_strpos($content, $search);
        if ($pos === false) { return $content; }
        else { return mb_substr($content, 0, $pos) . $replace . mb_substr($content, $pos+mb_strlen($search)); }
    }

    /**
     * Output all the HTTP headers that might convince a browser not to cache a page.
     *
     * @return 	void
     */
    public static function browserNeverCache() {
        header("Cache-Control: no-cache"); 	// Forces caches to obtain a new copy of the page from the origin server
        header("Cache-Control: no-store"); 	// Directs caches not to store the page under any circumstance
        header("Expires: " . date('D, d M Y H:i:s', 0) . ' GMT'); //Causes the proxy cache to see the page as "stale"
        header("Pragma: no-cache"); 		// HTTP 1.0 backward compatibility
        /*  header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false); */
    }

    /**
     * Determines if $sub appears at the start of $str.
     *
     * @param	$str	String to check the beginning of.
     * @param	$sub	String to check for.
     * @param	boolean
     */
    public static function startsWith($str, $sub) {
       return (mb_substr($str, 0, mb_strlen($sub)) === $sub);
    }

    /**
     * Determines if $sub appears at the end of $str.
     *
     * @param	$str	String to check the end of.
     * @param	$sub	String to check for.
     * @param	boolean
     */
    public static function endsWith($str, $sub) {
       return (mb_substr($str, mb_strlen($str) - mb_strlen($sub)) === $sub);
    }

    /**
     * Converts the possibly nested array into a non-nested array. Keys are not preserved.
     *
     * @param $array array  the array, which may contain arrays recursively
     * @return array  the values (no sub-arrays)
     */
    public static function arrayFlatten($array) {
        $result = array();
        foreach ($array as $item) {
            if (is_array($item)) {
                $result = array_merge($result, self::arrayFlatten($item));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * Takes a UTF-8 string and produces an ASCII string with [0-9a-zA-Z_-] chars only.
     * Useful for forming URLs from UTF-8 strings.
     * Transliterates where it can and elides where it cannot.  Do check that the
     * String returned will be empty if none of the characters can be transliterated.
     * Knows about some Chinese, Greek, Hebrew, Cyrillic and other character sets.
     * Does not know Kanji.
     *
     * @param	$str	string				String to transform.
     * @param	$spacesToUnderscore	boolean	If true, spaces will be changed into an underscore;
     *										if false, spaces will be elided.
     * @return 	string	String matching [A-Za-z_-]* regular expression.  May be empty.
     */
    public static function urlFriendlyStr($str, $spacesToUnderscore=false) {
        $transliterations = array('cyrillic_transliterate', 'diacritical_remove',
            'greek_transliterate', 'han_transliterate', 'hebrew_transliterate',
            'jamo_transliterate', 'normalize_ligature', 'normalize_punctuation', 'remove_punctuation',
            'normalize_superscript_numbers', 'normalize_subscript_numbers', 'normalize_numbers',
            'normalize_superscript', 'normalize_subscript', 'decompose_special',
            'decompose_currency_signs', 'decompose', 'hangul_to_jamo', 'compact_underscores');
        if ($spacesToUnderscore) {
            $transliterations = array_merge($transliterations, array('spaces_to_underscore'));
        }
        $s = transliterate($str, $transliterations, 'utf-8', 'ascii');
        return preg_replace('/[^0-9a-zA-Z_-]/u', '', $s);
    }

    /**
     * Sort an array of arrays (hashes) in place.  ARRAY PASSED IN WILL BE MODIFIED.
     * For a good example of how this routine works see XG_LangHelperTest::testAasort.
     *
     * @param	Array	&$data 	Array to sort.  Must be an array of hashes.
     * @param	$sort	String 	Comma-delimited string of "fieldname_direction" values:
     * 							"field_a,field_d,field_a" - _a for sort ascending, _d for sort descending.
     * @return 	void
     */
    public static function aasort(&$data, $sort) {
        $function = self::create_sort_function($sort, $data);
        return usort($data, $function);
    }

    // Create a sort function based on the supplied sort string (see aasort, above)
    // and sniffing the data in the supplied $data for suitable comparators.
    private static function create_sort_function($sort, $data) {
        $f = '';
        foreach (explode(",", $sort) as $raw) {
            $ending = mb_substr($raw, -mb_strlen("_d"), mb_strlen("_d"));
            if ($ending !== '_a' && $ending !== '_d') {
                $ending = '';
            }
            $key  = mb_substr($raw, 0, mb_strlen($raw) - mb_strlen($ending));
            $desc = ($ending === "_d");
            $cmp  = self::get_comparison_function($key, $data);
            $f .= '$res = ' . $cmp . '($a["' . $key . '"], $b["' . $key . '"]); '
                . 'if ($res != 0) { '
                    . 'return ' . ($desc ? '-$res' : '$res') . '; '
                . '} ';
        }
        $f .= 'return $a;';
        return create_function('$a, $b', $f);
    }

    //Look at the data and guess what the best comparator is for the specified key.
    private static function get_comparison_function($key, $data) {
        foreach ($data as $row) {
            $value = $row[$key];
            if (is_numeric($value)) {
                return 'XG_LangHelper::numcmp';
            }
        }
        return 'strcasecmp';
    }

    /**
     * Compares two numbers in a manner analagous to PHP's built-in strcmp.
     *
     * @param	$a	Number	First number to compare.
     * @param	$b	Number	Second number to compare.
     * @return 	Integer		1 if a > b, -1 if b > a, otherwise 0.
     */
    public static function numcmp($a, $b) {
        //TODO surely this exists in PHP already?
        if ($a > $b) {
            return 1;
        } elseif ($b > $a) {
            return -1;
        } else {
            return 0;
        }
    }

    /**
     * Returns numbers from $start to $end with the given $step.
     * Differs from range() in that $start and $end are always returned.
     *
     * @param $start integer  the first index
     * @param $end integer  the last index
     * @param $step integer  the interval size between indexes
     */
    public static function indexes($start, $end, $step) {
        // TODO: Rename this function to inclusiveRange(), to show its similarity to range()  [Jon Aquino 2008-01-03]
        if ($start + $step > $end) { return array($start, $end); }
        $indexes = range($start, $end, $step);
        if ($indexes[count($indexes)-1] != $end) { $indexes[] = $end; }
        return $indexes;
    }

    /**
     * Returns the value at the given key.
     *
     * @param $key string|integer  the key at which to retrieve the value
     * @return mixed  the value at the key
     */
    public static function get($key, $array) {
        return $array[$key];
    }

    /**
     * Lowercases the first character in the string.
     *
     * @param $string string  the string to transform
     * @return string  the string with lower-case first character
     */
    public static function lcfirst($string) {
        // From webmaster@onmyway.cz, http://ca3.php.net/ucfirst  [Jon Aquino 2008-03-12]
        return mb_strtolower(mb_substr($string, 0, 1)) . mb_substr($string, 1, mb_strlen($string));
    }

    /* **************************************************************
     * htmlwrap() function - v1.7
     * Copyright (c) 2004-2008 Brian Huisman AKA GreyWyvern
     *
     * This program may be distributed under the terms of the GPL
     *   - http://www.gnu.org/licenses/gpl.txt
     *
     *
     * htmlwrap -- Safely wraps a string containing HTML formatted text (not
     * a full HTML document) to a specified width
     *
     *
     * Requirements
     *   htmlwrap() requires a version of PHP later than 4.1.0 on *nix or
     * 4.2.3 on win32.
     *
     *
     * Changelog
     * 1.7  - Fix for buggy handling of \S with PCRE_UTF8 modifier
     *         - Reported by marj
     *
     * 1.6  - Fix for endless loop bug on certain special characters
     *         - Reported by Jamie Jones & Steve
     *
     * 1.5  - Tags no longer bulk converted to lowercase
     *         - Fixes a bug reported by Dave
     *
     * 1.4  - Made nobreak algorithm more robust
     *         - Fixes a bug reported by Jonathan Wage
     *
     * 1.3  - Added automatic UTF-8 encoding detection
     *      - Fixed case where HTML entities were not counted correctly
     *      - Some regexp speed tweaks
     *
     * 1.2  - Removed nl2br feature; script now *just* wraps HTML
     *
     * 1.1  - Now optionally works with UTF-8 multi-byte characters
     *
     *
     * Description
     *
     * string htmlwrap ( string str [, int width [, string break [, string nobreak]]])
     *
     * htmlwrap() is a function which wraps HTML by breaking long words and
     * preventing them from damaging your layout.  This function will NOT
     * insert <br /> tags every "width" characters as in the PHP wordwrap()
     * function.  HTML wraps automatically, so this function only ensures
     * wrapping at "width" characters is possible.  Use in places where a
     * page will accept user input in order to create HTML output like in
     * forums or blog comments.
     *
     * htmlwrap() won't break text within HTML tags and also preserves any
     * existing HTML entities within the string, like &nbsp; and &lt;  It
     * will only count these entities as one character.
     *
     * The function also allows you to specify "protected" elements, where
     * line-breaks are not inserted.  This is useful for elements like <pre>
     * if you don't want the code to be damaged by insertion of newlines.
     * Add the names of the elements you wish to protect from line-breaks as
     * as a space separate list to the nobreak argument.  Only names of
     * valid HTML tags are accepted.  (eg. "code pre blockquote")
     *
     * htmlwrap() will *always* break long strings of characters at the
     * specified width.  In this way, the function behaves as if the
     * wordwrap() "cut" flag is always set.  However, the function will try
     * to find "safe" characters within strings it breaks, where inserting a
     * line-break would make more sense.  You may edit these characters by
     * adding or removing them from the $lbrks variable.
     *
     * htmlwrap() is safe to use on strings containing UTF-8 multi-byte
     * characters.
     *
     * See the inline comments and http://www.greywyvern.com/php.php
     * for more info
     ******************************************************************** */
    public static function htmlwrap($str, $width = 60, $break = "\n", $nobreak = "") {

        // Split HTML content into an array delimited by < and >
        // The flags save the delimeters and remove empty variables
        $content = preg_split("/([<>])/", $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // Transform protected element lists into arrays
        $nobreak = explode(" ", strtolower($nobreak));

        // Variable setup
        $intag = false;
        $innbk = array();
        $drain = "";

        // List of characters it is "safe" to insert line-breaks at
        // It is not necessary to add < and > as they are automatically implied
        $lbrks = "/?!%)-}]\\\"':;&";

        // Is $str a UTF8 string?
        $utf8 = (preg_match("/^([\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})*$/", $str)) ? "u" : "";

        while (list(, $value) = each($content)) {
            switch ($value) {

                // If a < is encountered, set the "in-tag" flag
                case "<": $intag = true; break;

                // If a > is encountered, remove the flag
                case ">": $intag = false; break;

                default:

                    // If we are currently within a tag...
                    if ($intag) {

                        // Create a lowercase copy of this tag's contents
                        $lvalue = strtolower($value);

                        // If the first character is not a / then this is an opening tag
                        if ($lvalue{0} != "/") {

                            // Collect the tag name
                            preg_match("/^(\w*?)(\s|$)/", $lvalue, $t);

                            // If this is a protected element, activate the associated protection flag
                            if (in_array($t[1], $nobreak)) array_unshift($innbk, $t[1]);

                        // Otherwise this is a closing tag
                        } else {

                            // If this is a closing tag for a protected element, unset the flag
                            if (in_array(substr($lvalue, 1), $nobreak)) {
                                reset($innbk);
                                while (list($key, $tag) = each($innbk)) {
                                    if (substr($lvalue, 1) == $tag) {
                                        unset($innbk[$key]);
                                        break;
                                    }
                                }
                                $innbk = array_values($innbk);
                            }
                        }

                    // Else if we're outside any tags...
                    } else if ($value) {

                        // If unprotected...
                        if (!count($innbk)) {

                            // Use the ACK (006) ASCII symbol to replace all HTML entities temporarily
                            $value = str_replace("\x06", "", $value);
                            preg_match_all("/&([a-z\d]{2,7}|#\d{2,5});/i", $value, $ents);
                            $value = preg_replace("/&([a-z\d]{2,7}|#\d{2,5});/i", "\x06", $value);

                            // Enter the line-break loop
                            do {
                                $store = $value;

                                // Find the first stretch of characters over the $width limit
                                if (preg_match("/^(.*?\s)?([^\s]{".$width."})(?!(".preg_quote($break, "/")."|\s))(.*)$/s{$utf8}", $value, $match)) {

                                    if (strlen($match[2])) {
                                        // Determine the last "safe line-break" character within this match
                                        for ($x = 0, $ledge = 0; $x < strlen($lbrks); $x++) $ledge = max($ledge, strrpos($match[2], $lbrks{$x}));
                                        if (!$ledge) $ledge = strlen($match[2]) - 1;

                                        // Insert the modified string
                                        $value = $match[1].substr($match[2], 0, $ledge + 1).$break.substr($match[2], $ledge + 1).$match[4];
                                    }
                                }

                            // Loop while overlimit strings are still being found
                            } while ($store != $value);

                            // Put captured HTML entities back into the string
                            foreach ($ents[0] as $ent) $value = preg_replace("/\x06/", $ent, $value, 1);
                        }
                    }
                    break;
            }

            // Send the modified segment down the drain
            $drain .= $value;
        }

        // Return contents of the drain
        return $drain;
    }
}
