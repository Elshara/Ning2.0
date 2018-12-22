dojo.provide('xg.opensocial.shared.AddApplicationsLink');

dojo.require('xg.shared.util');

/**
 * show pop-up if you're beyond the limit already
 */
dojo.widget.defineWidget('xg.opensocial.shared.AddApplicationsLink', dojo.widget.HtmlWidget, {
    _maxFeatures: '',
    
    fillInTemplate: function(args, frag) {
        var link = this.getFragNodeRef(frag);
        var that = this;
        dojo.event.connect(link, 'onclick', function(event){
            dojo.event.browser.stopEvent(event);
            xg.shared.util.alert({title: xg.opensocial.nls.html('addApplication'),
                                  bodyHtml: xg.opensocial.nls.html('youCanOnlyAdd',that._maxFeatures)
            });
        });
    }
 });
