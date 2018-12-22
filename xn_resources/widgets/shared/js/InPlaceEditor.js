dojo.provide('xg.shared.InPlaceEditor');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

dojo.widget.defineWidget('xg.shared.InPlaceEditor', dojo.widget.HtmlWidget, {
    // Use '<required>' as there is a Dojo bug preventing the attributes from being recognized if the defaults are instead null  [Jon Aquino 2006-07-15]
    _instruction: '<required>',
    _maxLength: '<required>',
    _setValueUrl: '<required>',
    _getValueUrl: '',
    _value: '',
    _control: 'textArea',
    _controlAttributes: '',
    _endRegexToIgnore: '',
    // If _html is true, be sure to set _getValueUrl or _value, as html auto-discovery works poorly in IE [Jon Aquino 2007-03-31]
    _html: false,
    /** The text for the join prompt, or an empty string to skip the prompt */
    _joinPromptText: '',
    _toolbar: false,
    disabled: false,
    initialized: false,
    // To do when needed: startRegexToIgnore  [Jon Aquino 2006-07-16]
    showForm: function() {
        dojo.html.hide(this.displayNode);
        dojo.html.removeClass(this.displayNode, 'editable_hover');
        dojo.html.show(this.form);
        // Ensure that text value is set *after* showing the form;
        // otherwise it will be empty in Safari (VID-323, BAZ-2856)  [Jon Aquino 2007-07-23]
        setTimeout(dojo.lang.hitch(this, function() {
            // We inserted the span to work around an IE6 issue - see setDisplayHtml() [Jon Aquino 2007-02-13]
            this.textControl.value = this._value.replace(this.end, '').replace(/<span><\/span>/gi, '');
            // Use stripTags to normalize the comparison in IE  [Jon Aquino 2006-08-12]
            if (this.stripTags(this.textControl.value) == this.stripTags(this.instruction())) { this.textControl.value = ''; }
            if (! this._html) { this.textControl.value = this.stripTags(this.textControl.value); }
            // \r in IE [Jon Aquino 2007-03-31]
            else { this.textControl.value = this.textControl.value.replace(/<br ?.?>\r?\n/gi, "\n") }
        }), 0);
        this.textControl.focus();
        xg.index.util.FormHelper.scrollIntoView(this.form);
        if(this._toolbar){
            if (dojo.html.getElementsByClass('texteditor_toolbar',this.textControl.parentNode).length < 1){
                if(!this.textControl.id) this.textControl.id = new Date().getTime()
                var toolbar = dojo.widget.createWidget("SimpleToolbar", {_id: this.textControl.id});
            }
        }
    },
    hideForm: function() {
        dojo.html.hide(this.form);
        dojo.html.show(this.displayNode);
    },
    stripTags: function(x) {
        return dojo.html.renderedTextContent(dojo.html.createNodesFromText('<div>' + x + '</div>')[0])
    },
    fillInTemplate: function(args, frag) {
        this.displayNode = this.getFragNodeRef(frag);
        this.displayNode.title = xg.shared.nls.text('clickToEdit');
        this._value = dojo.string.trim(this._value ? this._value : this.displayNode.innerHTML);
        // Ignore case, as IE uppercases tags  [Jon Aquino 2006-07-15]
        var endMatch = new RegExp(this._endRegexToIgnore, 'i').exec(this._value);
        this.end = endMatch ? endMatch[0] : '';
        this.setDisplayHtml(this._value, this._value.length > 0); // BAZ-6133 [Jon Aquino 2008-02-20]
        dojo.event.connect(this.displayNode, 'onmouseover', dojo.lang.hitch(this, function() {
            dojo.html.addClass(this.displayNode, 'editable_hover');
        }));
        dojo.event.connect(this.displayNode, 'onmouseout', dojo.lang.hitch(this, function() {
            dojo.html.removeClass(this.displayNode, 'editable_hover');
        }));
        dojo.event.connect(this.displayNode, 'onclick', dojo.lang.hitch(this, function() {
            xg.shared.util.promptToJoin(this._joinPromptText, dojo.lang.hitch(this, function() {
                this.initializeIfNecessary();
                if (this.disabled) { return; }
                if (this._getValueUrl == '') {
                    this.showForm();
                } else {
                    this.disabled = true;
                    this.displayNode.innerHTML = '<span class="instruction">' + xg.shared.nls.html('loading') + '</span>';
                    dojo.io.bind({
                        url         : this._getValueUrl,
                        preventCache: true,
                        encoding    : 'utf-8',
                        mimetype    : 'text/javascript',
                        load        : dojo.lang.hitch(this, function(type, data, event){
                            this.setDisplayHtml(data.html);
                            this.showForm();
                            this.disabled = false;
                        })
                    });
                }
            }));
        }));
    },
    initializeIfNecessary: function() {
        if (this.initialized) { return; }
        this.initialized = true;
        this.form = dojo.html.createNodesFromText(dojo.string.trim(' \
                <form class="inplace_edit" style="display:none;"> \
                    <div class="texteditor"> \
                    <textarea ' + this._controlAttributes + '></textarea> \
                    </div> \
                    <p class="buttongroup"> \
                        <input type="submit" class="button submit" value="' + xg.shared.nls.html('save') + '" /> \
                      <input type="button" class="button" value="' + xg.shared.nls.html('cancel') + '" /> \
                    </p> \
                </form>'))[0];
        dojo.dom.insertAfter(this.form, this.displayNode);
        this.textControl = this.form.getElementsByTagName('textarea')[0];
        this.saveButton = this.form.getElementsByTagName('input')[0];
        this.cancelButton = this.form.getElementsByTagName('input')[1];
        if (this._control == 'textInput') {
            var oldTextControl = this.textControl;
            this.textControl = dojo.html.createNodesFromText('<input type="text" ' + this._controlAttributes + ' maxLength="' + this._maxLength + '" />')[0];
            oldTextControl.parentNode.replaceChild(this.textControl, oldTextControl);
        }
        dojo.event.connect(this.cancelButton, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.setDisplayHtml(this._value);
            this.hideForm();
        }));
        dojo.event.connect(this.form, 'onsubmit', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (this.disabled) { return; }
            this.disabled = true;
            this.displayNode.innerHTML = '<span class="instruction">' + xg.shared.nls.html('saving') + '</a>';
            this.hideForm();
            this.textControl.value = dojo.string.trim(this.textControl.value).substr(0, this._maxLength);
            var params = xg.shared.util.parseUrlParameters(this._setValueUrl);
            params['value'] = this.textControl.value;
            dojo.io.bind({
                url: this._setValueUrl,
                content: params,
                method: "post",
                encoding: 'utf-8',
                preventCache: true,
                mimetype: 'text/javascript',
                encoding: 'utf-8',
                load: dojo.lang.hitch(this, function(type, data, event){
                    this.setDisplayHtml(data.html);
                    this.disabled = false;
                    this._getValueUrl = '';
                })
            });
        }));
    },
    /**
     * @param doNotSetInnerHtml  whether to skip updating the innerHTML of the element;
     *     speed optimization used during initialization
     */
    setDisplayHtml: function(displayHtml, doNotSetInnerHtml) {
        this._value = displayHtml;
        displayHtml = dojo.string.trim(displayHtml.replace(this.end, ''));
        if (displayHtml.length == 0) {
            displayHtml = this.instruction();
        }
        if (! doNotSetInnerHtml) {
            // Insert span to work around IE6 issue: it seems to ignore innerHTML if it begins with an <object> tag [Jon Aquino 2007-02-13]
            this.displayNode.innerHTML = '<span></span>' + xg.shared.util.nl2br(displayHtml) + this.end;
        }
        // If the person clicks a link, don't show the in-place editor  [Jon Aquino 2006-08-05]
        dojo.lang.forEach(this.displayNode.getElementsByTagName('a'), dojo.lang.hitch(this, function(a) {
            // Use a.onclick instead of dojo.event.connect; otherwise IE throws
            // "TypeError: '__clobberAttrs__' is null or not an object" for some reason  [Jon Aquino 2006-08-05]
            a.onclick = dojo.lang.hitch(this, function() {
                this.disabled = true;
                window.setTimeout(dojo.lang.hitch(this, function() {
                    this.disabled = false;
                }), 1000);
            });
        }));
    },
    instruction: function() {
        if (dojo.string.trim(this._instruction).length > 0) {
            return '<span class="instruction">[' + dojo.string.escape('html', this._instruction) + '] </span>';
        } else {
            return '<span class="instruction"></span>';
        }
    }
});
