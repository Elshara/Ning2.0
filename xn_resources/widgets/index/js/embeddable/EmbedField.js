dojo.provide('xg.index.embeddable.EmbedField');

dojo.require('xg.shared.util');

/**
 * An <input> text field for <embed> code
 */
dojo.widget.defineWidget('xg.index.embeddable.EmbedField', dojo.widget.HtmlWidget, {
    fillInTemplate: function(args, frag) {
        var input = this.getFragNodeRef(frag);
        xg.shared.util.selectOnClick(input);
        var br = dojo.html.createNodesFromText('<br />')[0];
        dojo.dom.insertAfter(br, input);
        var button = dojo.html.createNodesFromText('<input type="button" class="button right" value="' + xg.index.nls.html('copyHtmlCode') + '" />')[0];
        dojo.dom.insertAfter(button, br);
        dojo.event.connect(button, 'onclick', dojo.lang.hitch(this, function(event) {
            this.copyToClipboard(input);
        }));
    },
    /**
     * Copies the element's text to the clipboard.
     *
     * @param inElement  an <input> node
     */
     copyToClipboard: function(inElement) {
        var beforeCopy = inElement.getAttribute('_beforeCopy');
        if (beforeCopy) {
            eval(beforeCopy);
        }

        // From Dion Almaer, "Auto copy to clipboard", http://ajaxian.com/archives/auto-copy-to-clipboard  [Jon Aquino 2007-05-26]
        if (inElement.createTextRange) {
            var range = inElement.createTextRange();
            if (range)
               range.execCommand('Copy');
        } else {
            var flashcopier = 'flashcopier';
            if(!document.getElementById(flashcopier)) {
                var divholder = document.createElement('div');
                divholder.id = flashcopier;
                document.body.appendChild(divholder);
            }
            document.getElementById(flashcopier).innerHTML = '';
            var divinfo = '<embed src="' + xg.shared.util.cdn('/xn_resources/widgets/index/swf/_clipboard.swf') + '" FlashVars="clipboard='+encodeURIComponent(inElement.value)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
            document.getElementById(flashcopier).innerHTML = divinfo;
        }
    }
});

