/** $Id: $
 * 	Quick Add Share dialog scripting
 *
 * 	If page contains "a.share" links, id value of the HREF url is used as a content ID.
 */
dojo.provide('xg.index.quickadd.share');
dojo.require('xg.index.invitation.FriendList'); /** @explicit for quickadd/share.php */

(function(){
    var qa = xg.index.quickadd, friendList;

    xg.index.quickadd.listen('share', 'load', function(){
        var f = dojo.byId('xg_quickadd_share');
        var title = document.title, url = window.location.href;
        var appName = ning.CurrentApp.name;
        if (title.substr(title.length-appName.length) == appName) {
            title = title.substr(0,title.length-appName.length).replace(/\s*-\s*$/,'');
        }
        if (title.match(/^\s*$/)) {
            title = appName;
        }

        //
        // Form basics
        //
        var validate = function() {
            var errors = {};
            if ( dojo.string.trim(f.emailAddresses.value) == '' && friendList.selectedFriendCount == 0) {
                errors.x = xg.index.nls.text('youNeedToAddEmailRecipient');
            }
            return errors;
        }
        f.title.value = title;
        f.url.value = url;
        var share_links = xg.$$('a.share');
        if (share_links.length == 1 && share_links[0].href.match(/id=([^&]+)/)) {
            f.contentId.value = decodeURIComponent(decodeURIComponent(RegExp.$1)); // decode twice
        }
        xg.listen(f.cancel, 'onclick', qa, qa.cancelDialog);
        xg.listen(f, 'onsubmit', function(evt) {
            xg.stop(evt);
            if (friendList) friendList.updateHiddenInputs();
            if (!qa.validateForm(f, validate)) {
                return;
            }
            qa.submitFormXhr({form: f, title: xg.index.nls.text('sendingLabel'), text: xg.index.nls.text('yourMessageIsBeingSent')});
        });

        //
        // Friend list toggle
        //
        if (xg.$('p.xj_toggle', f)) {
            var tgl = xg.$('p.xj_toggle', f).firstChild, arrow = tgl.firstChild, friends = xg.$('div.xj_friends', f);
            xg.listen(tgl, 'onclick', function(evt) {
                xg.stop(evt);
                if (friends.style.display != 'none') {
                    arrow.innerHTML = '&#9658;';
                    dojo.html.hide(friends);
                } else {
                    arrow.innerHTML = '&#9660;';
                    dojo.html.show(friends);
                }
                xg.shared.util.fixDialogPosition(xg.parent(f,'div.xg_floating_module'));
            });
            friendList = dojo.widget.manager.byNode(dojo.html.getElementsByClass('xj_friend_list', f)[0]);
            friendList.init();
        }

        //
        // Install external resources handlers
        //
        var init = function (a, url) {
            a = xg.$(a, f);
            a.href = url;
            a.onclick = function(evt) { f.cancel.value = xg.index.nls.text('done') }
        }

        init('a.service-myspace', 'http://www.myspace.com/Modules/PostTo/Pages/'
            + '?t=' + encodeURIComponent(title)
            + '&c=' + encodeURIComponent( xg.qh( title == appName ? xg.index.nls.text('checkPageOut', appName) : xg.index.nls.text('checkingOutTitle', title, appName) ) )
            + '&u=' + encodeURIComponent(url)
            + '&l=' + 2);

        init('a.service-twitter', 'http://twitter.com/home/'
                + '?status=' + encodeURIComponent('Checking out '+url));

        init('a.service-delicious', 'http://del.icio.us/post'
                + '?url=' + encodeURIComponent(url)
                + '&title=' + encodeURIComponent(title));

        init('a.service-facebook', 'http://www.facebook.com/share.php'
                + '?u=' + encodeURIComponent(url));

        init('a.service-stumbleupon', 'http://www.stumbleupon.com/submit'
                + '?url=' + encodeURIComponent(url)
                + '&title=' + encodeURIComponent(title));

        // Digg needs a working URL because their service scans the URL before submission starts, otherwise it throws an error
        init('a.service-digg', 'http://digg.com/submit'
                + '?phase=2'
                + '&url=' + encodeURIComponent(url)
                + '&title=' + encodeURIComponent(title));

    });

    xg.index.quickadd.listen('share', 'open', function(){
        var f = dojo.byId('xg_quickadd_share');
        xg.index.util.FormHelper.hideErrorMessages(f);
        f.emailAddresses.value = '';
        f.cancel.value = xg.index.nls.text('cancel');
        if (friendList) friendList.reset(false);
    });

})();
