// Insert Flash player via JavaScript, to bypass Flash "click to activate" message in IE [Jon Aquino 2007-05-07]

// Auto-play after page load, to avoid stutter (VID-445)  [Jon Aquino 2006-09-08]
xg.addOnRequire(function() {
    if (dojo.byId('playerHtml') && ! window.location.href.match(/test_loading_message/)) {
        var playerHtml = dojo.byId('playerHtml').value;
        // Discard the <object> tags from the <embed>; otherwise setting innerHTML
        // will fail in IE  [Jon Aquino 2006-09-16]
        if (playerHtml.match(/(<embed[^>]+>)/)) { playerHtml = RegExp.$1; }
        dojo.byId('playerHtml').parentNode.innerHTML = playerHtml;
    }
});
