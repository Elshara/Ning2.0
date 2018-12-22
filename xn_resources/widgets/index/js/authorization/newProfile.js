dojo.provide('xg.index.authorization.newProfile')

dojo.require('xg.shared.topic');
dojo.require('xg.shared.BazelImagePicker');

/**
 * Behavior for the newProfile page.
 */
(function(){

    // Adjust the module body so that it encloses the image picker - needed for Firefox 3 and Safari (BAZ-9751) [Jon Aquino 2008-09-11]
    xg.shared.topic.subscribe('xg.shared.BazelImagePicker.shown', function(bazelImagePicker) {
        xg.shared.topic.unsubscribe('xg.shared.BazelImagePicker.shown', arguments.callee);
        var moduleBody = dojo.html.getElementsByClass('xg_module_body')[0];
        var imagePicker = bazelImagePicker.imagePickerDiv;
        var moduleBodyBottomY = dojo.html.getAbsolutePosition(moduleBody).y + dojo.html.getBorderBoxHeight(moduleBody);
        var imagePickerBottomY = dojo.html.getAbsolutePosition(imagePicker).y + dojo.html.getBorderBoxHeight(imagePicker);
        var heightAdjustment = imagePickerBottomY - moduleBodyBottomY + 5;
        if (heightAdjustment <= 0) { return; }
        var moduleBodyHeight = dojo.html.getComputedStyle(moduleBody, 'height');
        if (moduleBodyHeight == 'auto') { return; } // IE7 [Jon Aquino 2008-09-11]
        dojo.html.setStyle(moduleBody, 'height', heightAdjustment + parseInt(moduleBodyHeight) + 'px');
    });

})();




