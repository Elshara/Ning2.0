dojo.provide('xg.profiles.blog.show');

dojo.require('xg.shared.util');

(function() {
    var hiddenPostAction = dojo.byId('post_action');
    if (hiddenPostAction) {
        dojo.lang.forEach(['edit','publish'], function(status) {
            var el = dojo.byId('post_' + status);
            if (el) { dojo.event.connect(el,'onclick',function() { hiddenPostAction.value  = status }); }
        }, true);
    }
    var deleteLink = dojo.byId('deleteBlogPostLink');
    if (deleteLink) {
        dojo.event.connect(deleteLink, 'onclick', function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.confirm({ bodyText: deleteLink.getAttribute('_confirmQuestion'), onOk: dojo.lang.hitch(this, function() {
                var form = xg.shared.util.createElement('<form method="post"><input type="hidden" name="post_action" value="delete" /></form>');
                form.action = deleteLink.getAttribute('_url');
                form.appendChild(xg.shared.util.createCsrfTokenHiddenInput());
                document.body.appendChild(form);
                form.submit();
            }) });
        });
    }
    if (dojo.byId('incrementViewCountEndpoint')) {
        window.setTimeout(function() {
            dojo.io.bind({
                url: dojo.byId('incrementViewCountEndpoint').value,
                method: 'post',
                preventCache: true,
                encoding: 'utf-8',
                mimetype: 'text/javascript'
            });
        }, 5000);
    }
})();
