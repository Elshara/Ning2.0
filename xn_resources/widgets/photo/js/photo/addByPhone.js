dojo.provide('xg.photo.photo.addByPhone');
dojo.require('dojo.lfx.*');

function generateNewEmailAddress() {
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
            } /* email address and a place to show it? */
        } /* load */
    }); /* bind */
}
