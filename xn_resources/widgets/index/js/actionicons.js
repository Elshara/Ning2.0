


// Duplicate of BulkActionLink.js. Need to clean this up - see BAZ-1997. [Jon Aquino 2007-02-27]



dojo.provide('xg.index.actionicons');

dojo.require("dojo.lfx.*");

/*
 * This code is copied (and slightly modified) from xg/photo/index/_shared.js -
 * there should be a shared app-wide sharing button to use
 */

dojo.provide('xg.index.actionicons.PromotionLink');
dojo.widget.defineWidget('xg.index.actionicons.PromotionLink', dojo.widget.HtmlWidget, {
    _action: '<required>', /* promote or remove */
    _id: '<required>', /* id of content object to promote / remove */
    _dialogClass: 'dialog',
    _type: 'item', /* optional type of the object being promoted / removed */
    _afterAction: '', /* code to run after the action happens */

    fillInTemplate: function(args, frag) {
        this.link = this.getFragNodeRef(frag);
        dojo.event.connect(this.link, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.link.className = "desc working disabled";
            // Promote or remove, as appropriate
            var url = xg.global.requestBase + '/main/promotion/' + this._action + '?src=link&xn_out=json';
            dojo.io.bind({'url': url,
                          'method': 'post',
                          'mimetype': 'text/json',
                          'content': { 'id': this._id, 'type': this._type },
                          preventCache: true,
                          encoding: 'utf-8',
                          'load': dojo.lang.hitch(this, function(type, data, evt) {
                              dojo.lfx.html.highlight(this.link, '#ffee7d', 1000, null, dojo.lang.hitch(this, function() {
                                  this.link.style.backgroundImage = this.link.style.backgroundColor = ''; // BAZ-8169 [Jon Aquino 2008-06-20]
                              })).play();
                              // Update the internal _action
                              this._action = (this._action == 'promote') ? 'remove' : 'promote';

                              // Update the icon and text
                              if (data.linkText && data.linkClass) {
                                  dojo.html.setClass(this.link, "desc " + data.linkClass);
                                  this.link.title = data.linkText;
                                  this.link.innerHTML = data.linkText;
                              }

                              if (this._afterAction.length) {
                                  eval(this._afterAction);
                              }
                          } /* load */
                          ) /* hitch */
            } /* hash argument to bind() */
        ); /* bind */
        })); /* connect / hitch / function */
    } /* fillInTemplate */
});
