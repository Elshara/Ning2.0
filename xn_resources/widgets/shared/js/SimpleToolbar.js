dojo.provide('xg.shared.SimpleToolbar');

dojo.require('xg.shared.UploadFileDialog');
dojo.require('xg.shared.AddImageDialog');
dojo.require('xg.shared.util');

// adapted from http://placenamehere.com/photographica/js_textareas.html [Phil McCluskey 2007-04-04]
dojo.widget.defineWidget('xg.shared.SimpleToolbar', dojo.widget.HtmlWidget, {
    _id: false,
    _supressFileUpload: false,

    fillInTemplate: function(args, frag) {
        if (this._id) {
            this.textArea = dojo.byId(this._id);
        } else {
            this.textArea = this.getFragNodeRef(frag);
        }
        var toolbarWrapper = dojo.html.createNodesFromText(dojo.string.trim(' \
        <div class="texteditor"> \
        </div>'))[0];
        var toolbar = dojo.html.createNodesFromText(dojo.string.trim(' \
        <p class="texteditor_toolbar"> \
            <a href="#" tabindex="-1" title="' + xg.shared.nls.html('bold') + '"><img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/icon/text_bold.gif') + '" alt="' + xg.shared.nls.html('bold') + '" /></a> \
            <a href="#" tabindex="-1" title="' + xg.shared.nls.html('italic') + '"><img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/icon/text_italic.gif') + '" alt="' + xg.shared.nls.html('italic') + '" /></a> \
            <a href="#" tabindex="-1" title="' + xg.shared.nls.html('underline') + '"><img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/icon/text_underline.gif') + '" alt="' + xg.shared.nls.html('underline') + '" /></a> \
            <a href="#" tabindex="-1" title="' + xg.shared.nls.html('strikethrough') + '"><img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/icon/text_strikethrough.gif') + '" alt="' + xg.shared.nls.html('strikethrough') + '" /></a> \
            <a href="#" tabindex="-1" title="' + xg.shared.nls.html('addHyperink') + '"><img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/icon/text_link.gif') + '" alt="' + xg.shared.nls.html('addHyperink') + '" /></a> \
            <a href="#" tabindex="-1" title="' +  xg.shared.nls.html('addAnImage') + '"><img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/button/image.gif') + '" alt="' + xg.shared.nls.html('addAnImage') + '" /></a> \
            <a href="#" tabindex="-1" title="' + xg.shared.nls.html('uploadAFile') + '"><img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/button/file.gif') + '" alt="' + xg.shared.nls.html('uploadAFile') + '" /></a> \
        </p>'))[0];
        var imgs = toolbar.getElementsByTagName('a');
        dojo.event.connect(imgs[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.wrapText("<b>","</b>");
        }));
        dojo.event.connect(imgs[1], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.wrapText("<i>","</i>");
        }));
        dojo.event.connect(imgs[2], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.wrapText("<u>","</u>");
        }));
        dojo.event.connect(imgs[3], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.wrapText("<s>","</s>");
        }));
        dojo.event.connect(imgs[4], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.wrapText("<a>","</a>");
        }));
        dojo.event.connect(imgs[5], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.AddImageDialog.promptForImage(dojo.lang.hitch(this, function(html) { this.wrapText(html, ''); }));
        }));
        if (this._supressFileUpload) {
            dojo.style.hide(imgs[6]);
        } else {
            dojo.event.connect(imgs[6], 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                xg.shared.UploadFileDialog.promptForFile(dojo.lang.hitch(this, function(html) { this.wrapText(html, ''); }));
            }));
        }
        dojo.html.insertBefore(toolbar,this.textArea);
    },
    wrapText: function(startTag,endTag) {
        if (startTag == '<a>') {
            var href = prompt(xg.shared.nls.html('pleaseEnterAWebsite'), "http://");
            if (href != null) {
                startTag = "<a href=\"" + href + "\">";
            } else {
                startTag = '';
                endTag = '';
            }
        }
        if (document.selection) {
            var selectedText = document.selection.createRange().text;
            this.textArea.focus();
            var sel = document.selection.createRange();
            sel.text = startTag + selectedText + endTag;
        } else if (this.textArea.selectionStart | this.textArea.selectionStart == 0) {
            if (this.textArea.selectionEnd > this.textArea.value.length) { this.textArea.selectionEnd = this.textArea.value.length; }
            // decide where to add it and then add it
            var firstPos = this.textArea.selectionStart;
            var secondPos = this.textArea.selectionEnd+startTag.length; // cause we're inserting one at a time

            this.textArea.value=this.textArea.value.slice(0,firstPos)+startTag+this.textArea.value.slice(firstPos);
            this.textArea.value=this.textArea.value.slice(0,secondPos)+endTag+this.textArea.value.slice(secondPos);

            // reset selection & focus... after the first tag and before the second
            this.textArea.selectionStart = firstPos+startTag.length;
            this.textArea.selectionEnd = secondPos;
            this.textArea.focus();
        }
    }

});