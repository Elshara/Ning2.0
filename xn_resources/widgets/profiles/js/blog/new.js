dojo.provide('xg.profiles.blog.new');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

// xg.profiles.blog.new is not allowed because "new" is a reserved word

xg.profiles.blog['new'] = {

    validate: function(form) {
        var errors = { };

        // Clean up title and body before validating
        form['post_title'].value = dojo.string.trim(form['post_title'].value);
        form['post_istext'].value = 'true';

        // Body is required unless we're saving as draft
        if (form['post_action'].value != 'draft') {
            errors = xg.index.util.FormHelper.validateRequired(errors, form, 'post_body', xg.profiles.nls.html('pleaseEnterPostBody'));
            if (dojo.string.trim(form['post_body'].value).match(/^(<br\/?>|&nbsp;)+$/)) {
                errors['post_body'] = xg.profiles.nls.html('pleaseEnterValueForPost');
            }
        }
        return errors;
    }

};

xg.addOnRequire(function() {

    // Disable/enable date/time entry based on 'post_when' radio
    var timedate = dojo.byId('post_when_timedate');
    dojo.event.connect(dojo.byId('post_when_now'),'onclick', function() { dojo.html.addClass(timedate, 'disabled'); });
    dojo.event.connect(dojo.byId('post_when_later'),'onclick', function() { dojo.html.removeClass(timedate, 'disabled'); });

    // Attach to the various form submission buttons to work around IE 6's broken
    // <button/> handling
    var hiddenPostAction = dojo.byId('post_action');
    dojo.lang.forEach(['draft','preview','publish'], function(status) {
        var el = dojo.byId('post_' + status);
        if (el) { dojo.event.connect(el,'onclick',function() {
            hiddenPostAction.value  = status;
        }); }
    }, true);

    // When the delete button is clicked, pop up a confirmation alert.
    // If that succeeds, update the hidden post action and submit the form
    // without validation.
    var deleteButton = dojo.byId('post_delete');
    if (deleteButton) {
        dojo.event.connect(deleteButton, 'onclick', function(evt) {
            dojo.event.browser.stopEvent(evt);
            xg.shared.util.confirm({ bodyText: xg.profiles.nls.text('reallyDeleteThisPost'), onOk: dojo.lang.hitch(this, function() {
                hiddenPostAction.value = 'delete';
                dojo.byId('post_form').submit();
            }) });
        });
    };

    // Validate the form when submitting
    var postForm = dojo.byId('post_form');
    if (postForm) {
        xg.index.util.FormHelper.configureValidation(postForm, xg.profiles.blog['new'].validate);
    }

});
