dojo.provide('xg.notes.NoteEditor');

dojo.require('xg.shared.AddImageDialog');
dojo.require('xg.shared.util');

dojo.widget.defineWidget('xg.notes.NoteEditor', dojo.widget.HtmlWidget, {
    _saveUrl: '',				// URL for SAVE button
    _cancelUrl: '',				// URL for CANCEL button

    currentTab: -1,				// Current active tab
    tabs: [],					// Tab DOM ids.
    contentTmp: '',				// Freezed version of the text when save is clicked
    button: -1,					// pressed "Save" button
    form: undefined,			// Form

    fillInTemplate: function(args, frag) {
        this.form = this.getFragNodeRef(frag);
        this.tabs = [
            [dojo.byId('noteEditor'),dojo.byId('noteEditorTab')],
            [dojo.byId('noteSource'),dojo.byId('noteSourceTab')],
            [dojo.byId('notePreview'),dojo.byId('notePreviewTab')]
        ];

        // implement our part of the interface
        window.notes.widgetInsertImage = dojo.lang.hitch(this,this.onInsertImage);
        window.notes.widgetInsertLink = dojo.lang.hitch(this,this.onInsertLink);
        window.notes.widgetInsertNoteLink = dojo.lang.hitch(this,this.onInsertNoteLink);
        window.notes.widgetRun = dojo.lang.hitch(this,function() {
            dojo.event.connect(this.form.save1, 'onclick', dojo.lang.hitch(this, function(){this.actionSave(1)}));
            dojo.event.connect(this.form.cancel1, 'onclick', dojo.lang.hitch(this, this.actionCancel));
            dojo.event.connect(this.form.save2, 'onclick', dojo.lang.hitch(this, function(){this.actionSave(2)}));
            dojo.event.connect(this.form.cancel2, 'onclick', dojo.lang.hitch(this, this.actionCancel));
            dojo.event.connect(window, 'onbeforeunload', dojo.lang.hitch(this, this.onUnload));

            dojo.html.hide('noteStub');
            dojo.html.show(this.form.save1);
            dojo.html.show(this.form.cancel1);
            dojo.html.show(this.form.save2);
            dojo.html.show(this.form.cancel2);
            dojo.html.show('noteEditorToolbarWrapper');

			if (window.notes.activeTab == 1) {
				this.form.sourceText.value = window.notes.savedContent;
			}
			this.activateTab(window.notes.activeTab);
            dojo.html.show('noteTabs');
        });
        // notify that we're ready
        window.notes.componentIsReady(1);
    },

    activateTab: function(tab) {
        this.syncContent();
        var tl = dojo.byId('noteEditorToolbarLock');
        var _this = this;
        if (tab != 0) { // disable toolbar
            tl.style.width = tl.parentNode.offsetWidth + 'px';
            tl.style.height = tl.parentNode.offsetHeight + 'px';
            dojo.html.show(tl);
            window.notes.editorDisableToolbar();
        } else { // enable toolbar
            dojo.html.hide(tl);
            window.notes.editorEnableToolbar();
        }
        if (tab == 2) { // hide quick help
            dojo.html.hide('noteSaveMessage');
        } else { // show quick help
            dojo.html.show('noteSaveMessage');
        }
        for(var i = 0;i<this.tabs.length;i++) {
            if (tab == i) {
                dojo.html.show(this.tabs[i][0]);
                this.tabs[i][1].innerHTML = '<span class="xg_tabs">'+this.tabs[i][1].firstChild.innerHTML+'</span>';
                dojo.html.addClass(this.tabs[i][1],'this');
            } else {
                dojo.html.hide(this.tabs[i][0]);
                this.tabs[i][1].innerHTML = '<a href="javascript:void(0)" idx="'+i+'">'+this.tabs[i][1].firstChild.innerHTML+'</a>';
                this.tabs[i][1].firstChild.onclick = function(){ _this.activateTab(this.getAttribute('idx')); return false; };
                dojo.html.removeClass(this.tabs[i][1],'this');
            }
        }
        this.currentTab = tab;
    },
    actionCancel: function() {
        try {
            window.location = this._cancelUrl;
        } catch(e) {
            // in IE 'window.location=' + onbeforeunload == unspecified error.
        }
    },
    actionSave: function(button) {
        this.syncContent();
        if (this.form.sourceText.value.length < window.notes.maxLength) {
            this.button = button;
            this.contentTmp = this.form.sourceText.value;
            this.doSave(window.notes.currentVersion, this.contentTmp);
            return;
        }
        var _this = this;
        var dlg = dojo.byId('noteSaveError');
        var form = dlg.getElementsByTagName('form')[0];
        this.setContent(dojo.html.getElementsByClass('label1',dlg)[0], notesStrings['NOTE_TOO_LONG']);
        var b = dojo.html.getElementsByClass('buttongroup',dlg);
        for(var i = 0;i<b.length;i++) {
            (i == 2 ? dojo.html.show : dojo.html.hide)(b[i]);
        }
        form.ok.onclick = function(){
            _this.dialogHide(dlg)
        };
        this.dialogShow(dlg);
    },
    quickNormalizeHtml: function(tag) {
        return tag.replace(/\s+/g,'').replace(/;/g,'').replace(/\/>/g,'>').toLowerCase();
    },
    onUnload: function(event) {
        this.syncContent();
        /*
         *	Different browsers do different things with HTML :)
         *	IE - stores tags in uppercase
         *	FF - adds ";" at the end of style=""
         *	FF - adds spaces between styles in style=""
         *	To avoid unnecessary "document has been changed" warnings we do some quick html cleanup.
         */
        var src = window.notes.savedContent.replace(/(<\/?\w+[^>]*>)/g,this.quickNormalizeHtml);
        var dst = this.form.sourceText.value.replace(/(<\/?\w+[^>]*>)/g,this.quickNormalizeHtml);
        if (src != dst) {
            dojo.event.browser.stopEvent(event);
            event.returnValue = window.notesStrings['NOTE_HAS_BEEN_CHANGED'];
        }
    },
    doSave: function(version, content) {
        var title = dojo.byId('noteTitle') ? dojo.byId('noteTitle').value : '';
        this.disableSaveButton();
        dojo.html.show('noteSpinner' + this.button);
        dojo.io.bind({
            url: this._saveUrl,
            method: 'post',
            mimetype: 'text/javascript',
            preventCache: true,
            encoding: 'utf-8',
            content: {xn_out:'json', version: version, content: content, title: title},
            error: dojo.lang.hitch(this, this.onSaveFail),
            load: dojo.lang.hitch(this, this.onSaveSuccess)
        });
    },

//** Handlers
    onSaveFail: function(err) {
        alert("Cannot save the note. Internal error.");
        this.enableSaveButton();
        dojo.html.hide('noteSpinner1');
        dojo.html.hide('noteSpinner2');
    },
    onSaveSuccess: function(type, js) {
        dojo.html.hide('noteSpinner1');
        dojo.html.hide('noteSpinner2');
        this.enableSaveButton();

        var dlg = dojo.byId('noteSaveError');
        var form = dlg.getElementsByTagName('form')[0];
        var _this = this;

        switch(js.status) {
            case 'ok': // saved
                window.notes.savedContent = this.contentTmp;
                window.notes.currentVersion = js.version;
                this.setContent(dojo.byId('noteSaveMessage'), js.message);
                this.actionCancel();
                break;
            case 'updated': // note was updated
                this.setContent(dojo.html.getElementsByClass('label1',dlg)[0], js.message);
                var b = dojo.html.getElementsByClass('buttongroup',dlg);
                for(var i = 0;i<b.length;i++) {
                    (i == 0 ? dojo.html.show : dojo.html.hide)(b[i]);
                }
                form.overwrite.onclick = function(){
                    _this.doSave(js.version, _this.contentTmp);
                    _this.dialogHide(dlg);
                };
                form.discard.onclick = function(){
                    window.notes.savedContent = js.content;
                    window.notes.editorSetText(_this.tabs[2][0].innerHTML = _this.form.sourceText.value = js.content);
                    _this.dialogHide(dlg);
                    _this.actionCancel();
                };
                form.cancel.onclick = function(){
                    window.notes.savedContent = js.content; // trigger warning upon note unload
                    _this.dialogHide(dlg);
                };
                this.dialogShow(dlg);
                break;
            case 'deleted': // note was deleted
                this.setContent(dojo.html.getElementsByClass('label1',dlg)[0], js.message);
                var b = dojo.html.getElementsByClass('buttongroup',dlg);
                for(var i = 0;i<b.length;i++) {
                    (i == 1 ? dojo.html.show : dojo.html.hide)(b[i]);
                }
                form.recreate.onclick = function(){
                    _this.doSave(0, _this.contentTmp);
                    _this.dialogHide(dlg);
                };
                form.discard2.onclick = function(){
                    _this.syncContent();
                    window.notes.savedContent = _this.form.sourceText.value; // prevent warning upon note unload.
                    _this.dialogHide(dlg);
                    _this.actionCancel();
                };
                form.cancel2.onclick = function(){
                    window.notes.savedContent = ''; // trigger warning upon note unload
                    _this.dialogHide(dlg);
                };
                this.dialogShow(dlg);
                break;
            case 'fail': // cannot save right now
            default:
                this.setContent(dojo.html.getElementsByClass('label1',dlg)[0], js.message);
                var b = dojo.html.getElementsByClass('buttongroup',dlg);
                for(var i = 0;i<b.length;i++) {
                    (i == 2 ? dojo.html.show : dojo.html.hide)(b[i]);
                }
                form.ok.onclick = function(){
                    _this.dialogHide(dlg)
                };
                this.dialogShow(dlg);
                break;
        }
    },
//** Helpers
    syncContent: function() {
        if (this.currentTab == 0) {
            this.tabs[2][0].innerHTML = this.form.sourceText.value = window.notes.editorGetText();//.replace(/(<br\s*\/?>)\r?\n?/gi,'$1\n');
        } else if (this.currentTab == 1) {
            window.notes.editorSetText(this.tabs[2][0].innerHTML = this.form.sourceText.value);
        }
    },
    disableSaveButton: function() {
        this.form.save1.disabled = this.form.save2.disabled = 1;
        dojo.html.addClass(this.form.save1, 'disabled');
        dojo.html.addClass(this.form.save2, 'disabled');
    },
    enableSaveButton: function() {
        this.form.save1.disabled = this.form.save2.disabled = 0;
        dojo.html.removeClass(this.form.save1, 'disabled');
        dojo.html.removeClass(this.form.save2, 'disabled');
    },
    dialogShow: function(dlg) {
        var selects = dojo.byId('noteEditorToolbar').getElementsByTagName('select');
        for (var i = 0;i<selects.length;i++) {
            selects[i].style.visibility = 'hidden';
        }
        xg.shared.util.showOverlay();
        dojo.html.show(dlg);
    },
    dialogHide: function(dlg) {
        var selects = dojo.byId('noteEditorToolbar').getElementsByTagName('select');
        for (var i = 0;i<selects.length;i++) {
            selects[i].style.visibility = 'visible';
        }
        xg.shared.util.hideOverlay();
        dojo.html.hide(dlg);
    },
    setContent: function(el, content) {
        // workaround for IE innerHTML bugs.
        var div = document.createElement('DIV');
        div.innerHTML = content;
        el.innerHTML = '';
        el.appendChild(div);
    },
    saveSelectionIE: function() {
        // BAZ-7104: selection is lost in IE and links don't work.
        var editorIframe = this.tabs[0][0].firstChild;
        if (editorIframe.tagName == 'IFRAME' && editorIframe.contentWindow) {
            var editorDoc = editorIframe.contentWindow.document;
            if (editorDoc.selection) {
                this.ieSelection = editorDoc.selection.createRange();
            }
        }
    },
    restoreSelectionIE: function() {
        if (this.ieSelection) {
            this.ieSelection.select();
            this.ieSelection = undefined;
        }
    },
//** Editor callbacks
    onInsertImage: function() {
        var _this = this;
        this.saveSelectionIE();
        xg.shared.AddImageDialog.promptForImage(function(html) {
            _this.restoreSelectionIE();
            if (html == '') {
                return;
            }
            if (html.match(/<img\s[^>]*src=(?:"([^"]*)"|'([^']*)')/i)) {
                var url = "" + RegExp.$1 + RegExp.$2;
                if (url != '') {
                    return window.notes.editorInsertImage(url);
                }
            }
            alert("Internal error. Cannot find an image URL in the dialog response.");
        }, true);
    },
    onInsertLink: function() {
        this.saveSelectionIE();
        var dlg = dojo.byId('noteCreateLink');
        var form = dlg.getElementsByTagName('form')[0];
        var _this = this;
        form.url.value = 'http://';
        form.ok.onclick = function(){ _this.restoreSelectionIE(); window.notes.editorCreateLink(form.url.value); _this.dialogHide(dlg) };
        form.cancel.onclick = function(){ _this.restoreSelectionIE(); _this.dialogHide(dlg) };
        this.dialogShow(dlg);
        form.url.focus();
    },
    onInsertNoteLink: function() {
        this.saveSelectionIE();
        var dlg = dojo.byId('noteCreateNoteLink');
        var form = dlg.getElementsByTagName('form')[0];
        var _this = this;
        form.title.value = '';
        form.ok.onclick = function(){
            var title = form.title.value.replace(/^\s+/g,"").replace(/\s+$/,"").replace(/\s/g,"_");
            if (title == "") {
                return alert(notesStrings['YOU_ENTERED_INVALID_CHAR']);
            }
            if (title.length > window.notes.maxTitleLength) {
                return alert(notesStrings['NOTE_TITLE_TOO_LONG']);
            }
            _this.restoreSelectionIE();
            if (title.match(/[|?#\/%.]/)) {
                window.notes.editorCreateLink(window.notes.baseUrl + "index/show?noteKey="+encodeURIComponent(title));
            } else {
                window.notes.editorCreateLink(window.notes.baseUrl + encodeURIComponent(title));
            }
            _this.dialogHide(dlg);
        };
        form.cancel.onclick = function(){ _this.restoreSelectionIE(); _this.dialogHide(dlg) };
        this.dialogShow(dlg);
        form.title.focus();
    }
});
