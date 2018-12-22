/** $Id: $
 * 	Quick Add bar.
 *
 * 	Handles links in the quick add dropdown menu
 *
 */
dojo.provide('xg.index.quickadd.bar');
dojo.require('xg.index.quickadd.loader'); /** @explicit for loadModule */

dojo.widget.defineWidget('xg.index.quickAddBar', dojo.widget.HtmlWidget, {
    fillInTemplate: function(args, frag) {
        var s = this.getFragNodeRef(frag);
        xg.listen(s, 'onchange', function() {
            var module, url, js;
            if (s.selectedIndex) {
                var opt = s.options[s.selectedIndex];
                module = s.value;
                url = opt.getAttribute('url');
                js = opt.getAttribute('js');
            }
            s.value = '';
            if (module) {
                xg.index.quickadd.loadModule(module, url, js);
            }
        });
        s.disabled = false;
    }
});
