dojo.provide('xg.index.embed.WelcomeBox');
dojo.require('xg.index.quickadd.loader'); /** @explicit for loadModule */

/**
 * Behavior for the Welcome Box for a new network and for new members.
 */
xg.addOnRequire(function() {
    var div = dojo.byId('welcome_box');
    dojo.event.connect(div, 'onclick', function(evt) {
        dojo.event.browser.stopEvent(evt);
        dojo.io.bind({
            url: div.getAttribute('_url'),
            preventCache: true,
            encoding: 'utf-8',
            mimetype: 'text/javascript',
            load: function(type, data, evt) {
                dojo.dom.removeNode(dojo.dom.getAncestors(div, function(ancestor) { return dojo.html.hasClass(ancestor, 'xg_module'); }, true));
            }
        })
    });

    var ul = dojo.byId('xj_welcomebox_link_container');
    if (ul) {
        var handler = function(el) {
            return function(evt) {
                xg.stop(evt);
                xg.index.quickadd.loadModule(el.getAttribute('module'), el.getAttribute('url'), el.getAttribute('js'));
            }
        };
        for(var nodes = xg.$$('li',ul), i = 0; i<nodes.length; i++) {
            var n = nodes[i].firstChild;
            if (n.getAttribute('module')) {
                xg.listen(n, 'onclick', handler(n));
            }
        }
    }
});
