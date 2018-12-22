dojo.provide('xg.index.panel');

dojo.require('xg.shared.util');

(function(){

    //  Fix transparency of header images for IE (BAZ-1795)
    var sitename = dojo.byId('xg_sitename');
    if (sitename) {
        var images = sitename.getElementsByTagName('img');
        if (images) {
            xg.shared.util.fixImagesInIE(images, true);
        }
    }

}());
