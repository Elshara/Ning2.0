dojo.provide('xg.video.video.new');

dojo.require('xg.shared.util');

(function() {

    var form = dojo.byId('add_video_module').getElementsByTagName('form')[0];

    var validate = function() {
        var errors = {}
        var embedCodeMaxLength = 4000;
        if (dojo.string.trim(form.file.value).length == 0) {
            errors.file = xg.video.nls.html('pleaseSelectVideoToUpload');
        }
        return errors;
    };

    var showUploadingScreen = function() {
        dojo.html.hide(dojo.byId('add_video_module'));
        dojo.html.show(dojo.byId('adding_video_module'));
        window.scrollTo(0, 0);
    }

    dojo.event.connect(form, 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        dojo.require('xg.index.util.FormHelper');
        if (! xg.index.util.FormHelper.runValidation(form, validate)) { return; }
        var extensionValid = false;
        if (dojo.string.trim(form.file.value).match(/\./)) {
            dojo.lang.forEach([/\.mov/, /\.mpg/, /\.mpeg/, /\.mp4/, /\.avi/, /\.3gp/, /\.wmv/], function(extension) {
                if (form.file.value.toLowerCase().match(extension)) { extensionValid = true; }
            });
        } else {
            extensionValid = true;
        }
        //dojo.dom.removeNode(dojo.byId('embed_section').getElementsByTagName('textarea')[0]);
        // Show the Uploading Video message in a setTimeout call; otherwise the spinner
        // may fail to appear or stop spinning during the upload  [Jon Aquino 2006-07-25]
        var submit = dojo.lang.hitch(this, function() {
            window.setTimeout(function() {
                form.submit();
                showUploadingScreen();
            }, 0);
        });
        if (extensionValid) { submit(); }
        else { xg.shared.util.confirm({ bodyText: xg.video.nls.text('fileIsNotAMov'), onOk: submit }); }
    });

    if (window.location.href.match(/test_uploading_screen/)) {
        showUploadingScreen();
    }

}());