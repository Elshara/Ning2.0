dojo.provide('xg.video.video.edit');

(function() {

    var validate = function() {
        var errors = {};
        var descriptionMaxLength = 4000;
        if (form.description.value.length > descriptionMaxLength) {
            errors.description = xg.video.nls.html('numberOfCharactersExceedsMaximum', form.description.value.length, descriptionMaxLength);
        }
        return errors;
    };

    dojo.require('xg.index.util.FormHelper');
    var form = dojo.byId('edit_video_form');
    xg.index.util.FormHelper.configureValidation(form, validate);


}());
