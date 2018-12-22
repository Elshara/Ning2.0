dojo.provide('xg.chat.onlinestatus');
dojo.require('xg.shared.util');

(function() {

    var onlineStatus = dojo.byId('xj_online_status');
    var cookieName = dojo.html.getAttribute(onlineStatus, '_cookieName');
    var frame = dojo.byId('chatRoom');
    var cookieExpiresInDays = 10*365;  // how long should the status cookie persist (in days)
    onlineStatus.disabled = false;
    dojo.html.removeClass(onlineStatus, 'disabled');
    dojo.html.removeClass(onlineStatus, 'xg_lightfont');
    onlineStatus.style.display = '';

    /**
     * set a cookie
     *
     * @param name string  name of the cookie
     * @param value string  value to set for the cookie
     * @param expiresInDays integer|null  when the cookie should expire in days, or 0/null for session cookie
     */
    var setCookie = function(name, value, expiresInDays) {
        var expires = null;
        if (expiresInDays) {
            var now = new Date();
            expires = new Date();
            var daysToMs = 24 * 60 * 60 * 1000;
            expires.setTime(now.getTime() + (daysToMs * expiresInDays));
        }
        document.cookie = encodeURIComponent(name) + '=' + encodeURIComponent(value) + '; path=/' + (expires ? '; expires=' + expires.toGMTString() : '');
    }

    xg.listen(onlineStatus, 'onchange', this, function(evt) {
        setCookie(cookieName, onlineStatus.value, cookieExpiresInDays);
        var startChatUrl = dojo.html.getAttribute(onlineStatus, '_startChatUrl');
        startChatUrl = xg.shared.util.addParameter(xg.shared.util.removeParameter(startChatUrl, 'userOnlineStatus'), 'userOnlineStatus', onlineStatus.value);
        dojo.io.bind({
            url: startChatUrl,
            method: 'post',
            content: { },
            preventCache: true,
            mimetype: 'text/json',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event) {
                if (! ('token' in data)) {
                    // some error, just refresh the screen
                    window.location.href = window.location.href;
                } else {
                    var frameUrl = frame.src;
                    var urlParts = frameUrl.split('?', 2);
                    var newFrameUrl = urlParts[0] + '?' + 'a=' + data.appSubdomain + '&h=' + data.appHost + '&t=' + data.token;
                    frame.src = newFrameUrl;
                }
            })
        });
    });

})();
