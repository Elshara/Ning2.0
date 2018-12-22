dojo.provide('xg.index.bulk');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');
dojo.require('xg.shared.SpamWarning');

dojo.provide('xg.index.BulkActionLink');
dojo.widget.defineWidget('xg.index.BulkActionLink', dojo.widget.HtmlWidget, {
    title: '<required>',
    _url: '<required>',
    _verb: xg.index.nls.text('ok'),
    _confirmMessage: xg.index.nls.text('areYouSureYouWant'),
    _progressTitle: xg.index.nls.text('processing'),
    _progressMessage: xg.index.nls.text('pleaseKeepWindowOpen'),
    _successUrl: '',
    _successTitle: xg.index.nls.text('complete'),
    _successCallback: '',
    _displaySuccesDialog: 'true',
    _successMessage: xg.index.nls.text('processIsComplete'),
    _failureTitle: xg.index.nls.text('error'),
    _failureMessage: xg.index.nls.text('processingFailed'),
    /** The text for the join prompt, or an empty string to skip the prompt */
    _joinPromptText: '',
    _ensureCheckboxClicked: false,
    _formId: '',
    _checkboxSelectMessage: '',

    ensureSelection: function() {
        if (this._ensureCheckboxClicked) {
            var isChecked = false;
            var formToCheck = dojo.byId(this._formId);
            checkboxes = [];
            var allInputs = formToCheck.getElementsByTagName('input');
            for(a=0;a<allInputs.length;a++) {
                if (allInputs[a].type == 'checkbox') {
                    checkboxes.push(allInputs[a]);
                }
            }
            if (checkboxes.length) {
                for (i=0;i<checkboxes.length;i++) {
                    if (checkboxes[i].checked) {
                        isChecked = true;
                    }
                }
            }
            if (!isChecked) {
                xg.shared.util.alert(this._checkboxSelectMessage);
            }
            return isChecked;
        } else {
            return true;
        }
    },

    fillInTemplate: function(args, frag) {
        this.a = this.getFragNodeRef(frag);
        dojo.style.show(this.a);
        this.initDialog();
        dojo.event.connect(this.a, 'onclick', dojo.lang.hitch(this, function(evt) {
            dojo.event.browser.stopEvent(evt);
            xg.shared.util.promptToJoin(this._joinPromptText, dojo.lang.hitch(this, function() {
                if (this._confirmMessage) {
                    this.confirm();
                } else {
                    this.execute();
                }
            }));
        }));
    },

    initDialog: function() {
        if (this.dialog) { return; }
        var dialog = dojo.html.createNodesFromText(dojo.string.trim('\
            <div style="display: none" class="xg_floating_module">\
                <div class="xg_floating_container xg_module">\
                    <div class="xg_module_head">\
                        <h2>'+dojo.string.escape('html', this.title)+'</h2>\
                    </div>\
                    <div class="xg_module_body">\
                    </div>\
                </div>\
            </div>'))[0];
        this.dialog = document.body.appendChild(dialog);
        this.h2 = this.dialog.getElementsByTagName('h2')[0];
        this.body = dojo.html.getElementsByClass('xg_module_body', dialog, 'div')[0];
    },

    confirm: function() {
        // TODO: Use xg.shared.util.confirm() instead  [Jon Aquino 2007-08-17]
        if (this.ensureSelection()) {
            this.body.innerHTML= '<p>' + dojo.string.escape('html', this._confirmMessage) + '</p>\
                <p class="buttongroup"> \
                    <a href="#" class="button button-primary">'+ this._verb +'</a> \
                    <a href="#" class="button">' + xg.index.nls.html('cancel') + '</a> \
                </p>';
            var links = this.body.getElementsByTagName('a');
            dojo.event.connect(links[0],'onclick',dojo.lang.hitch(this, function(evt) {
                dojo.event.browser.stopEvent(evt);
                this.execute();
            }));
            dojo.event.connect(links[1],'onclick',dojo.lang.hitch(this, function(evt) {
                dojo.event.browser.stopEvent(evt);
                this.hide();
            }));
            this.showDialog();
        }
    },

    execute: function() {
        // Display the in-progress spinner
        this.h2.innerHTML = this._progressTitle;
        this.body.innerHTML = '<img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '" alt="" class="left" style="margin-right:5px" width="20" height="20"/> \
            <p style="margin-left:25px">' + this._progressMessage + '</p>';
        this.showDialog();
        this.doBulkAction(0);
    },

    showDialog: function() {
        xg.shared.util.showOverlay();
        dojo.html.show(this.dialog);
        // dojo.html.show(this.dialog.getElementsByTagName('div')[0]); // @todo: workaround for BAZ-730
        window.scrollTo(0, 0)
    },

    doBulkAction: function(counter) {
        // Note that xg/index/membership/list.js overrides doBulkAction [Jon Aquino 2007-05-01]
        dojo.io.bind({url: this._url,
                     method: 'post',
                     encoding: 'utf-8',
                     preventCache: true,
                     content: dojo.lang.mixin({ counter: counter }, this.getPostContent(counter)),
                     mimetype: 'text/json',
                     load: dojo.lang.hitch(this, function(t,data,e) {
             if (! data) {
                 this.failure(this._failureMessage);
             } else {
                             if (! ('contentRemaining' in data)) {
                 if ('errorMessage' in data) {
                     this.failure(data.errorMessage);
                 } else {
                     this.failure(this._failureMessage);
                 }
                 }
                 else if (this.isDone(data.contentRemaining)) {
                 this.success();
                             } else {
                 this.doBulkAction(counter+1);
                             }
             }
                     })
        });
    },

    /**
     * Returns additional values to add to the POST variables.
     * Subclasses may override this method.
     *
     * @param integer counter  which iteration (starting with 0)
     * @return object  names and values of additional POST variables
     */
    getPostContent: function(counter) {
        return {};
    },

    /**
     * Returns whether the bulk action is complete.
     * Subclasses may override this method.
     *
     * @param integer contentRemaining  content objects remaining to process,
     *     or 1 by convention if the exact number cannot easily be determined
     * @return boolean  whether to end the bulk-action iterations
     */
    isDone: function(contentRemaining) {
        return contentRemaining == 0;
    },

    success: function() {
        if (this._successUrl.length) {
            window.location.replace(this._successUrl);
        }
        else {
            if (this._displaySuccesDialog == 'true') {
                this.h2.innerHTML = this._successTitle;
                this.body.innerHTML = '<p>' + this._successMessage + '</p> \
                <p class="buttongroup"> \
                    <a href="#" class="button">' + xg.index.nls.html('ok') + '</a> \
                </p>';
                var anchors = this.body.getElementsByTagName('a');
                dojo.event.connect(this.body.getElementsByTagName('a')[anchors.length -1], 'onclick', dojo.lang.hitch(this, function(evt) {
                    dojo.event.browser.stopEvent(evt);
                    this.hide();
                }));
            } else {
                this.hide();
            }
            if (this._successCallback.length) {
                eval(this._successCallback+'(this.a)');
            }
            window.scrollTo(0, 0)
        }
    },

    failure: function(errorMessage) {
        this.h2.innerHTML = this._failureTitle;
        this.body.innerHTML = '<p>' + errorMessage + '</p> \
                <p class="buttongroup"> \
                    <a href="#" class="button">' + xg.index.nls.html('ok') + '</a> \
                </p>';
        var anchors = this.body.getElementsByTagName('a');
        dojo.event.connect(this.body.getElementsByTagName('a')[anchors.length -1], 'onclick', dojo.lang.hitch(this, function(evt) {
            dojo.event.browser.stopEvent(evt);
            this.hide();
        }));
        dj_global.scrollTo(0, 0)
    },

    hide: function() {
        // dojo.html.hide(this.dialog.getElementsByTagName('div')[0]); // @todo: workaround for BAZ-730
        dojo.html.hide(this.dialog);
        xg.shared.util.hideOverlay();
    }

});

dojo.widget.defineWidget('xg.index.BroadcastMessageLink', xg.index.BulkActionLink, {
    _spamUrl: '',
    _spamMessageParts: '',
    maxMsgLength: 2000,

     confirm: function() {
        this.body.innerHTML= '<dl style="display: none"></dl>\
                <fieldset> \
                <p><label for="subject">' + xg.index.nls.html('subject') + '</label><br /><input type="text" style="width:230px" class="textfield" name="subject" id="subject"/></p> \
                <p><label for="body">' + xg.index.nls.html('body') + '</label>('+ xg.index.nls.html('htmlNotAllowed') + ')<br /><textarea rows="12" cols="20" style="width:230px" name="body" id="body"></textarea></p> \
                <p class="buttongroup"> \
                    <a href="#" class="button button-primary">' + xg.index.nls.html('send') + '</a> \
                    <a href="#" class="button">' + xg.index.nls.html('cancel') + '</a> \
                </p> \
            </fieldset>';
        var links = this.body.getElementsByTagName('a');
        var subjectInput = this.body.getElementsByTagName('input')[0];
        var bodyInput = this.body.getElementsByTagName('textarea')[0];
        var errorDl = this.body.getElementsByTagName('dl')[0];
        dojo.event.connect(links[0],'onclick',dojo.lang.hitch(this, function(evt) {
            dojo.event.browser.stopEvent(evt);
            // Validate
            var errorMessages = [ ];
            // Clear old errors
            dojo.lang.forEach(dojo.html.getElementsByClass('error', this.body), function(el) { dojo.html.removeClass(el, 'error'); }, true);
            // Check Subject
            this.messageSubject = dojo.string.trim(subjectInput.value);
            if (this.messageSubject.length == 0) {
                errorMessages.push(xg.index.nls.html('pleaseEnterASubject'));
                xg.index.util.FormHelper.showErrorMessage(subjectInput);
            } else if (this.messageSubject.length > this.maxMsgLength) {
                errorMessages.push(xg.index.nls.html('subjectIsTooLong',this.maxMsgLength));
                xg.index.util.FormHelper.showErrorMessage(subjectInput);
            }
            // Check Body
            this.messageBody = dojo.string.trim(bodyInput.value);
            if (this.messageBody.length == 0) {
                errorMessages.push(xg.index.nls.html('pleaseEnterAMessage'));
                xg.index.util.FormHelper.showErrorMessage(bodyInput);
            } else if (this.messageBody.length > this.maxMsgLength) {
                errorMessages.push(xg.index.nls.html('messageIsTooLong',this.maxMsgLength));
                xg.index.util.FormHelper.showErrorMessage(bodyInput);
            }
            if (errorMessages.length == 0) {
                dojo.html.hide(errorDl);
                this._executeProper(subjectInput, bodyInput);
            } else {
                dojo.html.setClass(errorDl,'errordesc msg clear');
                errorDl.innerHTML = '<dt>' + xg.index.nls.html('thereHasBeenAnError') + '</dt><dd><ol><li>' + errorMessages.join('</li><li>') + '</li></ol></dd>';
                dojo.html.show(errorDl);
            }
        }));
        dojo.event.connect(links[1],'onclick',dojo.lang.hitch(this, function(evt) {
            dojo.event.browser.stopEvent(evt);
            this.hide();
        }));
        xg.shared.util.setAdvisableMaxLength(bodyInput, this.maxMsgLength);
        this.showDialog();
    },

    _executeProper: function(subjectInput, bodyInput) {
        var _this = this;
        this._spamMessageParts = dojo.json.evalJson(this._spamMessageParts);
        this._spamMessageParts[xg.index.nls.text('yourSubject')] = subjectInput.value;
        this._spamMessageParts[xg.index.nls.text('yourMessage')] = bodyInput.value;
        this._spamMessageParts = dojo.json.serialize(this._spamMessageParts);
        xg.shared.SpamWarning.checkForSpam( {
            url: this._spamUrl,
            messageParts: this._spamMessageParts,
            form: _this.body,
            onContinue: function () { dojo.style.show(_this.dialog); _this.execute(); },
            onBack: function () { dojo.style.show(_this.dialog); },
            onWarning: function () { dojo.style.hide(_this.dialog); }
        } );
    },

    getPostContent: function(counter) {
        return { subject: this.messageSubject, body: this.messageBody };
    }

});

dojo.widget.defineWidget('xg.index.bulk.BulkActionLinkWithCheckbox', xg.index.BulkActionLink, {
    _checkboxUrl: '',
    _checkboxMessage: '',
    _ensureCheckboxClicked: false,
    _formId: '',
    _checkboxName: '',
    _checkboxSelectMessage: '',
    confirm: function() {
        if (this.ensureSelection()) {
            this.body.innerHTML= ''+
    '            <p>' + dojo.string.escape('html', this._confirmMessage) + '</p>'+
    '            <fieldset class="nolegend">'+
    '                <p>'+
    '                    <label><input class="checkbox" type="checkbox" id="dialog_additional_checkbox">'+this._checkboxMessage+'</label>'+
    '                </p>'+
    '                <p class="buttongroup">'+
    '                    <a href="#" class="button button-primary">'+ this._verb +'</a>'+
    '                    <a href="#" class="button">' + xg.index.nls.html('cancel') + '</a>'+
    '                </p>'+
    '            </fieldset>';
            var links = this.body.getElementsByTagName('a');
            dojo.event.connect(links[0],'onclick',dojo.lang.hitch(this, function(evt) {
                dojo.event.browser.stopEvent(evt);
                if(dojo.byId('dialog_additional_checkbox').checked){
                    this._url = this._checkboxUrl;
                }
                this.execute();
            }));
            dojo.event.connect(links[1],'onclick',dojo.lang.hitch(this, function(evt) {
                dojo.event.browser.stopEvent(evt);
                this.hide();
            }));
            this.showDialog();
        }
    }
});
