dojo.provide('xg.index.index.feedback');
dojo.require('xg.index.util.FormHelper');

xg.index.index.feedback = {
    validate: function(form) {
        var errors = {};
        errors = xg.index.util.FormHelper.validateRequired(errors, form, 'feedback', xg.index.nls.html('pleaseEnterSomeFeedback'));
        return errors;
    }
};

xg.addOnRequire(function() {
    xg.index.util.FormHelper.configureValidation(dojo.byId('xg_feedback_form'), xg.index.index.feedback.validate);
});

