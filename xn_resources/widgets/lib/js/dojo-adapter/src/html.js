dojo.provide('dojo.dom');
dojo.provide('dojo.html');
dojo.provide('dojo.style');

dojo.style = dojo.dom = dojo.html;

dojo.html.addClass = function(node, classStr) {
    x$(dojo.byId(node)).addClass(classStr);
};

dojo.html.removeClass = function(node, classStr) {
    x$(dojo.byId(node)).removeClass(classStr);
};

dojo.html.createNodesFromText = function(txt) {
    return x$(txt).get();
};

dojo.html.getAttribute = function(node, attr) {
    return x$(dojo.byId(node)).attr(attr);
};

dojo.html.getClass = function(node) {
    return x$(dojo.byId(node)).attr('class');
};

dojo.html.getElementsByClass = function(classStr, parent, nodeType) {
    return x$((nodeType ? nodeType : '')+'.'+classStr, dojo.byId(parent)).get();
};

dojo.html.getElementsByClassName = dojo.html.getElementsByClass;

dojo.html.getFirstAncestorByTag = function(node, tag) {
    return dojo.html.getAncestorsByTag(node, tag, true);
}

dojo.html.getAncestorsByTag = function(node, tag, returnFirstHit) {
    var ancestors = x$(dojo.byId(node)).parents(tag);
    return returnFirstHit ? ancestors[0] : ancestors.get();
}

dojo.html.getAncestors = function(node, filterFunction, returnFirstHit) {
    var ancestors = [];
    var isFunction = dojo.lang.isFunction(filterFunction);
    while(node) {
        if (!isFunction || filterFunction(node)) {
            ancestors.push(node);
        }
        if (returnFirstHit && ancestors.length > 0) { return ancestors[0]; }

        node = node.parentNode;
    }
    if (returnFirstHit) { return null; }
    return ancestors;
}

dojo.html.getViewportHeight = function() {
    return x$(window).height();
}

dojo.html.hasClass = function(node, classname){
    return x$(dojo.byId(node)).hasClass(classname);
}

dojo.html.hide = function(node) {
    node = dojo.byId(node);
    if (node) { x$(node).hide(); }
}

dojo.html.show = function(node) {
    node = dojo.byId(node);
    if (node) { x$(node).show(); }
}

dojo.html.insertAfter = function(node, ref) {
    x$(dojo.byId(ref)).after(node);
}

dojo.html.insertBefore = function(node, ref) {
    x$(dojo.byId(ref)).before(node);
}

dojo.html.isDisplayed = function(node) {
    return x$(dojo.byId(node)).css('display') != 'none';
}

dojo.html.isShowing = function(node) {
    return x$(dojo.byId(node))[0].style.display != 'none';
}

dojo.html.removeNode = function(node) {
    if(node && node.parentNode){ x$(node).remove(); }
}

dojo.html.renderedTextContent = function(node) {
    return x$(dojo.byId(node)).text();
}

dojo.html.selectInputText = function(element) {
    element = dojo.byId(element);
    if(document.selection && document.body.createTextRange){ // IE
        var range = element.createTextRange();
        range.moveStart("character", 0);
        range.moveEnd("character", element.value.length);
        range.select();
    }else if(window["getSelection"]){
        var selection = window.getSelection();
        // FIXME: does this work on Safari?
        element.setSelectionRange(0, element.value.length);
    }
    element.focus();
}

dojo.html.setClass = function(node, classStr) {
    x$(dojo.byId(node)).attr('class', classStr);
}

dojo.html.setOpacity = function setOpacity(node, opacity) {
    x$(dojo.byId(node)).css('opacity', opacity);
}

dojo.html.setShowing = function(node, showing){
    node = dojo.byId(node);
    if (node) { dojo.html[(showing ? 'show' : 'hide')](node); }
}

dojo.html.toggleShowing = function(node) {
    node = dojo.byId(node);
    if (node) { dojo.html.setShowing(node, ! dojo.html.isShowing(node)); }
}

dojo.html.setStyle = function(node, cssSelector, value){
    node = dojo.byId(node);
    if(node && node.style){
        var camelCased = dojo.html.toCamelCase(cssSelector);
        node.style[camelCased] = value;
    }
}

dojo.html.toCamelCase = function(selector) {
    var arr = selector.split('-'), cc = arr[0];
    for(var i = 1; i < arr.length; i++) {
        cc += arr[i].charAt(0).toUpperCase() + arr[i].substring(1);
    }
    return cc;
}

dojo.html.firstElement = function(parentNode, tagName){
    return x$(dojo.byId(parentNode)).children(tagName)[0];
}

dojo.html.insertAtPosition = function(node, ref, position) {
    x$(dojo.byId(ref))[position == 'first' ? 'prepend' : 'append'](node);
}

dojo.html.ELEMENT_NODE = 1;

dojo.html.nextElement = function(node, tagName){
    if(!node) { return null; }
    do {
        node = node.nextSibling;
    } while(node && node.nodeType != dojo.html.ELEMENT_NODE);

    if(node && tagName && tagName.toLowerCase() != node.tagName.toLowerCase()) {
        return dojo.html.nextElement(node, tagName);
    }
    return node;
}

dojo.html.prevElement = function(node, tagName){
    if(!node) { return null; }
    if(tagName) { tagName = tagName.toLowerCase(); }
    do {
        node = node.previousSibling;
    } while(node && node.nodeType != dojo.html.ELEMENT_NODE);

    if(node && tagName && tagName.toLowerCase() != node.tagName.toLowerCase()) {
        return dojo.html.prevElement(node, tagName);
    }
    return node;
}

dojo.html.prependChild = function(node, parent) {
    x$(dojo.byId(parent)).prepend(node);
}

dojo.html.removeChildren = function(node){
    x$(dojo.byId(node)).empty();
}

dojo.html.setDisplay = function(node, display) {
    node = dojo.byId(node);
    if (node) { display ? dojo.html.show(node) : dojo.html.hide(node); }
}

dojo.html.setVisibility = function(node, visibility) {
    node = dojo.byId(node);
    if (node) { x$(node).css('visibility', visibility ? 'visible' : 'hidden'); }
}

dojo.html.getComputedStyle = function(node, cssSelector) {
	if(node = dojo.byId(node)) return x$(node).css(cssSelector);
}

dojo.html.getStyle = dojo.html.getComputedStyle;

dojo.html.getBackgroundColor = function(node) {
    node = dojo.byId(node);
    var color;
    do{
        color = dojo.html.getStyle(node, "background-color");
        // Safari doesn't say "transparent"
        if(color.toLowerCase() == "rgba(0, 0, 0, 0)") { color = "transparent"; }
        if(node == document.getElementsByTagName("body")[0]) { node = null; break; }
        node = node.parentNode;
    }while(node && dojo.lang.inArray(["transparent", ""], color));
    if(color == "transparent"){
        color = [255, 255, 255, 0];
    }else{
        color = dojo.graphics.color.extractRGB(color);
    }
    return color;
}

dojo.html.getAbsoluteX = function(node, includeScroll) {
    return x$(dojo.byId(node)).offset().left;
}

dojo.html.getAbsolutePosition = function(node, includeScroll) {
    var offset = x$(dojo.byId(node)).offset();
    var r = [offset.left, offset.top];
    r.x = r[0];
    r.y = r[1];
    return r;
}

dojo.html.setStyleAttributes = function(node, attributes) {
    var splittedAttribs=attributes.replace(/(;)?\s*$/, "").split(";");
    for(var i=0; i<splittedAttribs.length; i++){
        var nameValue=splittedAttribs[i].split(":");
        var name=nameValue[0].replace(/\s*$/, "").replace(/^\s*/, "").toLowerCase();
        var value=nameValue[1].replace(/\s*$/, "").replace(/^\s*/, "");
        x$(node).css(name, value);
    }
}

dojo.html.getBorderBoxWidth = function(node) {
    return x$(dojo.byId(node)).outerWidth();
}

dojo.html.getBorderBoxHeight = function(node) {
    return x$(dojo.byId(node)).outerHeight();
}

dojo.html.getInnerWidth = dojo.html.getBorderBoxWidth;

dojo.html.getInnerHeight = dojo.html.getBorderBoxHeight;

dojo.html.getPaddingWidth = function(node){
    var x = x$(dojo.byId(node));
    return (parseInt(x.css('padding-left')) || 0) + (parseInt(x.css('padding-right')) || 0);
}

dojo.html.getPaddingHeight = function(node){
    var x = x$(dojo.byId(node));
    return (parseInt(x.css('padding-top')) || 0) + (parseInt(x.css('padding-bottom')) || 0);
}

dojo.html.insertCssText = function(cssStr, doc){
    if(!cssStr){ return; }
    if(!doc){ doc = document; }
    var style = doc.createElement("style");
    style.setAttribute("type", "text/css");
    // IE is b0rken enough to require that we add the element to the doc
    // before changing it's properties
    var head = doc.getElementsByTagName("head")[0];
    if(!head){ // must have a head tag
        dojo.debug("No head tag in document, aborting styles");
        return;
    }else{
        head.appendChild(style);
    }
    if(style.styleSheet){// IE
        style.styleSheet.cssText = cssStr;
    }else{ // w3c
        var cssText = doc.createTextNode(cssStr);
        style.appendChild(cssText);
    }
    return style;
}
