dojo.provide("xg.profiles.appearance.edit");

xg.profiles.appearance.edit = {
    submitForm: function(evt) {
        if (evt) {
            dojo.event.browser.stopEvent(evt);
        }
        var form = dojo.byId('xg_appearance_form');
        form.submit();
    },

    handleJoinFlowSubmit: function(direction, evt) {
        var form = dojo.byId('xg_appearance_form');
        // back or next?
        if (form.joinFlowDirection) {
            form.joinFlowDirection = direction;
        }
        xg.profiles.appearance.edit.submitForm(evt);
    }
};
