<?php

/**
 * Useful functions for working with I18N and translations.
 */
class XG_LanguageHelper {

    /**
     * Returns all available languages.
     *
     * @return array  language names keyed by locale code, e.g., en_US => English (U.S.)
     */
    public static function localesAndNames() {
        $localesAndNames = array_merge(self::customLocalesAndNames(), self::nonCustomLocalesAndNames());
        asort($localesAndNames);
        return $localesAndNames;
    }

    /**
     * Array of popular languages for a user to select.
     */
    public static function popularCountryCodes() {
        $popularCountryCodes = array(
            'US',
            'GB',
            'FR',
            'DE'
        );
        return $popularCountryCodes;
   }

    /**
     * Non-custom languages keyed by locale code, e.g. en_US => English (U.S.)
     *
     * @var array
     */
    protected static $nonCustomLocalesAndNames = array(
            'en_US' => 'English (U.S.)',
            'en_GB' => 'English (British)',
            'es_AR' => 'Español (Argentina)',
            'es_ES' => 'Español (España)',
            'fr_CA' => 'Français',
            'pt_BR' => 'Português (Brasil)',
            'zh_CN' => '简体中文（China）',
            'nl_NL' => 'Nederlands (Nederlands)',
        	'it_IT' => 'Italiano (Italia)',
        	'de_DE' => 'Deutsch (Deutschland)',
            'cs_CZ' => 'Czech (Czech Republic)',
            'sv_SE' => 'Svensk (Sverige)',
            'fi_FI' => 'Suomi (Suomi)',
            'no_NO' => 'Norsk (Norge)',
            'el_GR' => 'Ελληνικα (Ελλαδα) ',
            'pl_PL' => 'Polski (Polska) ',
            'ko_KR' => '한국 (한국)',
            'zh_TW' => '正體字',
			'bg_BG' => 'Български (Република България)',
			'hu_HU' => 'Magyar (Magyar Köztársaság)',
			'ja_JP' => '日本語 (日本)',
			'ro_RO' => 'Română  (România)',
			'sk_SK' => 'Slovenčina (Slovensko)',
     );

    /**
     * Returns non-custom languages only.
     *
     * @return array  language names keyed by locale code, e.g., en_US => English (U.S.)
     */
    public static function nonCustomLocalesAndNames() {
        asort(self::$nonCustomLocalesAndNames);
        self::$nonCustomLocalesAndNames;
        return(self::$nonCustomLocalesAndNames);
    }

    /**
     * Returns custom languages only.
     *
     * @return array  language names keyed by locale code, e.g., custom_108442 => Kyrgyz (Cyrillic)
     */
    public static function customLocalesAndNames() {
        $customLocalesAndNames = array();
        foreach (self::customLocaleMetadata() as $locale => $metadata) {
            $customLocalesAndNames[$locale] = $metadata['name'];
        }
        return $customLocalesAndNames;
    }

    /**
     * Returns the list of custom languages.
     *
     * @param array  locale code => [name, baseLocale]
     */
    protected static function customLocaleMetadata() {
        $customLocalesAndNamesJson = W_Cache::getWidget('main')->config['customLocales'];
        if (! $customLocalesAndNamesJson || $customLocalesAndNamesJson[0] == 'a' /* Old serialized format [Jon Aquino 2007-08-11] */) {
            self::setCustomLocaleMetadata(array());
            return array();
        }
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $customLocaleMetadata = $json->decode($customLocalesAndNamesJson);
        asort($customLocaleMetadata);
        // Upgrade from old format [Jon Aquino 2007-08-20]
        foreach ($customLocaleMetadata as $locale => $metadata) {
            if (is_string($metadata)) {
                $customLocaleMetadata[$locale] = array('name' => $metadata, 'baseLocale' => 'en_US');
            }
        }
        return $customLocaleMetadata;
    }

    /**
     * Saves the list of custom languages.
     *
     * @param array  locale code => [name, baseLocale]
     */
    private static function setCustomLocaleMetadata($customLocaleMetadata) {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        W_Cache::getWidget('main')->config['customLocales'] = $json->encode($customLocaleMetadata);
        if (! defined('UNIT_TESTING')) {
            W_Cache::getWidget('main')->saveConfig();
        }
    }

    /**
     * If the locale is a custom locale (e.g., custom_12345), returns its base locale (e.g., fr_CA).
     * If the locale is a non-custom locale (e.g., fr_CA), simply returns the locale.
     *
     * @param $locale string  locale code for the translation, e.g., custom_12345
     * @return  the "base locale", i.e., the locale for filling in missing strings, e.g., fr_CA
     */
    public static function baseLocale($locale) {
        if (! self::isCustomLocale($locale)) { return $locale; }
        $customLocaleMetadata = self::customLocaleMetadata();
        return $customLocaleMetadata[$locale]['baseLocale'];
    }

    /**
     * Adds a new user-created language.
     *
     * @param $locale string  locale code for the translation, e.g., custom_12345
     * @param $name string  name of the locale, e.g., French (France)
     * @param $baseLocale string  locale for filling in missing strings, e.g., fr_CA
     */
    public static function addCustomLocaleMetadata($locale, $name, $baseLocale) {
        if (! self::isCustomLocale($locale)) { xg_echo_and_throw('Assertion failed (166452267)'); }
        $customLocaleMetadata = self::customLocaleMetadata();
        $customLocaleMetadata[$locale] = array('name' => $name, 'baseLocale' => $baseLocale);
        self::setCustomLocaleMetadata($customLocaleMetadata);
    }

    /**
     * Removes a user-created language.
     *
     * @param $locale string  locale code for the translation, e.g., custom_12345
     */
    public static function removeCustomLocaleMetadata($locale) {
        if (! self::isCustomLocale($locale)) { xg_echo_and_throw('Assertion failed (675660166)'); }
        $customLocaleMetadata = self::customLocaleMetadata();
        unset($customLocaleMetadata[$locale]);
        self::setCustomLocaleMetadata($customLocaleMetadata);
    }

    /**
     * Returns whether a decentralized message catalog exists for the current locale.
     *
     * @return boolean  whether a custom message catalog exists
     */
    public static function currentLocaleHasCustomCatalog() {
        if (is_null(W_Cache::getWidget('main')->config['localeHasCustomCatalog'])) {
            self::updateLocaleConfig();
        }
        return W_Cache::getWidget('main')->config['localeHasCustomCatalog'];
    }

    /**
     * Sets the current locale and associated config properties.
     * Note that this does not change the value of XG_LOCALE.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     */
    public static function setCurrentLocale($locale) {
        W_Cache::getWidget('main')->config['locale'] = $locale;
        self::updateLocaleConfig();
    }

    /**
     * Updates and saves the locale-related config properties.
     * Note that this function incurs a performance hit in that it invalidates the caches,
     * so call it only when necessary (i.e., only when the translation that changed is the current translation).
     */
    public static function updateLocaleConfig() {
        error_log('updateLocaleConfig');
        // Not XG_LOCALE, as the current locale may have just changed [Jon Aquino 2007-08-09]
        $locale = W_Cache::getWidget('main')->config['locale'];
        W_Cache::getWidget('main')->config['localeHasCustomCatalog'] = file_exists(self::customPhpCatalogPath($locale)) ? 1 : 0;
        W_Cache::getWidget('main')->config['languageVersion'] += 1;
        if (! defined('UNIT_TESTING')) {
            W_Cache::getWidget('main')->saveConfig();
        }
        NF_Controller::invalidateCache(NF::INVALIDATE_ALL);
    }

    /**
     * Returns the path for the centralized PHP message catalog.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @return  the absolute path to the file
     */
    public static function phpCatalogPath($locale) {
        return XG_App::includePrefix() . '/lib/XG_MessageCatalog_' . $locale . '.php';
    }

    /**
     * Returns the path for the decentralized PHP message catalog.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @return  the absolute path to the file
     */
    public static function customPhpCatalogPath($locale) {
        return NF_APP_BASE . '/instances/main/messagecatalogs/XG_CustomMessageCatalog_' . $locale . '.php';
    }

    /**
     * Returns the path for the centralized JavaScript message catalog.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @return  the absolute path to the file
     */
    public static function javaScriptCatalogPath($locale) {
        return W_INCLUDE_PREFIX . '/xn_resources/widgets/shared/js/messagecatalogs/' . $locale . '.js';
    }

    /**
     * Returns the path for the decentralized JavaScript message catalog.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @return  the absolute path to the file
     */
    public static function customJavaScriptCatalogPath($locale) {
        return NF_APP_BASE . '/xn_resources/instances/shared/js/messagecatalogs/' . $locale . '.js';
    }

    /**
     * Returns a localized version of a string. The first argument is the message name, e.g., 'ADD_A_PHOTO'.
     * Subsequent arguments are substitution values (if the message contains sprintf format elements).
     *
     * @param $args array the message name, plus optional substitution values
     * @return string the localized string
     * @see xg_html()
     */
    public static function text($args) {
        // Add dummy substitution values to prevent extra %s's from failing [Jon Aquino 2007-09-07]
        $args[] = $args[] = '';
        $text = self::getCustomCatalog()->text($args);
        if ($text != $args[0]) { return $text; }
        $text = self::getCatalog()->text($args);
        if ($text != $args[0]) { return $text; }
        return self::getDefaultCatalog()->text($args);
    }

    /**
     * Returns a localized version of a number. The first argument is the number, and the second a bool
     * to indicate whether its needs the decimal point or not.  For fr_CA: 25 000,00 | de_DE: 25.000,00
     * default: 25,000.00
     *
	 * @param $number number to be
	 * @param $decimal bool to indicate a decimal number or not, default is false
     * @return string the localized number
     */
    public static function number($number, $decimal=false) {
    	$decimals = $decimal ? 2 : 0;
    	// Default - return
    	$arr = self::getNumberFormat();
    	return number_format($number, $decimals, $arr['decimal'], $arr['thousand']);
    }
    /**
	 *  Returns current number format info
     *
     *  @return     {thousand,decimal}
     */
    public static function getNumberFormat() {
    	$arr = self::getCustomCatalog()->numformat();
    	if (sizeof($arr) == 0) $arr = self::getCatalog()->numformat();
    	if (sizeof($arr) == 0) $arr = self::getDefaultCatalog()->numformat();
		return array('thousand' => $arr['THOUSAND_SEPARATOR'], 'decimal' => $arr['DECIMAL_POINT']);
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
        // Add dummy substitution values to prevent extra %s's from failing [Jon Aquino 2007-09-07]
        $args[] = $args[] = '';
        $url = self::getCustomCatalog()->url($args);
        if ($url != $args[0]) {
            return xg_cdn($url);
        }
        $url = self::getCatalog()->url($args);
        if ($url != $args[0]) {
            return xg_cdn($url);
        }
        return xg_cdn(self::getDefaultCatalog()->url($args));
    }

    /** Mapping from widget-config titles to message-catalog names. */
    // _TAB_TEXT prefix will trigger "This text is for a header tab" note to be
    // displayed in the Language Editor [Jon Aquino 2007-08-20]
    protected static $widgetTitleNames = array(
            'Blog' => 'BLOG',
            'RSS' => 'RSS',
            'OpenSocial' => 'OPENSOCIAL_TAB_TEXT',
            'Forum' => 'FORUM_TAB_TEXT',
            'Videos' => 'VIDEOS_TAB_TEXT',
            'Photos' => 'PHOTOS_TAB_TEXT',
            'Events' => 'EVENTS_TAB_TEXT',
            'Text Box' => 'TEXT_BOX',
            'Groups' => 'GROUPS_TAB_TEXT',
            'Notes' => 'NOTES_TAB_TEXT',
            'Music' => 'MUSIC',
            'Chat' => 'CHAT');

    /**
     * If the given widget title is the default English title for the widget,
     * translate it into the current language.
     *
     * @param $widgetTitle string  The title of the widget
     * @return string  A translated title if the title is the English default; otherwise, the title unchanged
     */
    public static function translateDefaultWidgetTitle($widgetTitle) {
        $tabName = self::$widgetTitleNames[$widgetTitle];
        if (! $tabName) { $tabName = '<dummy>'; }
        $translatedTitle = self::getCustomCatalog()->text(array($tabName));
        if ($translatedTitle != $tabName) { return $translatedTitle; }
        $translatedTitle = self::getCustomCatalog()->translateDefaultWidgetTitle($widgetTitle); // Deprecated [Jon Aquino 2007-08-20]
        if ($translatedTitle != $widgetTitle) { return $translatedTitle; }
        $translatedTitle = self::getCatalog()->text(array($tabName));
        if ($translatedTitle != $tabName) { return $translatedTitle; }
        $translatedTitle = self::getCatalog()->translateDefaultWidgetTitle($widgetTitle); // Deprecated [Jon Aquino 2007-08-20]
        if ($translatedTitle != $widgetTitle) { return $translatedTitle; }
        $translatedTitle = self::getDefaultCatalog()->text(array($tabName));
        if ($translatedTitle != $tabName) { return $translatedTitle; }
        $translatedTitle = self::getDefaultCatalog()->translateDefaultWidgetTitle($widgetTitle); // Deprecated [Jon Aquino 2007-08-20]
        if ($translatedTitle != $widgetTitle) { return $translatedTitle; }
        return $widgetTitle;
    }

    /** Singleton instance of the decentralized message catalog */
    private static $customCatalog;

    /** Singleton instance of the centralized message catalog */
    private static $catalog;

    /** Singleton instance of the default message catalog */
    private static $defaultCatalog;

    /**
     * Returns the singleton instance of the decentralized message catalog.
     *
     * @return Object  The custom message catalog, e.g., XG_CustomMessageCatalog_fr_CA
     */
    private static function getCustomCatalog() {
        if (is_null(self::$customCatalog) && self::currentLocaleHasCustomCatalog()) {
            $path = self::customPhpCatalogPath(XG_LOCALE);
            XG_App::includeFileOnce($path, false); // Full path, don't add prefix
            $klass = str_replace('.php', '', basename($path));
            self::$customCatalog = new $klass;
        } elseif (is_null(self::$customCatalog)) {
            XG_App::includeFileOnce('/lib/XG_NullMessageCatalog.php');
            self::$customCatalog = new XG_NullMessageCatalog();
        }
        return self::$customCatalog;
    }

    /**
     * Returns the singleton instance of the centralized message catalog.
     *
     * @return Object  The "base" message catalog, e.g., XG_MessageCatalog_fr_CA
     */
    private static function getCatalog() {
        if (is_null(self::$catalog)) {
            $path = self::phpCatalogPath(self::baseLocale(XG_LOCALE));
            $klass = str_replace('.php', '', basename($path));
            if (! class_exists($klass)) { // BAZ-5378 [Jon Aquino 2007-11-27]
                XG_App::includeFileOnce($path, false); // Full path, don't add prefix
            }
            self::$catalog = new $klass;
        }
        return self::$catalog;
    }

    /**
     * Returns the singleton instance of the default message catalog.
     *
     * @return Object  The default message catalog: XG_CustomMessageCatalog_en_US
     */
    private static function getDefaultCatalog() {
        if (self::baseLocale(XG_LOCALE) == 'en_US') { return self::getCatalog(); }
        if (is_null(self::$defaultCatalog)) {
            $path = self::phpCatalogPath('en_US');
            $klass = str_replace('.php', '', basename($path));
            if (! class_exists($klass)) { // BAZ-5378 [Jon Aquino 2007-11-27]
                XG_App::includeFileOnce($path, false); // Full path, don't add prefix
            }
            self::$defaultCatalog = new $klass;
        }
        return self::$defaultCatalog;
    }

    /**
     * Returns whether the locale has been created by the user.
     *
     * @return boolean  whether the locale is not one of the built-in locales
     */
    public static function isCustomLocale($locale) {
        return mb_strpos($locale, 'custom_') !== false;
    }

    /**
     * Returns the name of the locale.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @return string  name of the locale, e.g., French (France)
     */
    public static function name($locale) {
        $localesAndNames = self::localesAndNames();
        return $localesAndNames[$locale];
    }

    /**
     * Sets the XG_LOCALE constant appropriately given the network's
     * configuration and availability of message catalog files
     */
    public static function setXgLocale() {
        $locale = W_Cache::getWidget('main')->config['locale'];
        /* If it's a built-in locale, assume the file exists */
        if (! isset(self::$nonCustomLocalesAndNames[$locale])) {
            $phpCatalogPath = self::customPhpCatalogPath($locale);
            if (! is_readable($phpCatalogPath)) {
                self::setCurrentLocale('en_US');
                $locale = 'en_US';
            }
        }
        define('XG_LOCALE', $locale);
    }


    /**
	 *  Sorts array using a locale-specific order and returns the sorted array
     *
     *  @param      $list   array	Source array
     *  @return     array
	 */
	protected static function _sort($list, $function) {
		$orig = setlocale(LC_COLLATE, "0");
		setlocale(LC_COLLATE, XG_LOCALE);
		$function($list, SORT_LOCALE_STRING);
		setlocale(LC_COLLATE, $orig);
		return $list;
    }
    public static function sort($list) {
    	return self::_sort($list, 'sort');
    }
	public static function ksort($list) {
    	return self::_sort($list, 'ksort');
    }
	public static function asort($list) {
    	return self::_sort($list, 'asort');
    }
}
