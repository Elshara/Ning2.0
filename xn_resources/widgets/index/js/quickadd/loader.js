/** $Id: $
 *  Quick Add Loader.
 *  Loads and launches the real code that handles content-specific dialogs.
 *
 *  HTML response can contain multiple HTML elements, but only the first one will be shown with appropriate animation and
 *  set as a dialog DIV.
 */
dojo.provide('xg.index.quickadd.loader');

xg.index.quickadd._dialogs = {};
xg.index.quickadd._stub = undefined;

(function(){
	var inFlight = 0;
	xg.index.quickadd.loadModule = function(module, url, js, reloadHtml) {
	   if ((reloadHtml === null) || ('undefined' === typeof(reloadHtml))) reloadHtml = false;
		if (inFlight || xg.index.quickadd.activeDialog) {
	        return;
		}
        if (!url) { return }

		inFlight = 1;
		xg.shared.util.showOverlay();

        if (xg.index.quickadd._dialogs[module]) {
            if (reloadHtml) {
                dojo.dom.removeNode(xg.index.quickadd._dialogs[module]);
            } else {
				xg.index.quickadd.openDialog(module); // implies that .core is loaded
				inFlight = 0;
				return;
            }
        }

        if (!reloadHtml && xg.index.quickadd._stub) {
            dojo.html.show(xg.index.quickadd._stub);
        } else {
			xg.index.quickadd._stub = document.body.appendChild(xg.shared.util.createElement(
			'<div class="xg_floating_module">'+
				'<div class="xg_floating_container xg_floating_container_wide xg_module" style="top: -30px">'+ // put it close to the screen center
					'<div class="xg_module_body">'+
						'<img src="'+xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif')+'" height="16" width="16">'+
					'</div>'+
				'</div>'+
			'</div>'));
        }

        // @TODO: In case of reloadHtml, we need to 'free' the parsewidget and the htmlelement - BAZ-10090
        var html, loader = function() {
			inFlight = 0;
			var el = document.body.appendChild(xg.shared.util.createElement(html));
            dojo.html.hide(el);
            xg.shared.util.parseWidgets(el);
            // Fire 'load' after, not before, parseWidgets. invite.js assumes that xj_friend_list has been parsed. [Jon Aquino 2008-07-11]
            xg.index.quickadd.fire(module, 'load'); // implies that .core is loaded
            xg.index.quickadd._dialogs[module] = el;
			xg.index.quickadd.openDialog(module);
        };
        var cnt = 2, cntDown = function() { if (0 == --cnt) loader() };
        var reqArgs = ['dojo.lfx.html', 'xg.index.util.FormHelper', 'xg.index.quickadd.core'];
        if (js) {
            reqArgs.push(js);
        }

        // Run 2 requests to load JS and HTML. Call loader() only when both are ready.
        xg.get(url, {}, function(r, data) { html = data; cntDown(); });
        reqArgs.push( cntDown );
        ning.loader.require.apply(ning.loader, reqArgs);
	}
})();
