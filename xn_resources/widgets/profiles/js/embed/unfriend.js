dojo.provide('xg.profiles.embed.unfriend');

dojo.require('xg.shared.util');
dojo.require("dojo.lfx.*");

// TODO: Rename this file to UnfriendLink.js. Change xg.profiles.embed.unfriend to xg.profiles.embed.UnfriendLink. [Jon Aquino 2008-09-01]
dojo.widget.defineWidget('xg.profiles.embed.unfriend.UnfriendLink', dojo.widget.HtmlWidget, {

    /* The URL to post to */
    _url: '<required>',
    _updateurl: null,
    /* The name of the user; used in confirmation dialogs */
    _username: '',
    /* Is this a profile page?  Used to set styles  */
    _isProfilePage: false,
    /* Number of friends per row */
    _numFriendsPerRow: 3,
    _fadeDone: false,
    _newListHtmlLoaded: false,
    _newListHtml: null,

    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        this.itemnode = a.parentNode;
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.confirmDialog(a);
        }));
    },
    updateListHtml: function() {
        if (this._fadeDone && this._newListHtmlLoaded) {
            x$('div.xj_list_container').html(this._newListHtml);

            // reinit dojo widgets
            dojo.lang.forEach(x$('a.smalldelete'), function(a) {
                xg.shared.util.parseWidgets(a);
            });
        }
    },
    fixFriendStatus: function(htmlfrag) {
        var ddr = dojo.byId('relationship');
        if (ddr) {
            dojo.dom.removeChildren(ddr);
            ddr.innerHTML = htmlfrag;
            xg.shared.util.parseWidgets(ddr);
        }
    },
    post: function() {
        this.posting = true;
        dojo.io.bind({
            url: this._url,
            method: 'post',
            mimetype: 'text/json',
            encoding: 'utf-8',
            preventCache: true,
            load: dojo.lang.hitch(this, function(type, data, event){
                this.posting = false;
                if (data && ('status' in data) && data.status == 1) {
                    if (! this._isProfilePage) {
                        // this widget is used on the friend list page as well as on profile pages
                        // the following actions are performed only when used on the friend list page
                        dojo.io.bind({
                            url: this._updateurl,
                            method: 'post',
                            mimetype: 'text/json',
                            encoding: 'utf-8',
                            content: {},
                            preventCache: true,
                            load: dojo.lang.hitch(this, function(type, data, event) {
                                if (data && ('listHtml' in data)) {
                                    this._newListHtmlLoaded = true;
                                    this._newListHtml = data.listHtml;
                                    this.updateListHtml();
                                } else {
                                    // something bad happened.. just refresh the page
                                    window.location.href = window.location.href;
                                }
                            })
                        });
                        dojo.lfx.html.fadeOut(this.itemnode, 500, dojo.lfx.easeIn, dojo.lang.hitch(this, function() {
                            dojo.dom.removeNode(this.itemnode);
                            this._fadeDone = true;
                            this.updateListHtml();
                        })).play();
                    } else {
                        // the following actions are performed only when used on the profile page
                        dojo.dom.removeNode(this.itemnode);
                        this.fixFriendStatus(data.htmlfrag);
                    }
                } else {
                    // error returned
                    if (this._isProfilePage) {
                        a.className = "desc xg_lightfont removefriend";
                    } else {
                        a.className = "smalldelete";
                    }
                }
            })
        });
    },
    confirmDialog: function(a) {
        xg.shared.util.confirm({
            title: xg.profiles.nls.html('removeFriendTitle', this._username),
            bodyHtml: '<p>' + dojo.string.escape('html', xg.profiles.nls.html('removeFriendConfirm', this._username)) + '</p>',
            onOk: dojo.lang.hitch(this, function(event) {
                if (this._isProfilePage) {
                    a.className = "desc working xg_lightfont disabled";
                } else {
                    a.className = "working smalldelete";
                }
                if (this.posting) { return; }
                this.post(a);
            }),
            okButtonText: this._confirmOkButtonText
        });
    }
});
