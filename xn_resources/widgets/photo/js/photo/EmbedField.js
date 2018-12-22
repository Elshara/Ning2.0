dojo.provide('xg.photo.photo.EmbedField');

dojo.require('xg.shared.util');

/**
 * An <input> text field for <embed> code
 */
dojo.widget.defineWidget('xg.photo.photo.EmbedField', dojo.widget.HtmlWidget, {
    fillInTemplate: function(args, frag) {
        var input = this.getFragNodeRef(frag);
        xg.shared.util.selectOnClick(input);
    }
});

