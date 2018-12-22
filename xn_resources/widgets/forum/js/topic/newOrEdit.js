dojo.provide('xg.forum.topic.newOrEdit');

/**
 * Behavior for the New Topic and Edit Topic pages.
 */
(function() {

    var form = dojo.byId('add_topic_form');

    var validate = function() {
        var errors = {};
        if (dojo.string.trim(form.title.value).length == 0) {
            errors.title = xg.forum.nls.html('pleaseEnterTitle');
        }
        if (dojo.string.trim(form.description.value).length > form.description.getAttribute('_maxlength')) {
            errors.description = xg.forum.nls.html('numberOfCharactersExceedsMaximum', dojo.string.trim(form.description.value).length, form.description.getAttribute('_maxlength'));
        }
        if (dojo.string.trim(form.description.value).length == 0) {
            errors.description = xg.forum.nls.html('pleaseEnterFirstPost');
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
        // Show the spinner_section in a setTimeout call; otherwise the spinner
        // may fail to appear or stop spinning during the upload  [Jon Aquino 2007-01-17]
        window.setTimeout(function() {
            form.submit();
            if (form.file1&&dojo.string.trim(form.file1.value).length || form.file2&&dojo.string.trim(form.file2.value).length || form.file3&&dojo.string.trim(form.file3.value).length) {
                showUploadingScreen();
            }
        }, 0);
    });
    form.title.focus();
}());