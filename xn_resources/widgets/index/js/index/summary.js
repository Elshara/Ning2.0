dojo.provide("xg.index.index.summary");

xg.index.index.summary = {
    
    handleLaunchBarSubmit: function(url, evt) {
        dojo.event.browser.stopEvent(evt);
        window.location = url;
    }    
};
