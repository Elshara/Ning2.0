<?php

/**
 * Useful functions for working with I18N and translations.
 */
class Index_LanguageHelper {

    /** Maximum number of characters allowed for the language name */
    const MAX_LANGUAGE_NAME_LENGTH = 200;

    /** Key used for the special-rules code in the data array */
    const SPECIAL_RULES_KEY = '<Special Rules>';

    /** Key used for the tab-naming rules code in the data array */
    const TAB_NAMES_KEY = '<Tab Names>';

    /** Special drop-down value indicating that the user wants to create a new translation */
    const NEW_TRANSLATION_LOCALE = '[New Translation]';

    /**
     * Returns the data for the 2-column translation table.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @param $searchText string  search terms, separated by spaces
     * @param $filter string  "all" (default), "missing", or "changed"
     * @param $submittedMessages array  messages submitted by the user: message name => string or JavaScript/PHP code
     * @param $submissionErrors array  error messages keyed by message name (e.g., "POPULAR_MEMBERS", "xg.forum.nls.deleteCategory", "SPECIAL RULES", "TAB NAMES")
     * @return array  name => message, each of which is an array containing:
     *     name - name of the message, e.g., "POPULAR_MEMBERS", "xg.forum.nls.deleteCategory", "SPECIAL RULES", "TAB NAMES"
     *     sourceText - original string or JavaScript/PHP code, in the base language
     *     targetText - corresponding text in the current language
     *     changed - whether the sourceText and targetText differ
     *     missing - whether the target text is missing
     *     errorMessage - description of a problem with the target text
     *     rows - estimated number of rows to use for textareas for this message
     *     wrap - false if the textarea should not word-wrap
     *     note - brief HTML message to display (optional)
     *     isTabText - whether this text is for a header tab
     * @param $percentComplete int  output for the percent complete (0-100)
     */
    public static function messages($locale, $searchText = null, $filter = 'all', &$submittedMessages = null, &$submissionErrors = array(), &$percentComplete = null) {
        // @todo  This function is too big. Refactor it into smaller functions.  [Jon Aquino 2007-08-10]
         W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Index_MessageCatalogReader.php');
        $reader = new Index_MessageCatalogReader();
        $reader->read(file_get_contents(NF_APP_BASE . '/lib/XG_MessageCatalog_en_US.php'));
        $reader->read(file_get_contents(NF_APP_BASE . '/xn_resources/widgets/shared/js/messagecatalogs/en_US.js'));
        if (XG_LanguageHelper::isCustomLocale($locale) && XG_LanguageHelper::baseLocale($locale) != 'en_US') {
            $reader->read(file_get_contents(NF_APP_BASE . '/lib/XG_MessageCatalog_' . XG_LanguageHelper::baseLocale($locale) . '.php'));
            $reader->read(file_get_contents(NF_APP_BASE . '/xn_resources/widgets/shared/js/messagecatalogs/' . XG_LanguageHelper::baseLocale($locale) . '.js'));
        }
        $sourceData = $reader->getData();
        $reader = new Index_MessageCatalogReader();
        foreach (array(XG_LanguageHelper::phpCatalogPath($locale), XG_LanguageHelper::customPhpCatalogPath($locale), XG_LanguageHelper::javaScriptCatalogPath($locale), XG_LanguageHelper::customJavaScriptCatalogPath($locale)) as $path) {
            if (file_exists($path)) { $reader->read(file_get_contents($path)); }
        }
        $targetData = $reader->getData();
        return self::messagesProper($sourceData, $targetData, $searchText, $filter, $submittedMessages, $submissionErrors, $percentComplete);
    }

    /**
     * Returns the data for the 2-column translation table.
     *
     * @param $sourceData array  texts for the left column, keyed by name
     * @param $targetData array  texts for the right column, keyed by name
     * @param $searchText string  search terms, separated by spaces
     * @param $filter string  "all" (default), "missing", or "changed"
     * @param $submittedMessages array  messages submitted by the user: message name => string or JavaScript/PHP code
     * @param $submissionErrors array  error messages keyed by message name (e.g., "POPULAR_MEMBERS", "xg.forum.nls.deleteCategory", "SPECIAL RULES", "TAB NAMES")
     * @return array  name => message, each of which is an array containing:
     *     name - name of the message, e.g., "POPULAR_MEMBERS", "xg.forum.nls.deleteCategory", "SPECIAL RULES", "TAB NAMES"
     *     sourceText - original string or JavaScript/PHP code, in the base language
     *     targetText - corresponding text in the current language
     *     changed - whether the sourceText and targetText differ
     *     missing - whether the target text is missing
     *     errorMessage - description of a problem with the target text
     *     rows - estimated number of rows to use for textareas for this message
     *     wrap - false if the textarea should not word-wrap
     *     note - brief HTML message to display (optional)
     *     isTabText - whether this text is for a header tab
     * @param $percentComplete int  output for the percent complete (0-100)
     */
    protected static function messagesProper(&$sourceData, &$targetData, $searchText = null, $filter = 'all', &$submittedMessages = null, &$submissionErrors = array(), &$percentComplete = null) {
        // TODO: Move the SmartSearch functionality out of this function  [Jon Aquino 2007-08-31]
        $tabSmartSearches = array('tab', 'tabs', mb_strtolower(xg_text('TAB')), mb_strtolower(xg_text('TABS')));
        if ($sourceData['TAB']) { $tabSmartSearches[] = mb_strtolower($sourceData['TAB']); }
        if ($targetData['TAB']) { $tabSmartSearches[] = mb_strtolower($targetData['TAB']); }
        if ($sourceData['TABS']) { $tabSmartSearches[] = mb_strtolower($sourceData['TABS']); }
        if ($targetData['TABS']) { $tabSmartSearches[] = mb_strtolower($targetData['TABS']); }
        $tabSmartSearch =  in_array(mb_strtolower($searchText), $tabSmartSearches);
        $submittedMessages = $submittedMessages ? $submittedMessages : array();
        $searchTerms = $searchText ? explode(' ', $searchText) : null;
        $earlyMessages = array();
        $middleMessages = array();
        $lateMessages = array();
        $errorCount = 0;
        $targetTextCount = 0;
        $totalMessageCount = 0;
        foreach (array_keys($sourceData) as $name) {
            $handledInTargetSpecialRules = mb_strpos($targetData[self::SPECIAL_RULES_KEY], "'" . $name . "'") !== false || mb_strpos($targetData[self::SPECIAL_RULES_KEY], '"' . $name . '"') !== false;
            $message = array(
                    'name' => $name,
                    'sourceText' => $sourceData[$name],
                    'targetText' => $targetData[$name],
                    'changed' => $targetData[$name] && ! self::messagesSame($sourceData[$name], $targetData[$name]),
                    'missing' => (! $targetData[$name] && ! $handledInTargetSpecialRules) || ($targetData[$name] && $handledInTargetSpecialRules),
                    'rows' => max(1, ceil(max(mb_strlen($sourceData[$name]), mb_strlen($targetData[$name])) / 60)));
            if (mb_strpos($message['name'], '_TAB_TEXT') !== false) {
                $message['isTabText'] = true;
            }
            if ($name == self::SPECIAL_RULES_KEY || $name == self::TAB_NAMES_KEY) {
                $message['rows'] = max(1, mb_substr_count($message['sourceText'], "\n"), mb_substr_count($message['targetText'], "\n"));
                $message['wrap'] = false;
            }
            if ($name == self::SPECIAL_RULES_KEY) {
                $missingSpecialRulesNames = self::missingSpecialRulesNames($sourceData[$name], $targetData[$name]);
                if ($targetData[$name] && $missingSpecialRulesNames) {
                    $message['missing'] = true;
                    $message['note'] = xg_html('FOLLOWING_ARE_MISSING_FROM_RULES', implode(', ', $missingSpecialRulesNames));
                }
            }
            if ($name == self::TAB_NAMES_KEY) {
                $message['missing'] = false; // This function is deprecated [Jon Aquino 2007-08-20]
            }
            if ($name == 'STOPWORDS') {
                $message['note'] = xg_html('STOPWORDS_ARE', 'href="http://en.wikipedia.org/wiki/Stopwords"');
                if (! $message['targetText']) { $message['missing'] = false; }
            }
            if ($message['errorMessage'] || $message['missing']) { $errorCount++; }
            if ($message['targetText']) { $targetTextCount++; }
            $totalMessageCount++;
            if (! $message['missing'] && $filter == 'missing') { continue; }
            if (! $message['changed'] && $filter == 'changed') { continue; }
            if ($tabSmartSearch && $message['isTabText']) {
                $earlyMessages[$name] = $message;
                continue;
            }
            if ($searchTerms) {
                $messageText = $message['sourceText'] . ' ' . $message['targetText'];
                foreach ($searchTerms as $searchTerm) {
                    if (mb_strlen($searchTerm) && mb_stripos($messageText, $searchTerm) === false) { continue 2; }
                }
            }
            if ($name == self::SPECIAL_RULES_KEY || $name == self::TAB_NAMES_KEY) {
                $lateMessages[$name] = $message;
            } else {
                $middleMessages[$name] = $message;
            }
        }
        $messages = array_merge($earlyMessages, $middleMessages, $lateMessages);
        foreach (array_keys($messages) as $name) {
            // Set submitted targetText and errorMessage after processing filter,
            // so the list won't change between submissions [Jon Aquino 2007-08-20]
            if (! is_null($submittedMessages[$name])) {
                $messages[$name]['targetText'] = $submittedMessages[$name];
            }
            if ($submissionErrors[$name]) {
                $messages[$name]['errorMessage'] = $submissionErrors[$name];
            }
        }
        $percentComplete = 100 - round(100 * $errorCount / $totalMessageCount);
        if ($percentComplete == 0 && $errorCount < $totalMessageCount) { $percentComplete = 1; }
        if ($percentComplete == 100 && $errorCount > 0) { $percentComplete = 99; }
        if ($targetTextCount == 0) { $percentComplete = 0; }
        return $messages;
    }

    /**
     * Returns whether the two messages are the same (ignoring whitespace differences).
     *
     * @param $a string  the first message
     * @param $b string  the second message
     * @return boolean  whether the two messages are basically equivalent
     */
    public static function messagesSame($a, $b) {
        return preg_replace('@\s@u', '', $a) == preg_replace('@\s@u', '', $b);
    }

    /**
     * Parses the decentralized catalogs.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @return array  the data, with keys being the message names (e.g. "POPULAR_MEMBERS",
     * "xg.forum.nls.deleteCategory", "SPECIAL RULES", "TAB NAMES") and values being a string or JavaScript
     * function code
     */
    public static function customMessages($locale) {
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/Index_MessageCatalogReader.php');
        $reader = new Index_MessageCatalogReader();
        foreach (array(XG_LanguageHelper::customPhpCatalogPath($locale), XG_LanguageHelper::customJavaScriptCatalogPath($locale)) as $path) {
            if (file_exists($path)) { $reader->read(file_get_contents($path)); }
        }
        return $reader->getData();
    }

    /**
     * Checks the messages for problems.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @param $messages array  message names and texts
     * @return array  message names and error messages, or an empty array if no problems were detected
     */
    public static function validate($locale, $messages) {
        $errors = array();
        foreach ($messages as $name => $text) {
            $text = trim($text);
            if (mb_strpos($text, 'function') !== false) {
                if (mb_substr_count($text, '{') != mb_substr_count($text, '}')) { $errors[$name] = xg_text('MISMATCHED_CURLY_BRACKETS', $name); }
                if (mb_substr_count($text, '(') != mb_substr_count($text, ')')) { $errors[$name] = xg_text('MISMATCHED_PARENTHESES', $name); }
                if (mb_substr_count($text, '[') != mb_substr_count($text, ']')) { $errors[$name] = xg_text('MISMATCHED_SQUARE_BRACKETS', $name); }
            }
            if ($name == Index_LanguageHelper::SPECIAL_RULES_KEY) {
                $error = self::validatePhp($name, $text, $locale);
                if ($error) { $errors[$name] = strip_tags($error); }
            }
            if ($name == Index_LanguageHelper::TAB_NAMES_KEY) {
                $error = self::validatePhp($name, $text, $locale);
                if ($error) { $errors[$name] = strip_tags($error); }
            }
        }
        return $errors;
    }

    /**
     * Tests the given PHP code.
     *
     * @param $name string  name of the code
     * @param $code string  the PHP code
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @retun string  an error message, or null if no error occurred
     */
    private static function validatePhp($name, $code, $locale) {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageCatalogReader.php');
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageCatalogWriter.php');
         XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        $writer = new Index_MessageCatalogWriter();
        $className = 'XG_CustomMessageCatalog_' . mt_rand();
        list($php, $javaScript) = $writer->write($locale, array($name => $code));
        ob_start();
        $success = eval(str_replace('XG_CustomMessageCatalog_' . $locale, $className, XG_LangHelper::replaceOnce('<?php', '', $php)));
        $message = trim(str_replace("\r", ' ', str_replace("\n", ' ', ob_get_contents())));
        ob_end_clean();
        if ($message) { return $message; }
        // Can't catch fatal errors, so use register_shutdown_function. [Jon Aquino 2007-08-11]
        register_shutdown_function(array('Index_LanguageHelper', 'handleShutdown'));
        $messageCatalog = new $className;
        self::$shutdownError = xg_text('ERROR_PROCESSING_SPECIAL_RULES');
        ob_start();
        foreach (self::specialRulesNames(Index_MessageCatalogReader::extractPhpSpecialRules(file_get_contents(NF_APP_BASE . '/lib/XG_MessageCatalog_en_US.php'))) as $name) {
            $messageCatalog->text(array($name, 1, 1, 1, 1, 1));
        }
        ob_end_clean();
        self::$shutdownError = xg_text('ERROR_PROCESSING_TAB_NAMES');
        ob_start();
        $messageCatalog->translateDefaultWidgetTitle('Forum');
        ob_end_clean();
        self::$shutdownError = '';
        return null;
    }

    /** Error message to display if a fatal error occurs during PHP validation. */
    private static $shutdownError;

    /**
     * Handles fatal errors that occur during PHP validation.
     */
    public static function handleShutdown() {
        if (! self::$shutdownError) { return; }
        $fatalErrorMessage = strip_tags(ob_get_contents());
        ob_end_clean();
        $requestedRoute = XG_App::getRequestedRoute();
        W_Cache::getWidget('main')->dispatch('language', $requestedRoute['actionName'] == 'doUpload' ? 'upload' : 'edit', array(array('' => self::$shutdownError . ' ' . $fatalErrorMessage)));
        self::$shutdownError = null; // handleShutdown seems to be called twice sometimes [Jon Aquino 2007-08-08]
    }

    /**
     * Converts the given message name into a friendlier one for display to the user.
     *
     * @param $name string  name of the message, e.g., "POPULAR_MEMBERS", "xg.forum.nls.deleteCategory", "SPECIAL RULES", "TAB NAMES"
     * @return string  a friendlier name, e.g., Special Rules
     */
    public static function displayName($name) {
        if ($name === Index_LanguageHelper::SPECIAL_RULES_KEY) { return xg_text('SPECIAL_RULES'); }
        if ($name === Index_LanguageHelper::TAB_NAMES_KEY) { return xg_text('TAB_NAMES'); }
        return $name;
    }

    /**
     * Returns an array of message names present in the source special rules but absent from the target special rules.
     *
     * @param $sourceSpecialRulesCode string  en_US code for special translation logic, e.g., handling plurals
     * @param $targetSpecialRulesCode string  corresponding code in the target language
     * @return array  message names missing from the target
     */
    protected static function missingSpecialRulesNames($sourceSpecialRulesCode, $targetSpecialRulesCode) {
        $missingSpecialRulesNames = array();
        foreach (self::specialRulesNames($sourceSpecialRulesCode) as $sourceName) {
            if (mb_strpos($targetSpecialRulesCode, "'" . $sourceName . "'") === false && mb_strpos($targetSpecialRulesCode, '"' . $sourceName . '"') === false) {
                $missingSpecialRulesNames[] = $sourceName;
            }
        }
        return $missingSpecialRulesNames;
    }

    /**
     * Returns an array of message names present in the source special rules
     *
     * @param $sourceSpecialRulesCode string  en_US code for special translation logic, e.g., handling plurals
     * @return array  message names
     */
    protected static function specialRulesNames($sourceSpecialRulesCode) {
        preg_match_all('@\$s\s*==\s*[\'"]([^\'"]+)[\'"]@u', $sourceSpecialRulesCode, $matches);
        $missingSpecialRulesNames = array();
        return $matches[1];
    }

}
