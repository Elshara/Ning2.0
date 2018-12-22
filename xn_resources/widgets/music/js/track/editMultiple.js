dojo.provide("xg.music.track.editMultiple");

function licenseChanged(selectValue, hiddenElementId) {
    if(selectValue=='other') {
        dojo.html.show(dojo.byId(hiddenElementId));
    } else {
        dojo.html.hide(dojo.byId(hiddenElementId));
    }
}

(function() {
}());