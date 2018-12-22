dojo.provide('xg.shared.features');

xg.shared.features = {
    currentLayout: null,
    jsonizeLayout: function() {
        //  Accumulate an object describing the feature layout chosen
        var layout = {};
        layout['iteration'] = dojo.byId('xg_layout').getAttribute('iteration');
        for (var col = 1; col <= 3; col++) {
            if (! dojo.byId('xg_layout_column_' + col)) {
                continue;
            }
            var n = 0;
            layout['col' + col] = new Array();
            dojo.lang.forEach(dojo.html.getElementsByClass('movable', dojo.byId('xg_layout_column_' + col)),
                    function(module) {
                var object = {};
                dojo.lang.forEach(module.attributes, function(attr) {
                    var attrName = (attr.localName ? attr.localName : attr.name);
                    var attrValue = (attr.nodeValue ? attr.nodeValue : dojo.html.getAttribute(module, attrName));
                    if (attrValue && attrName.substr(0, 3) == 'xg_') {
                        object[attrName] = attrValue;
                    }
                });
                layout['col' + col][n++] = object;
            });
        }
        if (dojo.byId('xg_layout_column_sidebar')) {
            n = 0;
            layout['sidebar'] = new Array();
            dojo.lang.forEach(dojo.byId('xg_layout_column_sidebar').getElementsByTagName('li'),
                    function(li) {
                var object = {};
                dojo.lang.forEach(li.attributes, function(attr) {
                    var attrName = (attr.localName ? attr.localName : attr.name);
                    var attrValue = (attr.nodeValue ? attr.nodeValue : dojo.html.getAttribute(li, attrName));
                    if (attrValue && attrName.substr(0, 3) == 'xg_') {
                        object[attrName] = attrValue;
                    }
                });
                layout['sidebar'][n++] = object;
            });
        }
        return dojo.json.serialize(layout);
    }
}

xg.addOnRequire(function() {
    xg.shared.features.currentLayout = xg.shared.features.jsonizeLayout();
});
