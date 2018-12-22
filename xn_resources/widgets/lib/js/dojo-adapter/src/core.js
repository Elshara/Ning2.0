dojo = {};
dj_eval = function(d) { return eval(d) }
dojo.raise = function(msg) {
    throw msg;
}
dojo._modules = {};
dojo.provide = function(module) {
    dojo._modules[module] = 1;
    if (!dojo._getObj(module)) {
        dojo._setObj(module,{});
    }
}
dojo.require = function(module) {
    if (!dojo._getObj(module)) {
        dojo.raise("Module "+module+" is not defined.");
    }
}
dojo._modulePrefixes = {};
dojo.setModulePrefix = function(prefix, path) {
    dojo._modulePrefixes[prefix] = path;
}

dojo.byId = function(id) {
    if(id && (typeof id == "string" || id instanceof String)){
        return document.getElementById(id);
    }
    return id; // assume it's a node
};

(function() {
    var funcs = [], run = function() {
        for (var i = 0; i<funcs.length; i++) {
            funcs[i]();
        }
        funcs = [];
        dojo.addOnLoad = function(func) { func() }
    }
    dojo.addOnLoad = function(func) { funcs.push(func) }
    if (window.addEventListener) {
        window.addEventListener("load", run, false);
    } else if (window.attachEvent) {
        window.attachEvent("onload", run);
    } else {
        window.onload = run;
    }
})();

dojo.addOnUnload = function(onUnload) { x$(window).unload(onUnload); }

dojo._setObj = function (name, value, context) {
    context = context || window;
    name = name.split('.');
    var n;
    for(var i = 0, l = name.length - 1; i<l; i++) {
        n = name[i];
        if ( !(n in context) )
            context[n] = {};
        context = context[n];
    }
    n = name[i];
    return n !== '' ? context[n] = value : undefined;
}

dojo._getObj = function (name, context) {
    context = context || window;
    name = name.split('.');
    for(var i = 0, n, l = name.length; i<l; i++) {
        n = name[i];
        if ( !(n in context) ) {
            return undefined;
        }
        context = context[n];
    }
    return context;
}

dojo.evalObjPath = function (path, create) {
    return dojo._getObj(path) || (create ? dojo._setObj(path,{}) : undefined);
}

dojo.provide("dojo.lang.declare");
dojo.declare = function (superClass, props) {
    var cls = function () { };
    if (superClass) {
        cls.prototype = new superClass();
        for (var i in props) {
            cls.prototype[i] = props[i];
        }
    } else {
        cls.prototype = props;
    }
    return cls;
}
