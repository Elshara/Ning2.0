dojo.provide('xg.opensocial.application.remove');
dojo.require('xg.shared.util');

/**
 * Behavior for app removal.
 */
(function(){
    var removeApplication = dojo.byId('xg_opensocial_remove_application');
    if (removeApplication) {
        dojo.event.connect(removeApplication, 'onclick', dojo.lang.hitch(this, function(event){
            dojo.event.browser.stopEvent(event);
            xg.shared.util.confirm({title: xg.opensocial.nls.html('removeApplication'),
                                    bodyText: xg.opensocial.nls.html('removeApplicationText'),
                                    okButtonText: xg.opensocial.nls.html('removeApplication'),
                                    onOk: dojo.lang.hitch(dojo.byId('xg_opensocial_remove_form'), function() {
                                              this.submit();
                                              xg.shared.util.progressDialog({
                                                  title: xg.opensocial.nls.text('removeApplication'),
                                                  bodyHtml: xg.opensocial.nls.html('yourApplicationIsBeingRemoved')
                                              });
                                          })
            });
        }));
    }
})();
