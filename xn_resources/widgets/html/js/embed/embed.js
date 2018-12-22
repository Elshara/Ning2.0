dojo.provide('xg.html.embed.embed');
dojo.require('xg.shared.util');
dojo.require('xg.shared.SimpleToolbar');

// Possible Dojo bug: the widget doesn't get defined if "html"
// is in the package name. So use "_html". [Jon Aquino 2006-11-18]

dojo.provide('xg._html.embed.embed.HtmlModule');
dojo.widget.defineWidget('xg._html.embed.embed.HtmlModule', dojo.widget.HtmlWidget, {
    // Use '<required>' as there is a Dojo bug preventing the attributes from being recognized if the defaults are instead null [Jon Aquino 2006-11-18]
    url: '<required>',
    updateUrl: '<required>',
    /** The title string. */
    _title: '',
    /** The maximum string length allowed for the html. */
    _maxLength: 0,
    hasDefaultContent: false,
    /** URL for the "Add a widget to this textbox" link, or null to hide the link */
    addWidgetUrl: '',
    /** The spinner image */
    spinner: null,
    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        //prevent double parsing
        var head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        if (head.getElementsByTagName('a').length == 0) {
            this.addEditLink();
            this.html = dojo.string.trim(dojo.html.getElementsByClass('html_code',this.module)[0].value);
        }
        // Technically the widget gets parsed twice. TODO: Try eliminating the second
        // parse by removing the dojoType attribute (wonder why Dojo allows an element
        // to be parsed more than once?) [Jon Aquino 2007-03-24]
    },
    addEditLink: function() {
        this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        var h2 = this.head.getElementsByTagName('h2')[0];
        var p = this.head.getElementsByTagName('p');
        if (p.length < 1) {
            dojo.dom.insertAfter(dojo.html.createNodesFromText('<p class="edit"><a class="button" href="#">' + xg.html.nls.html('edit') + '</a></p>')[0], h2);
            dojo.event.connect(this.head.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                this.showForm();
            }));
            this.foot = dojo.html.getElementsByClass('xg_module_foot', this.module)[0];
            if (this.foot) {
                dojo.event.connect(this.foot.getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
                    dojo.event.browser.stopEvent(event);
                    this.showForm();
                }));
            }
        }
    },
    showForm: function() {
        var curDate = new Date();
        this.taID = 'txt' + curDate.getTime();
        dojo.html.addClass(this.module, 'editing_html_module');
        this.head = dojo.html.getElementsByClass('xg_module_head', this.module)[0];
        this.body = dojo.html.getElementsByClass('xg_module_body', this.module)[0];
        this.foot = dojo.html.getElementsByClass('xg_module_foot', this.module)[0];
        if (this.foot) {
            dojo.style.show(this.body);
            dojo.style.hide(this.foot);
        }
        dojo.html.removeClass(this.body, "notification");

        // not draggable while in edit mode (if frink is enabled)
        if (dojo.html.hasClass(this.module, 'sortable')) {
            dojo.html.removeClass(this.head, 'draggable');
            dojo.html.removeClass(this.head.getElementsByTagName('h2')[0], 'draggable');
        }

        // save the original module title and body in case they cancel the edit
        this.originalBody = this.body.innerHTML;
        if (this.head.getElementsByTagName('h2')[0]) {
            // this is null when the module gets parsed the second time, so wrap in an if
            this.originalTitle = this.head.getElementsByTagName('h2')[0].innerHTML;
        }
        this.head.innerHTML = '<form><input type="text" class="textfield"/></form>';
        this.body.innerHTML = ' \
                <p class="errordesc" style="display: none">' + xg.html.nls.html('contentsTooLong', this._maxLength) + '</p> \
                <p><div class="texteditor"><textarea id="' + this.taID + '"></textarea></div></p> \
                ' + (this.addWidgetUrl && ! this.hasDefaultContent ? '<span class="left">' + xg.html.nls.html('addAWidget', dojo.string.escape('html', this.addWidgetUrl)) + '</span>' : '') + ' \
                <p class="buttongroup"> \
                    <img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '" alt="" style="display: none; width:20px; height:20px; margin-right:3px; vertical-align:top;" /> \
                    <input type="button" class="button button-primary" value="' + xg.html.nls.html('save') + '" /> \
                    <input type="button" class="button" value="' + xg.html.nls.html('cancel') + '" /> \
                </p>';

        this.input = this.head.getElementsByTagName('input')[0];
        this.textarea = this.body.getElementsByTagName('textarea')[0];
        var saveButton = dojo.html.getElementsByClass('button', this.body)[0];
        var cancelButton = dojo.html.getElementsByClass('button', this.body)[1];
        this.spinner = dojo.dom.prevElement(cancelButton, 'img');
        this.input.value = this._title;
        var html = this.html.replace(/(\r\n|\n|\r)/g,"\n");
        this.textarea.value = this.hasDefaultContent ? '' : html.replace(/<br[^>]*>\n?/ig, "\n").replace(/<\/p>\n?/ig, "</p>\n");
        dojo.require('xg.index.util.FormHelper');
        xg.index.util.FormHelper.scrollIntoView(this.module);
        var toolbar = dojo.widget.createWidget("SimpleToolbar", {_id: this.taID});
        dojo.event.connect(saveButton, 'onclick', dojo.lang.hitch(this, this.save));
        dojo.event.connect(cancelButton, 'onclick', dojo.lang.hitch(this, this.cancel));
        dojo.event.connect(this.head.getElementsByTagName('form')[0], 'onsubmit', dojo.lang.hitch(this, this.save));
        this.input.focus();
    },
    hideForm: function() {
        dojo.html.removeClass(this.module, 'editing_html_module');
        this.head.innerHTML = '<h2></h2>';
        var h2 = this.head.getElementsByTagName('h2')[0];
        h2.innerHTML = this._title ? dojo.string.escape('html', this._title) : '&nbsp;';  // &nbsp; to accomodate Edit-button height. [Jon Aquino 2008-01-14]

        // make draggable again (if frink is enabled)
        if (dojo.html.hasClass(this.module, 'sortable')) {
            dojo.html.addClass(this.head, 'draggable');
            dojo.html.addClass(h2, 'draggable');
        }

        this.body.innerHTML = xg.html.nls.html('saving');
    },

    /**
     * Call-back function to update the module body
     *
     * @param ui    jQuery.ui Object      The ui object which makes the callback
     */
    updateEmbed: function(ui) {
        var maxEmbedWidth = this.module.parentNode.getAttribute('_maxembedwidth')
        dojo.io.bind({
            url: this.updateUrl,
            method: 'post',
            content: { maxEmbedWidth: maxEmbedWidth },
            preventCache: true,
            mimetype: 'text/json',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event) {
                //refactor this with save/cancel
                this.hasDefaultContent = data.hasDefaultContent;
                if (!('body' in this)) {
                    this.body = dojo.html.getElementsByClass('xg_module_body', this.module)[0];
                }
                this.body.innerHTML = data.displayHtml;
                this.html = data.sourceHtml;
                this.addEditLink();
                ui.item.css('visibility', '');

                // fix hover drag icon
                var handleDiv = this.module.getElementsByTagName('div')[0];
                if (dojo.html.hasClass(handleDiv, 'xg_handle')) dojo.style.hide(handleDiv);
            })
        });
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        var errorMessage = dojo.html.getElementsByClass('errordesc', this.body)[0];
        if (this.textarea.value.length > this._maxLength && this._maxLength > 0) {
            dojo.style.show(errorMessage);
            xg.index.util.FormHelper.scrollIntoView(errorMessage);
            return;
        }
        dojo.style.hide(errorMessage);
        this._title = this.input.value;
        this.html = xg.shared.util.nl2br(this.textarea.value);
        var maxEmbedWidth = this.module.parentNode.getAttribute('_maxembedwidth');
        if (dojo.style.isShowing(this.spinner)) { return; }
        dojo.style.show(this.spinner);
        dojo.io.bind({
            url: this.url,
            method: 'post',
            content: { title: this._title, html: this.html, maxEmbedWidth: maxEmbedWidth },
            preventCache: true,
            mimetype: 'text/javascript',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event) {
                dojo.style.hide(this.spinner);
                if (data.errorCode == 'TOO_LONG') {
                    dojo.style.show(errorMessage);
                    xg.index.util.FormHelper.scrollIntoView(errorMessage);
                    return;
                }
                this.hasDefaultContent = data.hasDefaultContent;
                if (data.displayHtml.match(/<script/i)) {
                    window.location.reload(true);
                }
                this.hideForm();

                // update title
                if (('moduleHead' in data) && (data.moduleHead.length > 0)) {
                    // create dom node
                    var newHead = dojo.html.createNodesFromText(data.moduleHead)[0];
                    var newh2 = newHead.getElementsByTagName('h2')[0];
                    var h2 = this.head.getElementsByTagName('h2')[0];
                    h2.innerHTML = newh2.innerHTML;
                }

                // update body and source html
                if (data.displayFoot) {
                    this.foot.innerHTML = data.displayFoot;
                    dojo.style.show(this.foot);
                    dojo.style.hide(this.body);
                } else {
                    this.body.innerHTML = data.displayHtml;
                    this.html = data.sourceHtml;
                }

                // add back edit link if needed
                this.addEditLink();
            })
        });
    },
    cancel: function(event) {
        dojo.event.browser.stopEvent(event);
        if (dojo.style.isShowing(this.spinner)) { return; }
        this.hideForm();
        this.body.innerHTML = this.originalBody;
        var h2 = this.head.getElementsByTagName('h2')[0];
        h2.innerHTML = this.originalTitle;
        this.addEditLink();
        if (this.foot && this.hasDefaultContent) {
            dojo.style.hide(this.body);
            dojo.style.show(this.foot);
        }
    }
});
