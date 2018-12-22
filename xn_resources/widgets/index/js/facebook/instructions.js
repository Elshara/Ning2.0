dojo.provide('xg.index.facebook.instructions');
dojo.require('xg.index.util.FormHelper');

xg.index.facebook.instructions = {

    maxTabNameLength: 15,

    submitForm: function(evt) {
        if (evt) {
            dojo.event.browser.stopEvent(evt);
        }
        xg.index.util.FormHelper.hideErrorMessages(dojo.byId('setupNewEm'));
        var errors = { };
        var errorCount = 0;

        // validate facebook tabName
        var tabName = dojo.string.trim(dojo.byId('tabName').value);
        if (tabName.length == 0) {
            errors.tabName = xg.index.nls.html('pleaseEnterFbTabName');
            errorCount++;
        } else if (tabName.length > this.maxTabNameLength) {
            errors.tabName = xg.index.nls.html('pleaseEnterValidFbTabName', this.maxTabNameLength);
            errorCount++
        }
        // validate facebook api key
        var fbApiKey = dojo.string.trim(dojo.byId('fbApiKey').value);
        if (fbApiKey.length == 0) {
            errors.fbApiKey = xg.index.nls.html('pleaseEnterFbApiKey');
            errorCount++;
        } else if ((fbApiKey.length > 32) || ! fbApiKey.match(/^[0-9A-Fa-f]+$/)) {
            errors.fbApiKey = xg.index.nls.html('pleaseEnterValidFbApiKey');
            errorCount++
        }

        // validate facebook api secret
        var fbApiSecret = dojo.string.trim(dojo.byId('fbApiSecret').value);
        if (fbApiSecret.length == 0) {
            errors.fbApiSecret = xg.index.nls.html('pleaseEnterFbApiSecret');
            errorCount++;
        } else if ((fbApiSecret.length > 32) || ! fbApiSecret.match(/^[0-9A-Fa-f]+$/))
 {
            errors.fbApiSecret = xg.index.nls.html('pleaseEnterValidFbApiSecret');
            errorCount++
        }

        if (errorCount) {
            xg.index.util.FormHelper.showErrorMessages(dojo.byId('setupNewEm'), errors, xg.index.nls.html('thereIsAProblem'));
        }
        else {
            dojo.byId('setupNewEm').submit();
        }
    }

};

xg.addOnRequire(function() {
    dojo.event.connect(dojo.byId('xj_fb_complete'), 'onclick', xg.index.facebook.instructions, 'submitForm');
});
