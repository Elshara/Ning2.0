<?php
/**
 * Empty implementation of a message catalog.
 */
class XG_NullMessageCatalog {

    /**
     * Returns a localized version of a string. The first argument is the message name, e.g., 'ADD_A_PHOTO'.
     * Subsequent arguments are substitution values (if the message contains sprintf format elements).
     *
     * @param $args array the message name, plus optional substitution values
     * @return string the localized string
     * @see xg_html()
     */
    public static function text($args) {
        return $args[0];
    }

    /**
     * If the given widget title is the default English title for the widget,
     * translate it into the current language.
     *
     * @param $widgetTitle string  The title of the widget
     * @return string  A translated title if the title is the English default; otherwise, the title unchanged
     *
     * @deprecated  Use XG_LanguageHelper::translateDefaultWidgetTitle instead
     */
    public static function translateDefaultWidgetTitle($widgetTitle) {
        return $widgetTitle;
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
        return $args[0];
    }
    
    /**
     * Default number formats.
     */
    public static function numformat() {
    	return array();
    }
}
