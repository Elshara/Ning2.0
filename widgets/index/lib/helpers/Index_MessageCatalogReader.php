<?php
/**
 * Parses the PHP and JavaScript translation files ("message catalogs").
 */
class Index_MessageCatalogReader {

    /** The results of the parsing */
    private $data = array();

    /** JSON parser */
    private $json;

    /**
     * Constructor.
     */
    public function Index_MessageCatalogReader() {
        $this->json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
    }

    /**
     * Parses the given PHP or JavaScript I18N code.
     *
     * @param $s string  the code
     */
    public function read($s) {
        $this->readPhpArray($s);
        $this->readPhpSpecialRules($s);
        $this->readPhpTabNamingRules($s);
        $this->readJavaScript($s);
    }

    /**
     * Extracts the array of strings from the given PHP message catalog.
     *
     * @param $s string  the PHP message catalog string
     * @param $includeTokens boolean  whether to include the start- and end-delimiters in the result
     * @return string  the extracted code
     */
    public static function extractPhpArray($s, $includeTokens = false) {
        $code = self::extract('static $messages', ");\n", $s, $includeTokens);
        return $code ? $code : self::extract('static $messages', ");\r", $s, $includeTokens);
    }

    /**
     * Extracts the special rules from the given PHP message catalog.
     *
     * @param $s string  the PHP message catalog string
     * @param $includeTokens boolean  whether to include the start- and end-delimiters in the result
     * @return string  the extracted code
     */
    public static function extractPhpSpecialRules($s, $includeTokens = false) {
        return self::extract('$s = $args[0];', '$text = self::$messages[$s];', $s, $includeTokens);
    }

    /**
     * Extracts the tab-naming rules from the given PHP message catalog.
     *
     * @param $s string  the PHP message catalog string
     * @param $includeTokens boolean  whether to include the start- and end-delimiters in the result
     * @return string  the extracted code
     */
    public static function extractPhpTabNamingRules($s, $includeTokens = false) {
        return self::extract('translateDefaultWidgetTitle($widgetTitle) {', 'return $widgetTitle;', $s, $includeTokens);
    }

    /**
     * Parses the array of strings from the given PHP message catalog
     *
     * @param $s string  the code
     */
    private function readPhpArray($s) {
        $code = self::extractPhpArray($s);
        if (! $code) { return; }
        // Preserve line number in parse-error messages [Jon Aquino 2007-11-26]
        $lineNumber = mb_substr_count(mb_substr($s, 0, mb_strpos($s, $code)), "\n");
        ob_start();
        $result = eval(str_repeat("\n", $lineNumber) . '$messages' . $code . ');');
        $output = trim(ob_get_contents());
        ob_end_clean();
        if ($result === false && preg_match('@(Parse error.*?Index_MessageCatalogReader.php)\(\d+\)(.*?on line.*)@u', $output, $matches)) { throw new Exception(strip_tags($matches[1] . ' ' . $matches[2])); }
        if ($result === false) { throw new Exception('Could not parse the $messages array.'); }
        $this->data = array_merge($this->data, $messages);
    }

    /**
     * Parses the array of strings and functions from the given JavaScript message catalog
     *
     * @param $s string  the code
     */
    private function readJavaScript($s) {
        $s = str_replace("\r", "\n", str_replace("\r\n", "\n", $s));
        $currentNamespace = '';
        $currentFunctionName = '';
        $currentFunctionCode = '';
        foreach (explode("\n", $s) as $line) {
            if (preg_match('@^\s*dojo.lang.mixin\(([a-z.]+)@iu', $line, $matches)) {
                $currentNamespace = $matches[1];
            } elseif (preg_match('@^\s*\'?([a-z0-9_]+)\'?\s*:\s*(\'.*(?<!\\\\)\')@iu', $line, $matches)) {
                // prep $rawJson as a real JSON object
                if (mb_substr($matches[2], 0, 1) == "'") {
                    $matches[2] = '"' . mb_substr(str_replace(array("\'", '"'), array("'", '\"'), $matches[2]), 1, -1) . '"';
                }
                $rawJson= '{"x": ' . $matches[2] . '}';
                $result = $this->json->decode($rawJson);
                $this->data[$currentNamespace . '.' . $matches[1]] = $result['x'];
            } elseif (preg_match('@^\s*\'?([a-z0-9_]+)\'?\s*:\s*(function.*)@iu', $line, $matches)) {
                $currentFunctionName = $currentNamespace . '.' . $matches[1];
                $currentFunctionCode = $matches[2];
            } elseif ($currentFunctionName) {
                $currentFunctionCode .= "\n" . $line;
            }
            if ($currentFunctionName && mb_substr_count($currentFunctionCode, '{') > 0 && mb_substr_count($currentFunctionCode, '{') == mb_substr_count($currentFunctionCode, '}')) {
                $currentFunctionCode = trim(preg_replace('@}[^}]*$@su', '}', $currentFunctionCode));
                $this->data[$currentFunctionName ] = $currentFunctionCode;
                $currentFunctionName = '';
                $currentFunctionCode = '';
            }
        }
    }

    /**
     * Parses the special rules from the given PHP message catalog
     *
     * @param $s string  the code
     */
    private function readPhpSpecialRules($s) {
        $code = self::extractPhpSpecialRules($s);
        if (! $code) { return; }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_LanguageHelper.php');
        // Don't add the value if it is empty. Otherwise, if it is empty in the custom catalog, the empty
        // value will override the value in the main catalog, and the translation will be incorrectly reported as being
        // "99% complete" rather than "100% complete".  [Jon Aquino 2007-08-11]
        if (trim($code)) { $this->data[Index_LanguageHelper::SPECIAL_RULES_KEY] = trim($code); }
    }

    /**
     * Parses the rules for tab naming from the given PHP message catalog
     *
     * @param $s string  the code
     */
    private function readPhpTabNamingRules($s) {
        $code = self::extractPhpTabNamingRules($s);
        if (! $code) { return; }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_LanguageHelper.php');
        // Don't add the value if it is empty. Otherwise, if it is empty in the custom catalog, the empty
        // value will override the value in the main catalog, and the translation will be incorrectly reported as being
        // "99% complete" rather than "100% complete".  [Jon Aquino 2007-08-11]
        if (trim($code)) { $this->data[Index_LanguageHelper::TAB_NAMES_KEY] = trim($code); }
    }

    /**
     * Returns the results of the parsing.
     *
     * @return array  the data, with keys being the message names (e.g. "POPULAR_MEMBERS",
     * "xg.forum.nls.deleteCategory", "SPECIAL RULES", "TAB NAMES") and values being a string or JavaScript/PHP code
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Extracts the substring with the given start and end tokens.
     * $includeTokens helps to uniquely identify the result, which is useful if the extracted
     * substring is blank.
     *
     * @param $startString  the token that appears before the substring
     * @param $endString  the token that appears after the substring
     * @param $s  the string from which to extract the substring
     * @param $includeTokens boolean  whether to include the startString and endString in the result
     * @return string  the substring, or null if the start and end tokens were not found.
     */
    protected static function extract($startString, $endString, $s, $includeTokens = false) {
        $start = mb_strpos($s, $startString);
        if ($start === false) { return null; }
        $start += mb_strlen($startString);
        $end = mb_strpos($s, $endString, $start);
        if ($end === false) { return null; }
        return ($includeTokens ? $startString : '') . mb_substr($s, $start, $end - $start) . ($includeTokens ? $endString : '');
    }

}
