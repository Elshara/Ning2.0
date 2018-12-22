dojo.provide('xg.activity.embed.embed');
dojo.provide('xg.activity.embed.embed.RemoveActivityLink');
/* we load 'xg.shared.EditUtil' from the script */
dojo.require('xg.shared.util');
dojo.require('dojo.lfx.html');

// TODO: Eliminate RemoveActivityLink - since there are several of them, the page will
// probably load faster if they are not Dojo widgets. Plus there's no longer a need for them
// to be Dojo widgets.  [Jon Aquino 2007-09-05]
/** based on xg.shared.PostLink */
dojo.widget.defineWidget('xg.activity.embed.embed.RemoveActivityLink', dojo.widget.HtmlWidget, {

    /** The URL to post to */
    _url: '<required>',

    /** Text for the confirmation prompt; leave unset to skip the prompt. */
    _confirmQuestion: '',

    /** Title for the confirmation prompt */
    _confirmTitle: '',

    /** OK-button text for the confirmation prompt */
    _confirmOkButtonText: '',

    /** Whether the POST is in progress */
    posting: false,

    itemnode: {},

    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.itemnode = dojo.dom.getFirstAncestorByTag(a, 'div');
            if (this.posting) { return; }
            if (! this._confirmQuestion) {
                this.post();
            } else {
                xg.shared.util.confirm({
                    title: this._confirmTitle,
                    bodyHtml: '<p>' + dojo.string.escape('html', this._confirmQuestion) + '</p>',
                    onOk: dojo.lang.hitch(this, this.post),
                    okButtonText: this._confirmOkButtonText
                });
            }
        }));
    },
    /**
     * Executes the POST operation
     */
    post: function() {
		var self = this;
        this.posting = true;
        dojo.io.bind({
            'url'           : this._url,
            'method'        : 'post',
            'preventCache'  : true,
            'mimetype'      : 'text/html',
            'encoding'      : 'utf-8',
            'load'          : function (type, data, evt) {
                if(data==1){
                    dojo.lfx.html.fadeOut(self.itemnode, 500, dojo.lfx.easeIn, dojo.lang.hitch(this, function() {
                        dojo.dom.removeNode(self.itemnode);
                    })).play();
                }
            }
        });
    }

});

dojo.provide('xg.activity.embed.embed.ActivityModule');

dojo.widget.defineWidget('xg.activity.embed.embed.ActivityModule', dojo.widget.HtmlWidget, {
    _setValuesUrl: '',
    _activityNum: '',
    _numOptionsJson: '',
    _settingsUrl: '',
    _delConfirmTitle: '',
    _delConfirmQuestion: '',
    _delConfirmOk:'',
    _delItemUrl:'',
    _delIconTooltip:'',
    _delDeleteLinkText:'',
    _isProfile:'',
    _isAdmin:'',
    isContainer: true,
    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        if(this._setValuesUrl){
            dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.activity.nls.html('edit') + '</a></p>')[0], this.h2);
            dojo.event.connect(this.module.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                if ((! this.form) || (this.form.style.height == "0px")) {
                    this.showForm();
                } else {
                    this.hideForm();
                }
            }));
        }
        this.addDeleteLinks();
    },
    addDeleteLinks: function() {
        var items = dojo.html.getElementsByClass('activityitem', this.module);
        if (!ning.CurrentProfile) return false;
        var currentUserName = ning.CurrentProfile.id;
        for(var i=0; i<items.length; i++){
            var owners = items[i].getAttribute('_owners').split(',')
            var idList = items[i].getAttribute('_idList')
            var canDelete = false
            if (this._isAdmin) {
                canDelete = true
            }else {
                for(var j=0; j<owners.length; j++){
                    if(currentUserName == owners[j]) { canDelete = true; continue; }
                }
            }
            if(canDelete){
                var currentpage = document.location.href
                var delButtonHTML = ''
                delButtonHTML += '<a dojoType="RemoveActivityLink" '
                delButtonHTML += '_confirmTitle="'+this._delConfirmTitle+'" '
                delButtonHTML += '_confirmQuestion="'+this._delConfirmQuestion+'" '
                delButtonHTML += '_confirmOkButtonText="'+this._delConfirmOk+'" '
                delButtonHTML += '_url="'+this._delItemUrl+'?idList='+idList+'&cancelUrl='+encodeURIComponent(currentpage)+'&isProfile=false&xn_out=json" '
                delButtonHTML += 'href="'+this._delItemUrl+'?idList='+idList+'&cancelUrl='+encodeURIComponent(currentpage)+'&isProfile=false'+'" '
                delButtonHTML += 'rel="nofollow" '
                delButtonHTML += 'class="activity-delete" '
                delButtonHTML += '>'+this._delDeleteLinkText+'</a>'
                var removeButtonSpan = document.createElement('span')
                removeButtonSpan.innerHTML = delButtonHTML
                dojo.dom.insertAtPosition(removeButtonSpan, items[i], 'first');
                xg.shared.util.parseWidgets(removeButtonSpan);
            }
        }
    },
    showForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        var numOptionsHtml = '';
        dojo.lang.forEach(dj_eval(this._numOptionsJson), function(option) {
            numOptionsHtml += '<option value="' + dojo.string.escape('html', option.value) + '">' + dojo.string.escape('html', option.label) + '</option>';
        });
        this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        if(!this.form) {
            var extraLink = '';
            if(!this._isProfile){
                extraLink += '\
                <dd style="line-height:1.2em!important"> \
                    <a href="' + this._settingsUrl + '">'+xg.activity.nls.html('setWhatActivityGetsDisplayed')+'</a> \
                </dd>';
            }

            this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
            <form class="xg_module_options"> \
            <fieldset> \
                <dl> \
                    <dt><label for="' + this.widgetId + '_type">' + xg.activity.nls.html('show') + '</label></dt> \
                    <dd> \
                    <select id="' + this.widgetId + '_num" class="short"> \
                        ' + numOptionsHtml + ' \
                    </select> ' + xg.activity.nls.html('events') + '\
                    </dd> \
                    '+extraLink+'\
                </dl> \
                <p class="buttongroup"> \
                    <input type="submit" value="' + xg.activity.nls.html('save') + '" class="button button-primary"/> \
                    <input type="button" value="' + xg.activity.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
                </p> \
            </fieldset> \
        </form> \
                '))[0];
            dojo.dom.insertAfter(this.form, this.head);
            this.formHeight = this.form.offsetHeight;
            this.form.style.height = "0px";
            dojo.require('xg.index.util.FormHelper');
            xg.index.util.FormHelper.select(this._activityNum, dojo.byId(this.widgetId + '_num'));
            dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
                this.save(event);
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_cancelbtn'), 'onclick', dojo.lang.hitch(this, function(event) {
                this.hideForm();
            }));
        } else {
            dojo.html.removeClass(this.form, 'collapsed');
        }
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },
    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        this._activityNum = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_num')).value;
        this.hideForm();
        dojo.io.bind({
            url: this._setValuesUrl,
            method: 'post',
            content: { activityNum: this._activityNum },
            preventCache: true,
            mimetype: 'text/javascript',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event){
                dojo.lang.forEach(dojo.html.getElementsByClass('xg_module_body', this.module), function(nd) {
                    dojo.dom.removeNode(nd);
                });
                var footer = dojo.html.getElementsByClass('xg_module_foot', this.module)[0];
                if (footer) { dojo.dom.removeNode(footer); }
                var container = dojo.html.getElementsByClass('container', this.module)[0];
                if (container) { dojo.dom.removeNode(container); }
                container = dojo.html.createNodesFromText('<div class="container"></div>')[0];
                this.module.appendChild(container);
                container.innerHTML = data.moduleBodyAndFooterHtml;
                this.addDeleteLinks();
            })
        });
    }
});
