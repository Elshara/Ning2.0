dojo.provide('xg.forum.topic.QuoteLink');

dojo.require('xg.index.util.FormHelper');
/**
 * A link for creating a quoted reply in a non-threaded forum.
 */
dojo.widget.defineWidget('xg.forum.topic.QuoteLink', dojo.widget.HtmlWidget, {
    /** The contributor who is being replied to. */
    _contributor: '<required>',
    /** The url of the reply being replied to. */
    _citeUrl: '<required>',
    /** The id of the div that holds the text to be quoted. */
    _descId: '<required>',

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        this.anchor = this.getFragNodeRef(frag);
        replyFormDiv = dojo.html.getElementsByClass('texteditor', document)[0];
        replyTextArea = replyFormDiv.getElementsByTagName('textarea')[0];

        dojo.event.connect(this.anchor, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            quotedText = dojo.byId(this._descId).innerHTML;
            replyText = '\n\n<cite>' + xg.forum.nls.html('contributorSaid',dojo.string.escape('html', this._contributor)) + '</cite><blockquote cite="' + dojo.string.escape('html',this._citeUrl) + '"><div>' + quotedText + '</div></blockquote>';
            replyTextArea.value = replyText;
            xg.index.util.FormHelper.scrollIntoView(replyTextArea.parentNode.parentNode);
        }));
    }
});