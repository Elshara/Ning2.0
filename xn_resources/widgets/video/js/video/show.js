dojo.provide('xg.video.video.show');

dojo.require('xg.shared.util');
dojo.require('xg.index.embeddable.EmbedField');

xg.video.checkConversionStatus = function() {
    window.location.href.match(/id=([^&]+)/);
    var id = RegExp.$1;
    dojo.io.bind({
        encoding:'utf-8',
        url: '/index.php/' + xg.global.currentMozzle + '/video/conversionStatus?id=' + id,
        preventCache: true,
        mimetype: 'text/javascript',
        load: function(type, data, event){
            if (data.conversionStatus == 'complete') {
                var playerHtml = data.embedHtml;
                if (playerHtml.match(/(<embed[^>]+>)/)) { playerHtml = RegExp.$1; }
                dojo.byId('convPlaceHolder').parentNode.innerHTML = playerHtml;
            } else if (data.conversionStatus == 'in progress') {
                setTimeout('xg.video.checkConversionStatus()', 30000);
            } else if (data.conversionStatus == 'failed') {
                window.location.href = '/' + xg.global.currentMozzle + '/index/error';
            }
        }
    });
}

dojo.provide('xg.video.VideoEmbedField');

dojo.widget.defineWidget('xg.video.VideoEmbedField', xg.index.embeddable.EmbedField, {
    fillInTemplate: function(args, frag) {
        //pass
    }
});
// @todo move xg.index.embeddable.EmbedField to xg.shared so we don't have
// to subclass it with a blank implementation. Or move the function we need (copyToClipboard)
// to xg.shared.util.  [Jon Aquino 2007-07-07]
