dojo.provide('xg.opensocial.application.addByUrl');

dojo.require('xg.shared.util');

(function() {
    var form = dojo.byId('add_by_url_form');
    if (form) {
        dojo.event.connect(form, 'onsubmit', function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.confirm({
                    title: xg.opensocial.nls.text('addApplication'),
                    bodyHtml: xg.opensocial.nls.html('addApplicationConfirmation', 'href="http://about.ning.com/applicationstos.php"'),
                    onOk: function() {
                        //TODO: Should module_feature be in the HTML already?  Or should we break body_loading out into a separately applicable class?
                        // [Thomas David Baker 2008-09-11]
                        dojo.html.addClass(form, "module_feature");
                        dojo.html.addClass(form, "body_loading");
                        form.submit();
                    }
            });
        });
    }
})();
