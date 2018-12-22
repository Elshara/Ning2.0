dojo.provide('xg.profiles.profile.profileQuestionForm');

dojo.require('xg.index.util.FormHelper');

/**
 * Behavior for the "profile questions" section of the Create Profile and Edit Profile pages.
 * The validation assumes that the form's id is "profile_form", if element doesn't exist, "settings_form" is used.
 */
xg.profiles.profile.profileQuestionForm = {

    validations: [],

    addValidation: function(validationFunction, validationArguments) {
        xg.profiles.profile.profileQuestionForm.validations.push({ 'func': validationFunction, 'args': validationArguments });
    },

    validate: function(form) {
        var errors = {};
        for (var i in xg.profiles.profile.profileQuestionForm.validations) {
            var args = [errors, form];
            dojo.lang.forEach(xg.profiles.profile.profileQuestionForm.validations[i].args, function(a) { args.push(a); }, true);
            errors = xg.profiles.profile.profileQuestionForm.validations[i].func.apply(null, args);
        }
        return errors;
    }

};

xg.addOnRequire(function() {
	var f = dojo.byId('profile_form') || dojo.byId('settings_form');
	xg.index.util.FormHelper.configureValidation(f, xg.profiles.profile.profileQuestionForm.validate);
});
