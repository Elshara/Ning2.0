dojo.provide('xg.profiles.embed.ChatterModule');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.EditUtil');
dojo.require('xg.profiles.embed.chatterwall');

/**
 * A module which displays comments on a profile page.
 */
dojo.widget.defineWidget('xg.profiles.embed.ChatterModule',dojo.widget.HtmlWidget, {

    /** Endpoint for saving the values */
    _url: '',

    /** Y or N, indicating whether the user is moderating comments on her page. */
    _moderate: '',

    /** Number of comments to display */
    _itemCount: '',

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        this.h2 = this.module.getElementsByTagName('h2')[0];
        dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.profiles.nls.html('edit') + '</a></p>')[0], this.h2);
        dojo.event.connect(this.module.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if ((! this.form) || (this.form.style.height == "0px")) {
                this.showForm();
            } else {
                this.hideForm();
            }
        }));
    },
    /**
     * Displays the Edit form.
     */
    showForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        this.body = dojo.html.getElementsByClass('xg_module_body', this.module)[0];

        var checkedN = '';
        var checkedY = '';
        if (this._moderate == 'Y') {
            checkedY = 'checked="checked"';
        } else {
            checkedN = 'checked="checked"';
        }
        if(!this.form) {
            this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
                <form class="xg_module_options"> \
                    <fieldset> \
                        <dl> \
                            <dt><label for="' + this.widgetId + '_item_count">'+xg.profiles.nls.html('show')+'</label></dt> \
                            <dd> \
                                <select id="' + this.widgetId + '_item_count"> \
                                    <option value="3">3</option> \
                                    <option value="5">5</option> \
                                    <option value="10">10</option> \
                                    <option value="20">20</option> \
                                </select> \
                                '+xg.profiles.nls.html('comments')+' \
                            </dd> \
                            <dt class="wide">'+xg.profiles.nls.html('letMeApproveChatters')+'</dt> \
                            <dd> \
                                <ul class="options"> \
                                    <li><label><input type="radio" class="radio" name="moderate" value="N" '+checkedN+' />' + xg.profiles.nls.html('noPostChattersImmediately') +'</label></li> \
                                    <li><label><input type="radio" class="radio" name="moderate" value="Y" '+checkedY+' />' + xg.profiles.nls.html('yesApproveChattersFirst') +'</label></li> \
                                </ul> \
                            </dd> \
                        </dl> \
                        <p class="buttongroup"> \
                            <input type="submit" value="' + xg.profiles.nls.html('save') + '" class="button button-primary"/> \
                            <input type="button" value="' + xg.profiles.nls.html('cancel') + '" class="button"  id="' + this.widgetId + '_cancelbtn"/> \
                        </p> \
                    </fieldset> \
                </form> \
                '))[0];
            dojo.dom.insertAfter(this.form, this.head);
            this.formHeight = this.form.offsetHeight;
            this.form.style.height = 0;
            dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
                this.save(event);
            }));
            dojo.event.connect(dojo.byId(this.widgetId + '_cancelbtn'), 'onclick', dojo.lang.hitch(this, function(event) {
                this.hideForm();
            }));
        }
        xg.index.util.FormHelper.select(this._itemCount, dojo.byId(this.widgetId + '_item_count'));
        xg.shared.EditUtil.showModuleForm(this.form, this.formHeight, editbutton);
    },
    /**
     * Hides the Edit form.
     */
    hideForm: function() {
        var editbutton = this.module.getElementsByTagName('a')[0];
        xg.shared.EditUtil.hideModuleForm(this.form, this.formHeight, editbutton);
    },
    /**
     * Submits the values from the Edit form.
     *
     * @param event  the onsubmit event object
     */
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        this._moderate = xg.index.util.FormHelper.radioValue(this.form.moderate);
        this._itemCount = xg.index.util.FormHelper.selectedOption(dojo.byId(this.widgetId + '_item_count')).value;
        dojo.io.bind({
            url: this._url,
            method: 'post',
            content: { attachedToType: 'User', moderate: this._moderate, itemCount: this._itemCount },
            preventCache: true,
            mimetype: 'text/json',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event){
                dojo.lang.forEach(dojo.html.getElementsByClass('xj_ajax', this.domNode), function(node) { dojo.dom.removeNode(node); });
                var div = document.createElement('div');
                div.className = 'xj_ajax';
                div.innerHTML = data.moduleBodyAndFooterHtml;
                this.domNode.appendChild(div);
                xg.profiles.embed.chatterwall.bindToClassLinks('chatter-approve', xg.profiles.embed.chatterwall.approve, div);
                xg.profiles.embed.chatterwall.bindToClassLinks('chatter-remove', xg.profiles.embed.chatterwall.remove, div);
                this.hideForm();
            })
        });
    }
});
