dojo.provide('dojo.lang');

dojo.lang.extend = function(ctor /*function*/, props){
    x$.extend(ctor.prototype, props);
}

dojo.lang.filter = function(arr, callback) {
    return x$.grep(arr, callback);
}

dojo.lang.forEach = function(anArray /* Array */, callback /* Function */) {
    x$.map(anArray, callback);
}

dojo.lang.hitch = function(thisObject, method) {
    return function() { return ("function" == typeof method ? method : thisObject[method]).apply(thisObject, arguments); }
}

dojo.lang.inArray = function(arr /*Array*/, val /*Object*/) {
    var length = arr.length;
    for (var i = 0; i < length; i++) {
        if (arr[i] === val) { return true; }
    }
    return false;
}

dojo.lang.isEmpty = function(obj) {
    var tmp = {};
    for(var x in obj){
        if(obj[x] && !tmp[x]) { return false; }
    }
    return true;
}

dojo.lang.isString = function(wh){
    return (wh instanceof String || typeof wh == "string");
}

dojo.lang.isFunction = function(wh){
    if(!wh){ return false; }
    return (wh instanceof Function || typeof wh == "function");
}

dojo.lang.isArray = function(wh){
    return (wh instanceof Array || typeof wh == "array");
}

dojo.lang.map = function(arr, unary_func) {
    return x$.map(arr, unary_func);
}

dojo.lang.mixin = function(obj, props1, props2) {
    for (var i = 1; i < arguments.length; i++) {
        x$.extend(obj, arguments[i]);
    }
    return obj;
}
