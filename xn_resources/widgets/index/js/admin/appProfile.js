dojo.provide('xg.index.admin.appProfile');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

xg.index.admin.appProfile = {

    submitForm: function(evt, computeTimezone) {
        if (evt) {
            dojo.event.browser.stopEvent(evt);
        }
        xg.index.util.FormHelper.hideErrorMessages(dojo.byId('profile_form'));
        var errors = { };
        var errorCount = 0;
        // validate app name
        var appName = dojo.string.trim(dojo.byId('profile_app_name')).value;
        if (dojo.string.trim(appName).length === 0) {
            errors.name = xg.index.nls.html('pleaseEnterASiteName');
            errorCount++;
        } else if (appName.length > 64) {
            errors.name = xg.index.nls.html('pleaseEnterShorterSiteName');
            errorCount++;
        } else if (! appName.match(/^([-_!\\?\/ .:'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ])+$/)) {
            errors.name = xg.index.nls.html('siteNameHasInvalidCharacters');
            errorCount++;
        }
        //validate app description
        var appDescription = dojo.string.trim(dojo.byId('profile_app_description')).value;
        if (appDescription.length > 140) {
            errors.description = xg.index.nls.html('pleaseEnterShorterSiteDescription');
            errorCount++;
        }
        if (errorCount) {
            xg.index.util.FormHelper.showErrorMessages(dojo.byId('profile_form'), errors, xg.index.nls.html('thereIsAProblem'));
        }
        else {
            if (computeTimezone) {
                /* Compute timezone settings - BAZ-1628 */
                var now = new Date();
                var winter = new Date(now.getFullYear(), 0, 1, 12, 0, 0);
                var summer = new Date(now.getFullYear(), 6, 1, 12, 0, 0);
                var tzOffset = winter.getTimezoneOffset();
                var tzUseDST = (winter.getTimezoneOffset() == summer.getTimezoneOffset()) ? 0 : 1;
                // southern hemisphere
                if (winter.getTimezoneOffset() - summer.getTimezoneOffset() < 0) {
                    tzUseDST = -1;
                }
                var actionUrl = dojo.byId('profile_form').action;
                if (actionUrl.match(/\?/)) {
                    actionUrl += '&';
                } else {
                    actionUrl += '?';
                }
                actionUrl += 'tzOffset=' + tzOffset + '&tzUseDST=' + tzUseDST;
                dojo.byId('profile_form').action = actionUrl;
            }

            dojo.byId('profile_form').submit();
        }
    },

    handleLaunchBarSubmit: function(url, evt) {
        dojo.event.browser.stopEvent(evt);
        var form = dojo.byId('profile_form');
        if (form.successTarget && url) {
            form.successTarget.value = url;
        }
        xg.index.admin.appProfile.submitForm(null, true);
    },

    registerMaxLengthCounterOnAppDesc: function() {
        var maxCharactersForAppDesc = 140;
        var textarea = dojo.byId('profile_app_description');
        xg.shared.util.setAdvisableMaxLengthWithCountdown(textarea, maxCharactersForAppDesc);
    }
};

xg.addOnRequire(function() {
    dojo.event.connect(dojo.byId('profile_form'), 'onsubmit', xg.index.admin.appProfile, 'submitForm');
    xg.index.admin.appProfile.registerMaxLengthCounterOnAppDesc();
});
