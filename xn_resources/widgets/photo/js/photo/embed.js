dojo.provide('xg.photo.photo.embed');

(function() {
    // Prevent "Click to activate and use this control" message on IE [Jon Aquino 2006-11-25]
    if (dojo.render.html.ie) {
        // it is better to wait for the page loaded to do this dom intervention (see BAZ-370) [Fabricio Zuardi 2006-12-07]
        xg.addOnRequire(function() {
            objects = dojo.html.getElementsByClass('xg_slideshow');
            for (var i = 0; i < objects.length; i++) {
                objects[i].outerHTML = objects[i].outerHTML;
            }
        });
    }
}());

