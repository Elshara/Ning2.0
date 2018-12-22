dojo.provide('xg.opensocial.shared.AddToMyPageLink');

dojo.require('xg.shared.util');

/**
 * Behavior for adding Add To My Page link to OpenSocial Gadgets
 */
dojo.widget.defineWidget('xg.opensocial.shared.AddToMyPageLink', dojo.widget.HtmlWidget, {
    /** The url of the application to add */
    _appUrl: '',
    
    /** The url to post information to */
    _postUrl: '',
    
    /** The url to send the info as a get, if _postUrl is not defined */
    _getUrl: '',
    
    /** The title of the gadget */
    _gadgetTitle: '',
    
    /** 1 if the application is developed by ning, otherwise 0 */
    _ningApplication: '',
    
    /** Link to the Application TOS */
    _tosUrl: '',
    
    fillInTemplate: function(args, frag) {
        var link = this.getFragNodeRef(frag);
        var that = this;
        dojo.event.connect(link, 'onclick', function(event){
            dojo.event.browser.stopEvent(event);
            xg.shared.util.confirm({title: xg.opensocial.nls.html('addApplication'),
                                    bodyHtml: xg.opensocial.nls.html( (that._ningApplication == 1 ? 'youAreAboutToAddNing' : 'youAreAboutToAdd') , that._gadgetTitle, 'target="_blank" href="' + that._tosUrl + '"'),
                                    okButtonText: xg.opensocial.nls.html('addApplication'),
                                    wideDisplay: true, // Enable a wider display of the pop-up dialog
                                    onOk: function() {
                                        if (that._postUrl) {
                                            that.doSubmit();
					    xg.shared.util.progressDialog({
						title: xg.opensocial.nls.text('addApplication'),
						bodyHtml: xg.opensocial.nls.html('yourApplicationIsBeingAdded')
					    });
                                        } else {
                                            document.location.href = that._getUrl;
                                        }
                                    }
            });
        });
    },
    
    doSubmit: function() {
        var form = dojo.html.createNodesFromText('<form action="' + this._postUrl + '" method="POST"> <input type="hidden" name="appUrl" value="' + this._appUrl + '"/> <input type="hidden" name="xg_token" value="' + xg.token + '"/></form>')[0];
        document.body.appendChild(form);
        form.submit();
    }
});
