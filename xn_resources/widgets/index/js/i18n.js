dojo.provide('xg.index.i18n');

/**
 * Useful functions for I18N. These functions get added to the individual "nls" packages.
 * See each of the "nls" packages for the available strings.
 */

dojo.lang.mixin(xg.index.i18n, {
    /**
     * Returns a localized, HTML-encoded version of a message. The first argument is the message name, e.g., 'signUpNow'.
     * Subsequent arguments are substitution values (if the message is parameterized).
     * These arguments should be HTML-encoded, e.g., use &amp; instead of &.
     * You can use dojo.string.escape('html', ...) to do the encoding.
     *
     * @param string name the message name, e.g., 'pleaseEnterYourName'
     * @param ... optional substitution strings and numbers
     * @return string the localized string, which will be HTML-encoded
     */
     html: function(name) {
         return this.text.apply(this, arguments).replace(/ & /g, ' &amp; ');
     },

    /**
     * Returns a localized, plain-text version of a message. The first argument is the message name, e.g., 'signUpNow'.
     * Subsequent arguments are substitution values (if the message is parameterized).
     *
     * @param string name the message name, e.g., 'pleaseEnterYourName'
     * @param ... optional substitution strings and numbers
     * @return string the localized string, which will be plain text (not HTML-encoded)
     */
    text: function(name) {
		var message = this[name] ? this[name] : name, args;
		if ("function" != typeof message) {
			return message;
		}
		for(var i = 1, args = []; i<arguments.length; i++) {
			args[i-1] = arguments[i];
		}
        return message.apply(this, args);
    }

});
