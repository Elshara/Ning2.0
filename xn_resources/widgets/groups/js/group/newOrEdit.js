dojo.provide('xg.groups.group.newOrEdit');

dojo.require('xg.shared.util');

/**
 * Behavior for the New Group and Edit Group pages.
 */
(function() {

    var form = dojo.byId('add_group_form');
    var urlField = dojo.byId('groupurl');

    var validate = function() {
        var errors = {};
        if (dojo.string.trim(form.title.value).length == 0) {
            errors.title = xg.groups.nls.html('pleaseChooseAName');
        }
        if (form.description.value.length > form.description.getAttribute('_maxlength')) {
            errors.description = xg.groups.nls.html('descriptionTooLong', form.description.value.length, form.description.getAttribute('_maxlength'));
        }
        if (urlField && dojo.string.trim(urlField.value).length == 0) {
            errors.url = xg.groups.nls.html('pleaseChooseAUrl');
        }
        if (urlField && ! dojo.string.trim(urlField.value).match(/^[a-z0-9_]*$/i)) {
            errors.url = xg.groups.nls.html('urlCanContainOnlyLetters');
        }
        return errors;
    };

    dojo.event.connect(form, 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        dojo.require('xg.index.util.FormHelper');
        if (! xg.index.util.FormHelper.runValidation(form, validate)) { return; }
        var content = { title: form.title.value };
        if (urlField) { content.url = urlField.value; }
        dojo.io.bind({
            url: form.getAttribute('_checkNameUrl'),
            method: 'post',
            content: content,
            preventCache: true,
            encoding: 'utf-8',
            mimetype: 'text/javascript',
            load: dojo.lang.hitch(this, function(type, data, event){
                if (! data.nameTaken && ! data.urlTaken) {
                    form.submit();
                    return;
                }
                var errors = {};
                if (data.nameTaken) { errors.title = xg.groups.nls.html('nameTaken'); }
                if (data.urlTaken) { errors.url = xg.groups.nls.html('urlTaken'); }
                xg.index.util.FormHelper.runValidation(form, function() { return errors; });
            })
        });
    });
    if (dojo.byId('privacy_options')) {
        var radioButtons = dojo.byId('privacy_options').getElementsByTagName('input');
        var updateWhetherShowing = function() {
            dojo.style.setShowing(dojo.byId('invitation_options'), radioButtons[1].checked);
        }
        updateWhetherShowing();
        dojo.event.connect([radioButtons[0], radioButtons[1]], 'onclick', function(event) {
            updateWhetherShowing();
        });
    }
    if (urlField) {
        var generatedValue = '';
        dojo.event.connect(form.title, 'onkeyup', function(event) {
            if (urlField.value != generatedValue) { return; }
            urlField.value = generatedValue = form.title.value.replace(/[^a-z0-9_]/ig, '').toLowerCase();
        });
    }
    form.title.focus();
    xg.shared.util.setMaxLength(form.description, form.description.getAttribute('_maxlength'));
}());

