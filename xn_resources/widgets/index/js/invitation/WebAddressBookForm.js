dojo.provide('xg.index.invitation.WebAddressBookForm');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

/**
 * A form for importing email addresses from web email services like Yahoo Mail, Hotmail, GMail, and AOL Mail.
 */
dojo.widget.defineWidget('xg.index.invitation.WebAddressBookForm', dojo.widget.HtmlWidget, {

    /** The form element */
    form: null,

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        this.form = this.getFragNodeRef(frag);
        dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.style.hide(this.getErrorDiv());
            var error = this.validate();
            if (error) {
                this.showError(error.html, error.divClass);
                return false;
            }
            this.form.action = xg.shared.util.addParameter(this.form.action, 'emailDomain', this.form.emailDomain.value);
            this.form.submit();
        }));
        dojo.event.connect(this.form.emailDomain, 'onchange', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (xg.index.util.FormHelper.selectedOption(this.form.emailDomain).value == '(other)') {
                this.showError(xg.index.nls.html('sorryWeDoNotSupport'), 'notification');
            } else {
                dojo.style.hide(this.getErrorDiv());
            }
        }));
    },

    /**
     * Displays an error message.
     *
     * @param html  HTML for the error message
     * @param divClass  CSS class for the container div
     */
    showError: function(html, divClass) {
        dojo.html.setClass(this.getErrorDiv(), divClass);
        this.getErrorDiv().getElementsByTagName('p')[0].innerHTML = html;
        dojo.style.show(this.getErrorDiv());
        xg.index.util.FormHelper.scrollIntoView(this.getErrorDiv());
    },

    /**
     * Checks the input entered by the user.
     *
     * @return  null if no problems exist; otherwise an object with "html" and "divClass" attributes
     */
    validate: function() {
        // Keep client-side and server-side validations in sync [Jon Aquino 2007-10-27]
        if (! dojo.string.trim(this.form.emailLocalPart.value).length) {
            return { divClass: 'errordesc', html: xg.index.nls.html('pleaseEnterEmailAddress') };
        }
        if (xg.index.util.FormHelper.selectedOption(this.form.emailDomain).value == '') {
            return { divClass: 'errordesc', html: xg.index.nls.html('pleaseSelectSecondPart') };
        }
        if (xg.index.util.FormHelper.selectedOption(this.form.emailDomain).value == '(other)') {
            return { divClass: 'notification', html: xg.index.nls.html('sorryWeDoNotSupport') };
        }
        var emailLocalPartSplit = this.form.emailLocalPart.value.split('@');
        if (emailLocalPartSplit.length == 2) {
            if (emailLocalPartSplit[1] == this.form.emailDomain.value) {
                this.form.emailLocalPart.value = emailLocalPartSplit[0];
            } else {
                return { divClass: 'errordesc', html: xg.index.nls.html('atSymbolNotAllowed') };                            
            }
        } else if (emailLocalPartSplit.length > 2) {
            return { divClass: 'errordesc', html: xg.index.nls.html('atSymbolNotAllowed') };            
        }
        if (! this.form.password.value.length) {
            return { divClass: 'errordesc', html: xg.index.nls.html('pleaseEnterPassword', dojo.string.escape('html', dojo.string.trim(this.form.emailLocalPart.value) + '@' + this.form.emailDomain.value)) };
        }
        return null;
  },

    /**
     * Retrieves or creates the error div.
     *
     * @return the div element
     */
    getErrorDiv: function() {
        if (! this.errorDiv) { this.errorDiv = dojo.byId('web_address_book_error'); }
        return this.errorDiv;
    }

});

