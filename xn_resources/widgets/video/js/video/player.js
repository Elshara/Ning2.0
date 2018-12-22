// Insert Flash player via JavaScript, to bypass Flash "click to activate" message in IE [Jon Aquino 2007-05-07]

// Auto-play after page load, to avoid stutter (VID-445)  [Jon Aquino 2006-09-08]
xg.addOnRequire(function() {
    if (dojo.byId('playerHtml') && ! window.location.href.match(/test_loading_message/)) {
        var playerHtml = dojo.byId('playerHtml').value;
        // Discard the <object> tags from the <embed>; otherwise setting innerHTML
        // will fail in IE  [Jon Aquino 2006-09-16]
        if (playerHtml.match(/(<embed[^>]+>)/)) { playerHtml = RegExp.$1; }
        var maxWidth = (dojo.byId('playerHtml').parentNode.offsetWidth)
        var playerWidth = (playerHtml.match(/(width[:="]*)(\d+)/) || [])[2];
        var playerHeight = (playerHtml.match(/(height[:="]*)(\d+)/) || [])[2];
        if(dojo.byId('playerHtml').getAttribute('_thirdParty') == 'true' && (maxWidth)&&(playerWidth)&&(playerHeight)&&(playerWidth > maxWidth)){
            var percent     = maxWidth/playerWidth;
            var newHeight   = Math.round(playerHeight*percent);
            playerHtml      = playerHtml.replace(/(height[:="]*)(\d+)/, '$1' + newHeight);
            playerHtml      = playerHtml.replace(/(width[:="]*)(\d+)/, '$1' + maxWidth);
        }
        dojo.byId('playerHtml').parentNode.innerHTML = playerHtml;
    }
    if (dojo.html.getElementsByClass('in-progress').length > 0 || window.location.href.match(/test_check_conversion_status/)) {
        xg.video.checkConversionStatus();
    }
});
