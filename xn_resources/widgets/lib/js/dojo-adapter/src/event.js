dojo.provide('dojo.event');
dojo.provide('dojo.event.browser');
(function() {
    var bind = function(obj, evt, cb1, cb2) {
        var cb = cb2 ? ("string" == typeof cb2 ? function() {cb1[cb2].apply(cb1,arguments)} : function() {cb2.apply(cb1,arguments)} ) : cb1;
        if (obj.addEventListener) {
            obj.addEventListener(evt.substr(2), cb, false);
        } else {
            obj.attachEvent(evt, function() { var e = window.event; e.target = e.srcElement; cb(e) });
        }
        // we can try to use obj[evt] = callback, but I'm not sure that there are still browsers that require it.
    };
    dojo.event = {
        connect: function(obj, evt, cb1, cb2) {
            if (arguments.length != 3 && arguments.length != 4) {
                dojo.raise("dojo.event.connect: Only 3 argument syntax is supported. Use dojo.js instead.");
            }
            if (evt.substr(0,2) != "on") {
                dojo.raise("dojo.event.connect: Only DOM events are supported. Use dojo.js instead.");
            }
            if ("object" != typeof obj && !obj.nodeType) {
                dojo.raise("dojo.event.connect: Only objects are supported. Use dojo.js instead.");
            }
            if (obj instanceof Array) {
                for (var i = 0; i<obj.length; i++) {
                    bind(obj[i], evt, cb1, cb2);
                }
			} else {
                bind(obj, evt, cb1, cb2);
            }
        },
        browser: {
            stopEvent: function(evt) {
                if (evt.stopPropagation) evt.stopPropagation();
                if (evt.preventDefault) evt.preventDefault();
                evt.cancelBubble = true;
                evt.returnValue = false;
            }
        }
    }
})();