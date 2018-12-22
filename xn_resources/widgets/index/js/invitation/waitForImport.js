dojo.require('xg.shared.util');
dojo.provide('xg.index.invitation.waitForImport');

/**
 * Behavior for the waitForImport page.
 */
(function() {

    var jobId = dojo.byId('jobId').value;
    var target = dojo.byId('target').value;
    var checking = false;
    var check = function() {
        if (checking) { return; }
        checking = true;
        dojo.io.bind({
            url: '/main/invitation/checkImport?xn_out=json&jobId=' + encodeURIComponent(jobId),
            method: 'post',
            preventCache: true,
            encoding: 'utf-8',
            mimetype: 'text/javascript',
            load: function(type, data, event) {
                if (! data) { return window.location = '/main/error'; }
                if (! ('complete' in data)) { return window.location = '/main/error'; }
                if (! data.complete) { return checking = false; }
                window.location = decodeURIComponent(xg.shared.util.addParameter(target, 'contactListId', data.contactListId));
            }
        });
    }
    check();
    setInterval(check, 5000);
}());

