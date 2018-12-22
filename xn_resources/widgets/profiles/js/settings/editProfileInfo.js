dojo.provide('xg.profiles.settings.editProfileInfo');
dojo.require('dojo.lfx.*');

/**
 * Behavior for the Edit Profile info page.
 */
(function() {

    var doOnClick = function(id, handler) {
        var button = dojo.byId(id);
        if (button) {
            dojo.event.connect(button, 'onclick', function(evt) {
                dojo.event.browser.stopEvent(evt);
                handler();
            }); /* connect */
        } /* button? */
    };

    doOnClick('xg_profiles_settings_email_generate', function() {
            dojo.io.bind({
                'url': xg.global.requestBase + '/profiles/profile/newUploadEmailAddress?xn_out=json',
                'method': 'POST',
                'mimetype': 'text/json',
                preventCache: true,
                encoding: 'utf-8',
                'load': function (type, data, evt) {
                    var show = dojo.byId('xg_profiles_settings_email_show');
                    if (show && data.uploadEmailAddress) {
                        var address = dojo.string.escape('html', data.uploadEmailAddress);
                        show.href = 'mailto:' + address;
                        show.innerHTML = address;
                        dojo.lfx.html.highlight(show, '#ffee7d', 1000).play();
                    }
                }
            });
    });

})();
