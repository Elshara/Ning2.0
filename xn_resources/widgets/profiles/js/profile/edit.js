dojo.provide('xg.profiles.profile.edit');

dojo.require('xg.shared.util');

(function() {
    var form = dojo.byId('xg_return_to_default_form');
    if (form) {
        dojo.event.connect(form, 'onsubmit', function(evt) {
            dojo.event.browser.stopEvent(evt);
            xg.shared.util.confirm({ bodyText: xg.profiles.nls.html('returnToDefaultWarning'), onOk: function() { form.submit(); } })});
    }
})();
