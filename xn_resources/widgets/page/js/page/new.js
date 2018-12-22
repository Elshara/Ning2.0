dojo.provide('xg.page.page.new');

/**
 * Behavior of the New Page page.
 */
(function() {

    var form = dojo.byId('add_page_form');

    var validate = function() {
        var errors = {};
        if (dojo.string.trim(form.title.value).length == 0) {
            errors.title = xg.page.nls.html('pleaseEnterTitle');
        }
        if (dojo.string.trim(form.description.value).length > form.description.getAttribute('_maxlength')) {
            errors.description = xg.page.nls.html('numberOfCharactersExceedsMaximum', dojo.string.trim(form.description.value).length, form.description.getAttribute('_maxlength'));
        }
        if (dojo.string.trim(form.description.value).length == 0) {
            errors.description = xg.page.nls.html('pleaseEnterContent');
        }
        return errors;
    };

    var showUploadingScreen = function() {
        dojo.html.hide(dojo.byId('form_section'));
        dojo.html.show(dojo.byId('spinner_section'));
        window.scrollTo(0, 0);
    }

    dojo.event.connect(form, 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        dojo.require('xg.index.util.FormHelper');
        if (! xg.index.util.FormHelper.runValidation(form, validate)) { return; }
        window.setTimeout(function() {
            form.submit();
        }, 0);
    });

}());
