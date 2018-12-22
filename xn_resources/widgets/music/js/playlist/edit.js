dojo.provide('xg.music.playlist.edit');

dojo.require('dojo.event.topic');
dojo.require('dojo.dnd.*');
dojo.require('xg.shared.util');

function setDisabled (inputElement, disabled) {
    if(disabled) {
        inputElement.setAttribute('disabled','true');
        dojo.html.addClass(inputElement, 'disabled');
    } else {
        inputElement.removeAttribute('disabled');
        dojo.html.removeClass(inputElement, 'disabled');
    }
}
function removeTrackEntry(deleteLink) {
    dojo.dom.removeNode(deleteLink.parentNode);
    updateZebraAndNumbers();
}

function updateZebraAndNumbers() {
    var playlist = dojo.byId('playlist')
    var tracks = playlist.getElementsByTagName('li');
    var trackListElement = dojo.byId('track-list')
    var newTrackListValue = ''
    var begin = parseInt(dojo.byId('begin-field').value);
    for(var i=0; i<tracks.length; i++) {
        var track = tracks[i]
        if(i%2==0){
            dojo.html.removeClass(track, 'alt');
        } else {
            dojo.html.addClass(track, 'alt');
        }
        dojo.html.getElementsByClass('number', track)[0].innerHTML = (i+1+begin)+'.';
        newTrackListValue += tracks[i].getAttribute('_trackId')
        if (i<tracks.length-1) { newTrackListValue += ',' }
    }
//    setDisabled(dojo.byId('done-button'), false);
    trackListElement.value = newTrackListValue
}
function trackDragEnd(e) {
    updateZebraAndNumbers();
}

function deleteLinkClicked(linkelement){
    linkelement.setAttribute('dojoType','BulkActionLink');
    xg.shared.util.parseWidgets(linkelement);
    dojo.widget.manager.getWidgetByNode(linkelement).confirm();
    return false
}
xg.addOnRequire(function() {
    dojo.event.topic.subscribe('dragEnd', trackDragEnd)
    var playlist = dojo.byId('playlist')
    if(dojo.html.hasClass(playlist, 'can_reorder')) {
        var tracks = playlist.getElementsByTagName('li');
        formSubmission = false;
        for(var i=0; i<tracks.length; i++) {
            new dojo.dnd.HtmlDragSource(tracks[i]).dragClass = 'dragged_track';
        }
        var dropTarget = new dojo.dnd.HtmlDropTarget(dojo.byId('playlist'), '*');
        dropTarget.createDropIndicator = function() {
            this.dropIndicator = document.createElement("div");
            dojo.html.addClass(this.dropIndicator, 'track_drop_indicator');
            this.dropIndicator.style.left = dojo.style.getAbsoluteX(this.domNode, true) + "px";
        };
        //disable Done button (to re-enable it if there is a re-order)
        //setDisabled(dojo.byId('done-button'), true);
        window.onbeforeunload = function(evt) {
            if ((dojo.byId('done-button').getAttribute('disabled') != 'true') && (!formSubmission)){
                //return xg.music.nls.html('thereAreUnsavedChanges')
            }
        }
        dojo.event.connect(dojo.byId('reorder-form'), 'onsubmit', function(evt) { formSubmission = true; } );
    }
});

(function() {
}());