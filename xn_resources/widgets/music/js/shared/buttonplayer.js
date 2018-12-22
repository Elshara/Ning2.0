dojo.provide('xg.music.shared.buttonplayer');

dojo.require('xg.shared.util');

(function() {
    var playlist = dojo.byId('playlist');
    var playButtons = dojo.html.getElementsByClassName('play-button');
    function replaceWithFlashButtonAndPlay(clickedElement) {
        //stop any playing button by replacing it with the image again.
        for(var i=0; i<playButtons.length; i++) {
            if(playButtons[i].firstChild.nodeName == 'OBJECT') {
                var imgNode = document.createElement('img');
                imgNode.setAttribute('alt',xg.music.nls.html('play') )
                imgNode.setAttribute('src', xg.shared.util.cdn('/xn_resources/widgets/music/gfx/miniplayer.gif'))
                imgNode.setAttribute('width','21')
                imgNode.setAttribute('height','16')
                dojo.dom.insertAfter(imgNode,playButtons[i].firstChild)
                dojo.dom.removeNode(playButtons[i].firstChild)
            }
        }

        if(clickedElement.nodeName == 'A') {
            var imgElement = clickedElement.firstChild;
            var aElement = clickedElement;
        } else if(clickedElement.nodeName == 'IMG') {
            var imgElement = clickedElement;
            var aElement = clickedElement.parentNode;
        } else {
            return false;
        }
        var trackUrl = (aElement.getAttribute('_href'))? aElement.getAttribute('_href'):aElement.getAttribute('href');
        var movieUrl = xg.shared.util.cdn('/xn_resources/widgets/music/swf/buttonplayer.swf?autoplay=true&song_url=' + encodeURIComponent(trackUrl) + '');
        // IE throws error on document.createElement('object').innerHTML = ... [Jon Aquino 2007-05-29]
        var innerHTML = dojo.string.trim('\
                <object wmode="transparent" type="application/x-shockwave-flash" width="21" height="16" data="' + dojo.string.escape('html', movieUrl) + '"> \
                    <param name="wmode" value="transparent" /> \
                    <param name="movie" value="' + dojo.string.escape('html', movieUrl) + '" /> \
                </object>');
        // window.setInnerHtmlFromExternalScript will not be defined if (1) the browser is not IE (2) setInnerHtmlFromExternalScript.js fails to load for whatever reason [Jon Aquino 2007-05-31]
        if (window.setInnerHtmlFromExternalScript) { window.setInnerHtmlFromExternalScript(aElement, innerHTML); }
        else { aElement.innerHTML = innerHTML; }
        //prevent drag start if the user is just pressing stop
        dojo.event.connect(aElement.getElementsByTagName('object')[0], 'onmousedown', function(evt) { dojo.event.browser.stopEvent(evt) } );
        return true;
    }
    xg.addOnRequire(function() {
        for(var i=0; i<playButtons.length; i++) {
            dojo.event.connect(playButtons[i], 'onclick', function(event) {
                dojo.event.browser.stopEvent(event);
                replaceWithFlashButtonAndPlay(event.target);
            });
        }
    });
}());
