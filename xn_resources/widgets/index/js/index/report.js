dojo.provide('xg.index.index.report');
dojo.require('xg.index.util.FormHelper');


xg.index.index.report = {
    validate: function(form) {
        var errors = {};
        errors = xg.index.util.FormHelper.validateRequired(errors, form, 'issue', xg.index.nls.html('pleaseProvideADescription'));
        return errors;
    }
};

xg.addOnRequire(function() {
    xg.index.util.FormHelper.configureValidation(dojo.byId('xg_report_form'), xg.index.index.report.validate);
});

