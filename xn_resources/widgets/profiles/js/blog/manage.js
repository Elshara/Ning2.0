dojo.provide('xg.profiles.blog.manage');

xg.addOnRequire(function() {
    var checkbox = dojo.byId('checkbox-top');
    if (checkbox) {
        dojo.event.connect(checkbox, 'onclick',function() {
            // Find the table that contains checkbox -- it will also contain
            // all the other checkboxes
            var tbl = dojo.dom.getFirstAncestorByTag(checkbox, 'table');
            dojo.lang.forEach(dojo.html.getElementsByClass('checkbox',tbl), function(el) {
                el.checked = checkbox.checked;
            }, true);
        });
    }
});

