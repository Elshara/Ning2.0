/**
 * 	dojo.xml.Parse.
 */
delete dojo.xml;
dojo.provide("dojo.xml.Parse");

dojo.xml.Parse = function() {};
dojo.xml.Parse.prototype.parseElement = function(root) {
    var els = root.getElementsByTagName('*'), res = [], e;
    var isIE = x$.browser.msie;
    for (var i = -1, j = 0, l = els.length; i<l; i++) {
        e = i == -1 ? root : els[i];
        if (e.getAttribute('dojoType')) {
            var attrs = e.attributes, v = {nodeRef:e};
            for (var k = 0, attr, al = attrs.length; k<al; k++) {
                attr = attrs[k];
                if ( isIE && ( attr.nodeValue === null || attr.nodeValue == '' ) ) {
                    continue;
                }
                v[attr.nodeName.toLowerCase()] = attr.nodeValue;
            }
            res[j++] = v;
        }
    }
    return res;
};

/**
 *	dojo.widget.*
 */
dojo.widget = {
    _widgetTypes: {},
    getParser: function() {
        return dojo.widget
    },
    createComponents: function(wNodes) {
        for (var i = 0;i<wNodes.length;i++) {
            var n = wNodes[i], nodeRef = n.nodeRef;
            delete n.nodeRef;
            dojo.widget._createWidget(n.dojotype, n, nodeRef);
        }
    },
    createWidget: function(widgetName, props, node) {
        if (arguments.length > 3) {
            dojo.raise("dojo.widget.createWidget(widgetName, props, node) supports only 3 args. Consider using the original dojo.js");
        }

        props = props || {};
        props.fastMixIn = true;
        var newNode = 0;
        if (!node) {
            newNode = 1;
            node = document.body.appendChild(document.createElement('span'));
        }
        var w = dojo.widget._createWidget(widgetName, props, node);
        if (newNode && w.domNode.parentNode) {
            w.domNode.parentNode.removeChild(w.domNode);
        }
        return w;
    },
    defineWidget: function(widgetClass /*string*/, superclass /*function||array*/, props /*object*/) {
        if (arguments.length > 3) {
            dojo.raise("dojo.widget.defineWidget(name, base, props) supports only 3 args. Consider using the original dojo.js");
        }
        var type = (widgetClass.match(/\.(\w+)$/) ? RegExp.$1 : widgetClass).toLowerCase();
        dojo.widget._widgetTypes[type] = widgetClass;
        props= props || {};
        props.widgetType = type;
        dojo._setObj(widgetClass, dojo.declare(superclass, props));
    },
    _createWidget: function(widgetName, props, node) {
		var pkg = dojo.widget._widgetTypes[widgetName.toLowerCase()];
		if (!pkg) {
			return;
		}
		if ( !(pkg = dojo._getObj(pkg)) ) {
			dojo.raise("cannot find \"" + widgetName + "\" widget");
		}
		var w = new pkg;
		if (!w.create) {
			dojo.raise("\"" + widgetName + "\" widget object does not appear to implement *Widget");
		}
		return w.create(props, node);
    }
};

(function() {
    var dw = dojo.widget, dwm;

    dojo.provide("dojo.widget.Manager");

    dojo.widget.manager = {
        _widgetIdCnt: 0,
        _widgets: [],

        add: function(widget) {
            if (widget.widgetId == "") {
                widget.widgetId = widget["id"] || widget.extraArgs["id"] || "_djw_autoid_"+(dwm._widgetIdCnt++);
            }
            dwm._widgets.push(widget);
        },

        destroyAll: function() {
            for (var i=0, w; w = dwm._widgets[i]; i++){
                try { w.destroy(true) }catch(e){ }
            }
            dwm._widgets = [];
        },

        removeById: function(id) {
            for (var i=0, w; w = dwm._widgets[i]; i++){
                if (w.widgetId == id) {
                    try { w.destroy(true) }catch(e){ }
                    dwm._widgets[i].splice(i,1);
                    break;
                }
            }
        },

        getWidgetById: function(id) {
            for (var i=0, w; w = dwm._widgets[i]; i++){
                if (w.widgetId == id) return w;
            }
            return undefined;
        },

        getWidgetsByType: function(type) {
            type = type.toLowerCase();
            var ret = [];
            for (var i=0, w; w = dwm._widgets[i]; i++) {
                if (w.widgetType.toLowerCase() == type) ret.push(w);
            }
            return ret;
        },

        getWidgetByNode: function(/* DOMNode */ node) {
            for (var i=0, w; w = dwm._widgets[i]; i++) {
                if (w.domNode==node) return w;
            }
            return undefined;
        }
    };

    dwm = dw.manager;
    dwm.byNode = dwm.getWidgetByNode;

    dojo.provide("dojo.widget.Widget");
    dojo.provide("dojo.widget.HtmlWidget");

    var lcArgsCache = {}

    dojo.widget.Widget = dojo.declare(null, {
        extraArgs: undefined,
        widgetId: "",
        widgetType: "Widget", // used for building generic widgets
        domNode: null,

        create: function(args, fragment){
            this.extraArgs = {};
            this.mixInProperties(args, fragment);
            this.postMixInProperties(args, fragment);
            this.domNode = this.getFragNodeRef(fragment);
            dojo.widget.manager.add(this);
            this.buildRendering(args, fragment);
            this.fillInTemplate(args, fragment);
            this.postCreate(args, fragment);
            return this;
        },

        getFragNodeRef: function(frag) {
            return frag;
        },

        destroy: function(finalize) {
            try {
                for(var i in this) {
                    if ("object" == typeof this[i]) delete this[i];
                }
            } catch(e){ }
        },

        mixInProperties: function(args) {
            if (args["fastMixIn"]) {
                for(var x in args) {
                    this[x] = args[x];
                }
                return;
            }

            var lcArgs = lcArgsCache[this.widgetType];
            if ( !lcArgs ) { // build a lower-case property name cache if we don't have one
                lcArgs = {}
                for(var y in this){
                    lcArgs[y.toLowerCase()] = y;
                }
                lcArgsCache[this.widgetType] = lcArgs;
            }
            for (var i in args) {
                var j = lcArgs[i];
                if( (typeof this[j]) == "undefined" ) {
                    this.extraArgs[i] = args[i];
                    continue;
                }
                switch (typeof this[j]) {
                    case 'string': this[j] = "" + args[i]; break;
                    case 'number': this[j] = parseFloat(args[i]); break;
                    case 'boolean': this[j] = (args[i].toLowerCase()=="false") ? false : true; break;
                    case 'function': this[j] = new Function(args[i]); break;
                    default: this[j] = args[i]; break;
                }
            }
        },

        postMixInProperties: function() {},
        buildRendering: function() {
            if (!this.templateString) {
                return;
            }
            var _this = this, el = document.createElement('div');
            // Replace ${var} strings
            el.innerHTML = this.templateString.replace(/\$\{this\.([\w.]+)\}/g, function($0,$1) { return dojo._getObj($1,_this) });

            // Copy to domNode
            this.domNode.innerHTML = '';
            for (var i = 0; i<el.childNodes.length; i++) {
                this.domNode.appendChild(el.childNodes[i]);
            }

            // Init dojoAttachPoint, dojoAttachEvent attrs
            var nodes = this.domNode.getElementsByTagName('*'), n, a, e;
            for (var i = 0; n = nodes[i]; i++) {
                if ( (a = n.getAttribute('dojoAttachPoint')) && (a in this) ) {
                    this[a] = n;
                }
                if ( (e = n.getAttribute('dojoAttachEvent')) && e.match(/^(\w+):(\w+)$/)) {
                    dojo.event.connect(n, RegExp.$1.toLowerCase(), dojo.lang.hitch(this, this[RegExp.$2]));
                }
            }
        },
        fillInTemplate: function(args, frag) {},
        postCreate: function(args, frag) {}
    });

    dojo.widget.HtmlWidget = dojo.widget.Widget;
})();