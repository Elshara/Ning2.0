dojo.provide('xg.photo.photo.new');


/**
 * Javascript for the new and edit actions.
 */
(function() {

    var photoCount = 8;

    var isAddPhotoPage = function() {
        return dojo.byId('upload-photo-step') != null;
    }

    var validate = function() {
        var errors = {}

        if (isAddPhotoPage()) {
            var numPhotos = 0;

            for (var idx = 1; idx <= photoCount; idx++) {
                if (dojo.string.trim(form.elements['photo0' + idx].value).length > 0) {
                    if (dojo.string.trim(form.elements['photo0' + idx].value).length > 0) {
                        numPhotos++;
                    }
                }
            }

            if (numPhotos == 0) {
                errors.photo01 = xg.photo.nls.html('pleaseSelectPhotoToUpload');
            }
        }
        return errors;
    };

    var form = dojo.byId('xg_body').getElementsByTagName('form')[0];

    dojo.require('xg.index.util.FormHelper');
    xg.index.util.FormHelper.configureValidation(form, validate);

    dojo.event.connect(form, 'onsubmit', function(event) {
        if (!dojo.lang.isEmpty(validate())) {
            dojo.event.browser.stopEvent(event);
            return;
        }
        if (!isAddPhotoPage()) {
            return;
        }
        dojo.event.browser.stopEvent(event);

        // Show the Uploading Photo message in a setTimeout call; otherwise the spinner
        // may fail to appear or stop spinning during the upload  [Jon Aquino 2006-07-25]
        window.setTimeout(function() {
            form.submit();
            dojo.html.hide(dojo.byId('add_photos_module'));
            dojo.html.show(dojo.byId('adding_photos_module'));
            window.scrollTo(0, 0);
        }, 0);
    });

    var last = function(a) {
        return a[a.length-1];
    }

    var unescapeHtml = function(html) {
        var x = document.createElement('textarea');
        x.innerHTML = html;
        return x.value;
    }

    if (dojo.byId('my-tag-cloud')) {
        dojo.lang.forEach(dojo.byId('my-tag-cloud').getElementsByTagName('a'), function(a) {
            dojo.event.connect(a, 'onclick', function(event) {
                dojo.event.browser.stopEvent(event);
                xg.photo.addTag('enter-tags', unescapeHtml(a.innerHTML));
            });
        });
    }

}());
