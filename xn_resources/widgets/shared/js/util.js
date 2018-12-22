dojo.provide('xg.shared.util');

/**
 *  Adds an event listener
 *
 *  @param      src			object|id	ID or object
 *  @param		evt			string		Event name
 *  @param		cb1+cb2		callback		either object,function or function
 *  @return     void
 */
xg.listen = function (src, evt, cb1, cb2) {
    dojo.event.connect("string" == src ? dojo.byId(src) : src, evt, "function" == typeof cb1 ? cb1 : function(){ cb2.apply(cb1,arguments) });
};
xg.stop = function (evt) {
    dojo.event.browser.stopEvent(evt);
}
xg.qh = function (str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}

/**
 *  CSS selector. RETURNS ONLY THE FIRST MATCHING NODE! Use $$() to get all nodes.
 *  For now only:
 *  	#ID
 *  	ELEMENT
 *  	.CLASS
 *  	ELEMENT.CLASS
 *	rules are supported
 *
 *  @param      rule	string				CSS rule
 *  @param		root	string|DOMNode	    Root node or node-id
 *  @return     array
 *  @deprecated  Use jQuery instead (x$)
 */
xg.$ = function(rule, root) {
    if (rule.substr(0,1) == '#') {
        return dojo.byId(rule.substr(1));
    }
    return xg.$$(rule, root)[0];
}
/**
 *  CSS selector. Syntax is the same as for xg.$(), but always returns the array of nodes, even for #ID.
 *  Mnemonic: if $ stands for tag, then $$ stands for tags.
 *
 *  @deprecated  Use jQuery instead (x$)
 */
xg.$$ = function(rule, root) {
    if (rule.substr(0,1) == '#') {
        return [dojo.byId(rule.substr(1))];
    }
    rule = rule.split('.',2);
    if ("string" == typeof root) {
        root = document.getElementById(root);
    }
    if (!rule[1]) {
        return (root||document.body).getElementsByTagName(rule[0]);
    }
    return dojo.html.getElementsByClass(rule[1], root, rule[0]);
}
/**
 * 	Return the parent node.
 *
 * 	@param	el			DOMNode		Element
 * 	@param	selector	string		CSS selector. TAG, .CLASS and TAG.CLASS are supported
 * 	@return	DOMNode | null
 */
xg.parent = function(el, selector) {
    selector = (selector||'').split('.');
    var tag = selector[0].toUpperCase();
    var cls = selector[1] ? new RegExp("(^|\\s+)"+selector[1]+"(\\s+|$)") : '';
    while(el = el.parentNode) {
        if ( (!tag || el.tagName == tag) && (!cls || el.className.match(cls)) ) {
            return el;
        }
    }
    return null;
}

/**
 *  Performs GET/POST XHR.
 *  Content type is detected from the response and can be
 *  	text/json
 *  	text/javascript
 *  	application/xml
 *  	text/xml
 *  Callback receives 2 parameters: xhr and json|text|xml data (depends on the response content type)
 *
 *  @param      url			string			URL
 *  @param		content		hash|DomNode    Content of <form>...</form> node. If it's a hash:
 *  										  - add preventCache = false|true if you need it.
 *  										  - add formNode = formNode if you need both formNode and content
 *  @param		cb1+cb2		callback		either object,function or function
 *  @return     XMLHttpRequest
 */
xg._xhr = function(method, url, content, cb1, cb2) {
    var req = {url: url, method: method, encoding: 'utf-8', mimetype: 'text/plain', load: function (tmp1, tmp2, http) {
        var ct = http.getResponseHeader("Content-Type"), ret;
        try {
            if (ct.indexOf('text/javascript') != -1) { ret = dj_eval(http.responseText) }
            else if (ct.indexOf('text/json') != -1) { ret = dj_eval("("+http.responseText+")") }
            else if (ct.indexOf('/xml') != -1) { ret = http.responseXML }
            else { ret = http.responseText }
        } catch(e) {
            ret = null
        }
        "function" == typeof cb1 ? cb1(http, ret) : cb2.call(cb1, http, ret);
    }};
    if (content) {
        if (content.constructor != Object) {
            req.formNode = content;
        } else {
            if ("undefined" != typeof content["preventCache"]) {
                req.preventCache = content["preventCache"];
                delete content["preventCache"];
            }
            if ("undefined" != typeof content["formNode"]) {
                req.formNode = content["formNode"];
                delete content["formNode"];
            }
            req.content = content;
        }
    }
    return dojo.io.bind(req);
}
xg.get = function(url, content, cb1, cb2) {
    return xg._xhr('get', url, content, cb1, cb2);
};
xg.post = function(url, content, cb1, cb2) {
    return xg._xhr('post', url, content, cb1, cb2);
};

xg.shared.util = {
    /**
     *  Creates HTML element from passed text. If text contains several tags, only first is returned.
     *  Function should be used instead of dojo.html.createNodesFromText()[0] when text contains <form>...</form> tag.
     *  In IE form elements break when they're clonned (as createNodesFromText does).
     *
     *  Leading/trailing spaces are trimmed.
     *
     *  @param      text   string    Text of HTML element, for example: <div class="xg_"><span>...
     *  @return     DOMNode
     */
    createElement: function(text) {
        var el = document.createElement('div');
        el.innerHTML = text.replace(/^\s+/,'').replace(/\s+$/,'');
        return el.firstChild || undefined;
    },
    /**
     *  Calculates offset between two elements.
     *
     *  @param      el   	DOMNode		Usually a static node
     *  @param		node	DOMNode		Usually a dynamically positioned node
     *  @return     {x,y}
     *  @deprecated  Use getOffsetX instead
     */
    getOffset: function (el, node) {
        var x = 0, y = 0;
        var parents = [];
        for (; node; node = node.parentNode) parents.push(node);
        for (var cur = el; cur; cur = cur.offsetParent) {
            var p = dojo.style.getStyle(cur, 'position');
            if (p == 'relative' || p == 'absolute') {
                var is_p = 0;
                for (var i = 0; i < parents.length; i++) if (cur == parents[i]) {
                    is_p = 1;
                    break;
                }
                if (is_p) break;
            }
            x += cur.offsetLeft || 0;
            y += cur.offsetTop || 0;
            if (cur.tagName == 'BODY') break;
        }
        return { x: x, y: y };
    },

    /**
	 *  Calculates offset between two elements (proper variant).
	 *  We need to merge these two functions together. IE7.sucks. [Andrey 2008-09-16]
     *
     *  @param      el   	DOMNode		Usually a static node
     *  @param		node	DOMNode		Usually a dynamically positioned node
     *  @return     {x,y}
     */
    getOffsetX: function (el, node) {
        // TODO: Rename getOffset to oldGetOffset, and this function to getOffset [Jon Aquino 2008-09-27]
		var e = x$(el).offset(), n = x$(node).offset();
		return { x: e.left - n.left, y: e.top - n.top};
    },

    _widgetParsingStrategy: 0,
    safeBindUrl: function(url) {
        // IE and Safari don't like [ or ] in the request
        return url.replace(/\[/g, "%5B").replace(/\]/g, "%5D");
    },

    parseUrlParameters: function(url) {
        var urlParts   = url.split('?');
        var urlContent = new Object;

        if (urlParts.length > 1) {
            var urlPairs   = urlParts[1].split('&');

            for (var idx = 0; idx < urlPairs.length; idx++) {
                var kv = urlPairs[idx].split('=');

                urlContent[kv[0]] = kv[1];
            }
        }
        return urlContent;
    },

    /**
     * Parses Dojo widgets in the given DOM subtree.
     * Be careful not to parse widgets more than once.
     *
     * @param HTMLElement  The root of the subtree to parse, or null to parse the entire document.
     */
    parseWidgets: function(root) {
        var root = root || document.getElementsByTagName("body")[0] || document.body;
        var parser = new dojo.xml.Parse();
        var frag = parser.parseElement(root, null, true);
        // Use createComponents rather than createSubComponents, which doesn't parse the top-level node [Jon Aquino 2007-01-31]
        dojo.widget.getParser().createComponents(frag);
    },

    /**
     * Fixes two problems with img tags in IE:
     * <ul>
     *     <li>png transparency doesn't work</li>
     *     <li>img tags created with Javascript sometimes fail to display if they aren't preloaded</li>
     * </ul>
     *
     * @param imgs array of image tags, typically found using node.getElementsByTagName('img')
     * @param sync whether to fix the image synchronously or asynchronously i.e. whether it is more important to display the image
     *         as soon as possible (as in the case of the app header image) or to allow processing to continue (important when preloading
     *         all of the images of a panel -- you want the panel to appear ASAP, and the images can be filled in afterwards).
     *         Setting sync to true reduces the duration of the flash of opaque in the app header icon for some reason (from 1 second to instantaneous).
     * @param width optional. Used if img.width is 0, which sometimes happens
     * @param height optional. Used if img.height is 0, which sometimes happens
     * @see Guyon Roche, "JavaScript Image Preloader", http://www.webreference.com/programming/javascript/gr/column3/
     */
    fixImagesInIE: function(imgs, sync, width, height) {
        if (! (dojo.render.html.ie50 || dojo.render.html.ie55 || dojo.render.html.ie60)) { return; }
        dojo.lang.forEach(imgs, function(img) {
            if (dojo.lang.inArray(xg.shared.util.fixedImageURLs, img.src)) { return; }
            var fixImage = function() {
                var image = new Image();
                image.onload = image.onerror = image.onabort = function() {
                    img.src = img.src;
                    xg.shared.util.fixTransparencyInIEProper(img, width, height);
                    xg.shared.util.fixedImageURLs.push(img.src);
                }
                image.src = img.src;
            }
            if (sync) { fixImage(); }
            else { window.setTimeout(fixImage, 0); }
        });
    },

    fixedImageURLs: [],

    /**
     * Consider fixImagesInIE instead of using this function directly; fixImagesInIE takes care of both
     * preloading images loaded with javascript and fixing transparency.
     *
     * img.width and img.height are sometimes 0 for some reason. If this happens, you can specify the
     * width and height explicitly.
     * @see fixImagesInIE
     */
    fixTransparencyInIEProper: function(img, width, height) {
        if (img && (dojo.render.html.ie50 || dojo.render.html.ie55 || dojo.render.html.ie60)
                && img.src.match(/png/) && dojo.style.isShowing(img)) {
            // The header image is sometimes distorted in IE (half-loaded, or incorrectly sized).
            // Preloading (as is done with fixImagesInIE) probably fixes this, but it also delays the image
            // from appearing for a couple of seconds. [Jon Aquino 2006-05-31]
            width = width ? width : img.width;
            height = height ? height : img.height;
            img.style.width = width + "px";
            img.style.height = height + "px";
            img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + img.src + "', sizingMethod='scale')";
            img.src = xg.shared.util.cdn('/xn_resources/widgets/index/gfx/x.gif');
        }
        if (img) { img.style.visibility = 'visible'; }
    },

    fixTransparencyInIE: function(node) {
        if (dojo.render.html.ie50 || dojo.render.html.ie55 || dojo.render.html.ie60) {
            dojo.lang.forEach(node.getElementsByTagName('img'), function(img) {
                xg.shared.util.fixTransparencyInIEProper(img);
            });
        }
    },

    /**
     *  Fixes the dialog position, centering it and adding the scrollbar if necessary
     *
     *  @param  dlg     DOMNode     xg_module floating div.
     */
    fixDialogPosition: function(dlg) {
        var container = xg.$('div.xg_floating_container', dlg);
        var vh = parseInt(dojo.html.getViewportHeight());

        container.style.height = 'auto';
        container.style.overflow = 'visible';
        var h = parseInt(container.offsetHeight);
        if (h > vh * 0.9) {
            container.style.height = parseInt(vh * 0.9) + 'px';
            container.style.overflow = 'auto';
        }
        var drh = dojo.render.html;
        container.style.top = (drh.ie && (drh.ie60 || drh.ie55 || drh.ie50) ? 0 : -parseInt(container.offsetHeight / 2)) + "px"; // 14 is the double border width.
    },

    /**
     * Replaces newlines with <br />s, except on lines with certain HTML elements like <p>
     *
     * @param string s The original text or HTML
     * @return string  The text with <br />s inserted
     * @see xg_nl2br
     */
    nl2br: function(s) {
        s = s.replace(/\r\n/, "\n"); // IE [Jon Aquino 2007-03-31]
        result = '';
        dojo.lang.forEach(s.split("\n"), function (line) {
            result += line;
            // See Web Design Group, "HTML 4 Block-Level Elements", http://htmlhelp.com/reference/html40/block.html  [Jon Aquino 2007-03-31]
            // Keep this list in sync with xg_nl2br [Jon Aquino 2007-04-02]
            if (! line.match(/<.?OBJECT\b|<.?EMBED\b|<.?PARAM\b|<.?APPLET\b|<.?IFRAME\b|<.?SCRIPT\b|<.?BR\b|<.?ADDRESS\b|<.?BLOCKQUOTE\b|<.?CENTER\b|<.?DIR\b|<.?DIV\b|<.?DL\b|<.?FIELDSET\b|<.?FORM\b|<.?H1\b|<.?H2\b|<.?H3\b|<.?H4\b|<.?H5\b|<.?H6\b|<.?HR\b|<.?ISINDEX\b|<.?MENU\b|<.?NOFRAMES\b|<.?NOSCRIPT\b|<.?OL\b|<.?P\b|<.?PRE\b|<.?TABLE\b|<.?UL\b|<.?DD\b|<.?DT\b|<.?FRAMESET\b|<.?LI\b|<.?TBODY\b|<.?TD\b|<.?TFOOT\b|<.?TH\b|<.?THEAD\b|<.?TR\b/i)) {
                result += '<br />';
            }
            result += "\n";
        });
        return dojo.string.trim(result).replace(/(<br \/>)+$/, '');
    },

    /**
     * Shows the lightbox background.
     */
    showOverlay: function() {
        var o = dojo.byId('xg_overlay');
        if (o.style.display == 'none') {
            o.style.height = this.getPageHeight() + 'px';
            o.style.display = 'block';
        }
    },

    /**
     * Hides the lightbox background.
     */
    hideOverlay: function() {
        var o = dojo.byId('xg_overlay');
        if (o.style.display != 'none') {
            o.style.display = 'none';
        }
    },

    /**
     * Returns the distance between the top and bottom of the document's content, or the
     * viewport height, whichever is larger.
     */
    getPageHeight: function() {
        var yScroll;

        if (window.innerHeight && window.scrollMaxY) {
            yScroll = window.innerHeight + window.scrollMaxY;
        } else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
            yScroll = document.body.scrollHeight;
        } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
            yScroll = document.body.offsetHeight;
        }

        var windowHeight;
        if (self.innerHeight) {	// all except Explorer
            windowHeight = self.innerHeight;
        } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
            windowHeight = document.documentElement.clientHeight;
        } else if (document.body) { // other Explorers
            windowHeight = document.body.clientHeight;
        }

        // for small pages with total height less then height of the viewport
        if(yScroll < windowHeight){
            pageHeight = windowHeight;
        } else {
            pageHeight = yScroll;
        }

        return pageHeight;
    },

    /**
     * Prevents the user from entering text longer than the given maxLength.
     * Note that this does not prevent the user from pasting in text that exceeds maxLength,
     * so you should validate that as well (on the client and the server).
     *
     * @param textarea  the textarea node
     * @param maxLength  the maximum allowed length
     * @see twitter.com
     */
    setMaxLength: function(textarea, maxLength) {
        x$(textarea).bind('keypress', function(e) { 
            var key = e.which || e.keyCode;
            if (key != 8 /*backspace*/ &&
                key != 46 /*delete*/ && 
                key != 37 /*left*/ && 
                key != 39 /*right*/ && 
                key != 38 /*up*/ && 
                key != 40 /*down*/ && 
                textarea.value.length >= maxLength) {
                e.preventDefault();
            }
        });
    },

    /**
     * Notify user that he/she entered too much. You should check the actual length when user clicks submit.
     *
     * @param textarea  the textarea node
     * @param maxLength  the maximum length
     * @param helpText  (optional) help text to display in the same span when no error is present
     * @see twitter.com
     */
    setAdvisableMaxLength: function(textarea, maxLength, helpText) {
        var msgEl, timer, inErrorMode = 0, parent = textarea.parentNode, updateStatus = function() {
            if (textarea.value.length > maxLength) {
                msgEl.innerHTML = xg.shared.nls.text('messageIsTooLong', textarea.value.length, maxLength);
                if (!inErrorMode) {
                    dojo.html.addClass(textarea.parentNode, 'error');
                    dojo.html.addClass(msgEl, 'error');
                }
                inErrorMode = 1;
            } else {
                if (inErrorMode) {
                    msgEl.innerHTML = helpText || ''; // can display the actual number of characters
                    dojo.html.removeClass(textarea.parentNode, 'error');
                    dojo.html.removeClass(msgEl, 'error');
                }
                inErrorMode = 0;
            }
            timer = 0;
        };

        msgEl = document.createElement('small');
        msgEl.innerHTML = helpText || '';
        textarea.nextSibling ? parent.insertBefore(msgEl, textarea.nextSibling) : parent.appendChild(msgEl, textarea);

        var triggerUpdate = function () { if (!timer) timer = window.setTimeout(updateStatus, 50); }
        dojo.event.connect(textarea, 'onkeyup', triggerUpdate);
        dojo.event.connect(textarea, 'onkeypress', triggerUpdate);
        dojo.event.connect(textarea, 'onblur', triggerUpdate);
        dojo.event.connect(textarea, 'oncut', triggerUpdate);
        dojo.event.connect(textarea, 'onpaste', triggerUpdate);
        dojo.event.connect(textarea, 'onchange', triggerUpdate);
    },

    /**
     * Display a count down showing how many characters are left to the user when filling
     * out a form with a limit.
     *
     * Once the maxLength has been reached, turn display into a simpleerrordesc wrapped
     * <span>
     *
     * Expects DOM structure like:
     * <code>
     *     <... wrapper ...>
     *         <textarea />
     *         <span>
     *             ... any messages ...
     *         </span>
     *     </... wrapper ...>
     * </code>
     *
     * @param textarea  the textarea node
     * @param maxLength the maximum allowed length
     */
    setAdvisableMaxLengthWithCountdown: function(textarea, maxLength) {
        var timer, inErrorMode = 0;
        var messageContainer = dojo.dom.nextElement(textarea, 'span');
        var charsLeftId = textarea.id + "_chars_left";
        var msgEl = dojo.byId(charsLeftId);
        var updateStatus = function() {
            if (!msgEl) {
                msgEl = document.createElement('small');
                msgEl.id = charsLeftId;
                messageContainer.appendChild(msgEl);
            }

            msgEl.innerHTML = xg.shared.nls.html('charactersLeft', maxLength - textarea.value.length);
            if (textarea.value.length > maxLength) {
                if (!inErrorMode) {
                    dojo.html.addClass(messageContainer, 'simpleerrordesc');
                }
                inErrorMode = 1;
            } else {
                if (inErrorMode) {
                    dojo.html.removeClass(messageContainer, 'simpleerrordesc');
                }
                inErrorMode = 0;
            }
            timer = 0;
        }
        var triggerUpdate = function() { if (!timer) {
            timer = window.setTimeout(updateStatus, 50);
        }}
        dojo.event.connect(textarea, 'onkeyup', triggerUpdate);
        dojo.event.connect(textarea, 'onkeypress', triggerUpdate);
        dojo.event.connect(textarea, 'onblur', triggerUpdate);
        dojo.event.connect(textarea, 'oncut', triggerUpdate);
        dojo.event.connect(textarea, 'onpaste', triggerUpdate);
        dojo.event.connect(textarea, 'onchange', triggerUpdate);

        // initialize one update when loading to add the chars left wording
        triggerUpdate();
    },

    /**
     * Displays an alert message
     *
     * @param string alert message, or object with the following properties:
     *   bodyHtml  HTML for the dialog message (to be placed in a <p>)
     *   title     (optional) plain-text title, defaults to none
     *   param     (optional) okButtonText  text for the OK button; defaults to "OK"
     *   onOk      (optional) function pointer to call when clicking "OK"
     *   autoCloseTime (optional) the alert will automatically close after this time (in ms) - no OK button
     */
    alert: function(stringOrObject) {
        //  If there's already an alert up, remove it
        if (dojo.byId('xg_lightbox_alert')) {
            dojo.dom.removeNode(dojo.byId('xg_lightbox_alert'));
        }
        //  Is the argument a string or an object?
        if ((typeof stringOrObject) == 'string') {
            args = {bodyHtml: stringOrObject};
        } else {
            args = stringOrObject;
        }
        args.onOk = args.onOk ? args.onOk : function() {};
        args.autoCloseTime = args.autoCloseTime ? args.autoCloseTime : 0;
        if (!args.okButtonText) {
            args.okButtonText = xg.shared.nls.text('ok');
        }
        var wideDisplayClass = args.wideDisplay ? ' xg_floating_container_wide' : '';
        var dialogHtml = dojo.string.trim(' \
                <div class="xg_floating_module" id="xg_lightbox_alert"> \
                    <div class="xg_floating_container xg_module'+wideDisplayClass+'"> \
                        <div class="xg_module_head ' + (args.title ? '' : 'notitle') + '"> \
                            ' + (args.title ? '<h2>' + dojo.string.escape('html', args.title) + '</h2>' : '') + ' \
                        </div> \
                        <div class="xg_module_body"> \
                            <p>' + args.bodyHtml + '</p>');
        if (args.autoCloseTime < 1) {
            dialogHtml += dojo.string.trim(' \
                            <p class="buttongroup"> \
                                <input type="button" class="button" value="' + dojo.string.escape('html', args.okButtonText) + '" /> \
                            </p>');
        }
        dialogHtml += dojo.string.trim(' \
                        </div> \
                    </div> \
                </div>');
        var dialog = dojo.html.createNodesFromText(dialogHtml)[0];
        this.showOverlay();
        document.body.appendChild(dialog);
        this.fixDialogPosition(dialog);
        if (args.autoCloseTime < 1) {
            dojo.event.connect(dojo.html.getElementsByClass('button', dialog)[0], 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                dojo.dom.removeNode(dialog);
                this.hideOverlay();
                args.onOk(dialog);
            }));
        } else {
            setTimeout(dojo.lang.hitch(this, function() {
                dojo.dom.removeNode(dialog);
                this.hideOverlay();
                args.onOk(dialog);
            }), args.autoCloseTime);
        }
        return dialog;
    },

    /**
     * Displays a message indicating that processing is underway.
     *
     * @param args  object with the following properties:
     *         title     (optional) plain-text title, defaults to none
     *         bodyHtml  HTML for the dialog message (to be placed in a <p>)
     * @return  an object that you can call hide() on to hide the progress dialog
     */
    progressDialog: function(args) {
        if (dojo.byId('xg_lightbox_alert')) {
            dojo.dom.removeNode(dojo.byId('xg_lightbox_alert'));
        }
        var dialogHtml = dojo.string.trim(' \
                <div class="xg_floating_module" id="xg_lightbox_alert"> \
                    <div class="xg_floating_container"> \
                        <div class="xg_module_head ' + (args.title ? '' : 'notitle') + '"> \
                            ' + (args.title ? '<h2>' + dojo.string.escape('html', args.title) + '</h2>' : '') + ' \
                        </div> \
                        <div class="xg_module_body"> \
                            <img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '" alt="" class="left" style="margin-right:5px" width="20" height="20"/> \
                            <p style="margin-left:25px">' + args.bodyHtml + '</p> \
                        </div> \
                    </div> \
                </div>');
        var dialog = dojo.html.createNodesFromText(dialogHtml)[0];
        this.showOverlay();
        document.body.appendChild(dialog);
        return { hide: dojo.lang.hitch(this, function() {
            dojo.dom.removeNode(dialog);
            this.hideOverlay();
        })};
    },

    /**
     * Displays a message while the user is being redirected to a new target
     *
     * @param args  object with the following properties:
     *         title     (optional) plain-text title, defaults to none
     *         bodyHtml  HTML for the dialog message (to be placed in a <p>)
     *         target    new target
     * @return  an object that you can call hide() on to hide the progress dialog
     */
    showDialogAndRedirect: function(args) {
        if (dojo.byId('xg_lightbox_alert')) {
            dojo.dom.removeNode(dojo.byId('xg_lightbox_alert'));
        }
        var dialogHtml = dojo.string.trim(' \
                <div class="xg_floating_module" id="xg_lightbox_alert"> \
                    <div class="xg_floating_container"> \
                        <div class="xg_module_head ' + (args.title ? '' : 'notitle') + '"> \
                            ' + (args.title ? '<h2>' + dojo.string.escape('html', args.title) + '</h2>' : '') + ' \
                        </div> \
                        <div class="xg_module_body"> \
                            <p>' + args.bodyHtml + '</p> \
                        </div> \
                    </div> \
                </div>');
        var dialog = dojo.html.createNodesFromText(dialogHtml)[0];
        this.showOverlay();
        document.body.appendChild(dialog);
        window.location = args.target;
    },

    /**
     * Displays a confirmation prompt.
     *
     * @param title  plain-text title; defaults to "Confirmation"
     * @param bodyText  plain text for the dialog message
     * @param bodyHtml  HTML for the dialog message; alternative to bodyText
     * @param okButtonText  text for the OK button; defaults to "OK"
     * @param onOk  function to call if the person presses OK. The dialog node is passed as the first argument.
     * @param onCancel  function to call if the person presses Cancel.  The dialog node is passed as the first argument.
     * @param extraButton {title,onClick} 	Extra button to show between Ok/Cancel buttons
     * @param closeOnlyIfOnOk bool if true, pressing OK closes the dialog only if onOk returns true
     */
    confirm: function(args) {
        args.title = args.title ? args.title : xg.shared.nls.text('confirmation');
        args.okButtonText = args.okButtonText ? args.okButtonText : xg.shared.nls.text('ok');
        args.onOk = args.onOk ? args.onOk : function() {};
        args.onCancel = args.onCancel ? args.onCancel : function() {};
        if (args.bodyText) { args.bodyHtml = '<p>' + dojo.string.escape('html', args.bodyText) + '</p>'; }
        // ContactList.js assumes that the dialog contains a <form>...</form>  [Jon Aquino 2007-10-25]
        var wideDisplayClass = args.wideDisplay ? ' xg_floating_container_wide' : '';
        var dialog = dojo.html.createNodesFromText(dojo.string.trim('\
                <div class="xg_floating_module"> \
                    <div class="xg_floating_container'+wideDisplayClass+'"> \
                        <div class="xg_module_head"> \
                            <h2>' + dojo.string.escape('html', args.title) + '</h2> \
                        </div> \
                        <div class="xg_module_body"> \
                            <form> \
                                <input type="hidden" name="xg_token" value="' + xg.token + '" /> \
                                ' + args.bodyHtml + ' \
                                <p class="buttongroup"> \
                                    <input type="submit" class="button button-primary" value="' + dojo.string.escape('html', args.okButtonText) + '"/> '+
                                    (args.extraButton && args.extraButton.title ? '<input type="button" class="button xj_custom" value="'+args.extraButton.title+'" /> ' : '') +
                                    '<input type="button" class="button xj_cancel" value="' + xg.shared.nls.html('cancel') + '" /> \
                                </p> \
                            </form> \
                        </div> \
                    </div> \
                </div>'))[0];
        this.showOverlay();
        document.body.appendChild(dialog);
        this.fixDialogPosition(dialog);
        xg.listen(xg.$('.xj_cancel', dialog), 'onclick', this, function(event) {
            xg.stop(event);
            dojo.dom.removeNode(dialog);
            this.hideOverlay();
            if (args.onCancel) args.onCancel();
        });
        if (args.extraButton && args.extraButton.title) {
            xg.listen(xg.$('.xj_custom',dialog), 'onclick', this, function() {
                dojo.dom.removeNode(dialog);
                this.hideOverlay();
                if (args.extraButton.onClick) args.extraButton.onClick(dialog);
            });
        }
        xg.listen(xg.$('form',dialog), 'onsubmit', this, function(event) {
            xg.stop(event);
            // Hide dialog rather than remove it; otherwise radio buttons will be reset in IE (BAZ-) [Jon Aquino 2007-05-09]
            if (args.closeOnlyIfOnOk) {
                if (args.onOk(dialog)) {
                    dojo.style.hide(dialog);
                    this.hideOverlay();
                }
            } else {
                dojo.style.hide(dialog);
                this.hideOverlay();
                args.onOk(dialog);
            }
        });
        return dialog;
    },

    /**
     * Prompts the user to join the network or current group. Once the user chooses to join, subsequent
     * calls to this function will simply call onOk().
     *
     * @param name  the text for the prompt, or null or an empty string to skip the prompt
     * @param membershipPending  whether the user's membership is pending approval
     * @param onOk  function to call if the person presses OK
     */
    promptToJoin: function(text, membershipPending, onOk) {
        if (typeof membershipPending == 'function') {
            onOk = membershipPending;
            membershipPending = false;
        }
        if (membershipPending) {
            this.promptIsPending();
            return;
        }
        if (this.joined || ! text) {
            onOk();
            return;
        }
        xg.shared.util.confirm({
            title: xg.shared.nls.text('joinNow'),
            bodyHtml: '<p>' + dojo.string.escape('html', text) + '</p>',
            okButtonText: xg.shared.nls.text('join'),
            onOk: dojo.lang.hitch(this, function() {
                this.joined = true;
                onOk();
            })
        });
    },

    /**
     * Tells the user that they can't continue since their membership is pending
     */
     promptIsPending: function() {
         xg.shared.util.alert({
                 title: xg.shared.nls.text('pendingPromptTitle'),
                 bodyHtml: '<p>' + xg.shared.nls.html('youCanDoThis') + '</p>'
         });
     },

    /**
     * Selects the contents of the given text field if you click it; makes it read-only.
     * Useful for text fields containing <embed> code.
     *
     * @param input  a text field
     */
    selectOnClick: function(input) {
        dojo.event.connect(input, 'onfocus', function(event) {
            dojo.html.selectInputText(input);
        });
        dojo.event.connect(input, 'onclick', function(event) {
            dojo.html.selectInputText(input);
        });
        var text = input.value;
        dojo.event.connect(input, 'onkeyup', function(event) {
            dojo.html.selectInputText(input);
            input.value = text;
        });
    },

    /**
     * Intercepts Enter keypresses in the textfield so that its containing form is not submitted.
     *
     * @param textInput  the text <input>
     * @param onEnterPress  optional callback to call when Enter is pressed
     */
    preventEnterFromSubmittingForm: function(textInput, onEnterPress) {
        if (! onEnterPress) { onEnterPress = function() {}; }
        dojo.event.connect(textInput, 'onkeydown', function(event) {
            if (event.keyCode == 13 /*enter*/) {
                dojo.event.browser.stopEvent(event);
                onEnterPress();
            }
        });
    },

    /**
     * Sets the default value for the textfield that is cleared upon focus or form submit.
     *
     * @param textInput  <input:text>
     * @param value  	string			Default value
     */
    setPlaceholder: function(textInput, value) {
        if (textInput.value != '') {
            return;
        }
        textInput.value = value;
        dojo.event.connect(textInput, 'onfocus', function(event) {
            if (textInput.value == value) {
                textInput.value = '';
            }
        });
        dojo.event.connect(textInput, 'onblur', function(event) {
            if (textInput.value == '') {
                textInput.value = value;
            }
        });
        dojo.event.connect(textInput.form, 'onsubmit', function(event) {
            if (textInput.value == value) {
                textInput.value = '';
            }
        });
    },

    /**
     * Creates a hidden <input> for the CSRF token.
     *
     * @return the input element, for adding to forms
     */
    createCsrfTokenHiddenInput: function() {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'xg_token';
        input.value = xg.token;
        return input;
    },

    /**
     * Converts the URL to an equivalent URL served by the Ning CDN.
     *
     * @param url  the URL to convert
     * @param addVersionParameter  whether to append the app's current version to the URL
     * @return   the CDN version of the URL, or the original URL if a CDN equivalent could not be determined.
     */
    cdn: function(url) {
        var cdnUrl = url.replace(/.*\/xn_resources(.*)/, xg.cdn + '/' + ning.CurrentApp.id + '$1');
        if (url !== cdnUrl) { cdnUrl = this.addParameter(cdnUrl, 'v', xg.version); }
        return cdnUrl;
    },

    /**
     * Removes all occurrences of the specified parameter
     *
     * @param url  the URL
     * @param name  the name of the parameter
     *
     * @return string  the updated URL
     */
    removeParameter: function(url, name) {
        var urlParts = url.split('?', 2);
        if (urlParts[1]) {
            var params = urlParts[1].split('&');
            var newParams = [];
            for (var i = 0; i < params.length; i++) {
                var data = params[i].split('=', 2);
                if (data[0] != name) {
                    newParams.push(params[i]);
                }
            }
            if (newParams.length > 0) {
                urlParts[1] = newParams.join('&');
                return urlParts.join('?');
            } else {
                return urlParts[0];
            }
        } else {
            return url;
        }
    },

    /**
     * Adds or replaces the given parameter.
     *
     * @param url  the URL
     * @param name  the name of the parameter
     * @param value  the value of the parameter
     * @return  the updated URL
     */
    addParameter: function(url, name, value) {
        var delimiter = url.indexOf('?') > -1 ? '&' : '?';
        return url + delimiter + encodeURIComponent(name) + '=' + encodeURIComponent(value);
    },

    /**
     * Parse a formatted string representation of a number and return its integer value
     * e.g., '1,278,941' => 1278941
     *
     * @param value string  the formatted string number
     *
     * @return integer|NaN  the integer value or NaN on parse error
     */
    parseFormattedNumber: function(value) {
        if (value) {
            var number = value.replace(/\D+/g, '');
            return parseInt(number);
        }
        return NaN;
    },

    /**
     * Format an integer for display by adding thousands separators as appropriate
     * e.g., 1278941 => '1,278,941'
     *
     * @param value integer  the integer value to format
     * @param separator string  the thousands separator character
     *
     * @return string  the formatted number
     */
    formatNumber: function(value, separator) {
        var sep = separator || xg.num_thousand_sep || ',';
        if ((value < 1000) && (value > -1000)) {
            // no formatting required
            return value + '';
        }
        var isNegative = value < 0;
        value = Math.abs(value) + '';  // typecast to string
        var valueLen = value.length;
        var charCount = (3 - (valueLen % 3)) % 3;
        var formattedValue = '';
        for (i = 0; i < valueLen; i++) {
            formattedValue += value.charAt(i);
            charCount = (charCount + 1) % 3;
            if ((charCount == 0) && (i < valueLen - 1)) {
                formattedValue += sep;
            }
        }
        return isNegative ? '-'+formattedValue : formattedValue;
    },

    /**
     * Returns an object that will execute the callback when its trigger() method is called, but only after a
     * given "quiescent period" in which trigger() is not called. Useful for triggering an expensive event; it will
     * run only after the triggers quiet down.
     *
     * @param milliseconds  period of inactivity before the callback will be fired
     * @param callback  function that trigger() will call after the given period of inactivity
     */
    createQuiescenceTimer: function(milliseconds, callback) {
        var lastTriggerId = 0;
        return {
            trigger : function() {
                lastTriggerId++;
                var triggerId = lastTriggerId;
                window.setTimeout(function() {
                    if (triggerId == lastTriggerId) { callback(); }
                }, milliseconds);
            }
        };
    }
};
