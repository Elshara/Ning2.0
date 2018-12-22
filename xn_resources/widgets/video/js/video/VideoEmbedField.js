dojo.provide('xg.video.video.VideoEmbedField');

dojo.require('xg.index.embeddable.EmbedField');
dojo.require('xg.shared.util');

dojo.widget.defineWidget('xg.video.video.VideoEmbedField', xg.index.embeddable.EmbedField, {
    fillInTemplate: function(args, frag) {
        //pass
    }
});
// @todo move xg.index.embeddable.EmbedField to xg.shared so we don't have
// to subclass it with a blank implementation. Or move the function we need (copyToClipboard)
// to xg.shared.util.  [Jon Aquino 2007-07-07]