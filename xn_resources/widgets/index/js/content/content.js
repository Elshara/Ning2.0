dojo.provide("xg.index.content.content");

xg.index.content.content = {
    submitForm: function(evt) {
        if (evt) {
            dojo.event.browser.stopEvent(evt);
        }
        var form = dojo.byId('content_form');

        // Change page body to progress message (BAZ-932)
        // Show the Uploading Content message in a setTimeout call; otherwise the spinner
        // may fail to appear or stop spinning during the upload
        window.setTimeout(function() {
            form.submit();
            dojo.html.hide('add_content_module');
            dojo.html.show('adding_content_module');
        }, 0);
    },

    handleLaunchBarSubmit: function(url, evt) {
        dojo.event.browser.stopEvent(evt);
        var form = dojo.byId('content_form');
        if (form.successTarget && url) {
            form.successTarget.value = url;
        }
        xg.index.content.content.submitForm(evt);
    },

    handleJoinFlowSubmit: function(direction, evt) {
        var form = dojo.byId('content_form');
        // back or next?
        if (form.joinFlowDirection) {
            form.joinFlowDirection = direction;
        }
        xg.index.content.content.submitForm(evt);
    }
};


xg.addOnRequire(function() {

    var form = dojo.byId('content_form');
    dojo.event.connect(form, 'onsubmit', xg.index.content.content, 'submitForm');

});
