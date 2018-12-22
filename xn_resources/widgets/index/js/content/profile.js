dojo.provide("xg.index.content.profile");

xg.index.content.profile = {
    
    submitForm: function(evt) {
        if (evt) {
            dojo.event.browser.stopEvent(evt);
        }
        var form = dojo.byId('profile_form');
        form.submit();
    },
    
    handleLaunchBarSubmit: function(url, evt) {
        dojo.event.browser.stopEvent(evt);
        var form = dojo.byId('profile_form');
        if (form.successTarget && url) {
            form.successTarget.value = url;
        }
        xg.index.content.profile.submitForm();
    }

};

xg.addOnRequire(function() {    
    var form = dojo.byId('profile_form');
    dojo.event.connect(form, 'onsubmit', xg.index.content.profile, 'submitForm');
});
