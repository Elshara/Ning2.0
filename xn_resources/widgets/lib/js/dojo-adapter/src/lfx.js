dojo.provide('dojo.lfx.html');
dojo.provide('dojo.lfx.Animation');
(function() {
    var dj_anim = function(f) {
        return function() {
            var args = [], anim = {};
            for(var i = 0;i<arguments.length;i++) args[i] = arguments[i];
            anim.play = function() { f.apply(anim, args) }
            return anim;
        }
    }
    var rgb = function(c) {
        var r = parseInt(c[0]), g = parseInt(c[1]), b = parseInt(c[2]);
        return "rgb(" +
            (r < 0 ? 0 : (r > 255 ? 255 : r)) + ","+
            (g < 0 ? 0 : (g > 255 ? 255 : g)) + ","+
            (b < 0 ? 0 : (b > 255 ? 255 : b)) + ")";
    }
    dojo.lfx.html = {
        wipeIn: dj_anim(function(nodes, duration, easing, callback) {
            x$(nodes).hide().css('height','auto').slideDown(duration, this.onEnd||callback);
        }),
        wipeOut: dj_anim(function(nodes, duration, easing, callback) {
            var self = this, n = x$(nodes);
            n.show().slideUp(duration, function() {
                n.css('height','0px');
                (self.onEnd||callback||function(){})();
            });
        }),
        fadeIn: dj_anim(function(nodes, duration, easing, callback) {
            x$(nodes).hide().css('opacity',1).fadeIn(duration, dojo.lang.hitch(this, function() {
                dojo.lang.forEach(x$(nodes).get(), function(node) {
                    dojo.lfx.html._clearOpacityInIe(nodes); // BAZ-9298 [Jon Aquino 2008-08-28]
                });
                (this.onEnd||callback||function(){})();
            }));
        }),
        fadeOut: dj_anim(function(nodes, duration, easing, callback) {
            x$(nodes).show().fadeOut(duration, this.onEnd||callback);
        }),
        _clearOpacityInIe: function clearOpacity(node) {
            node = dojo.byId(node);
            var ns = node.style;
            if(dojo.render.html.ie){
                try {
                    if( node.filters && node.filters.alpha ){
                        ns.filter = ""; // FIXME: may get rid of other filter effects
                    }
                } catch(e) {
                    /*
                     * IE7 gives error if node.filters not set;
                     * don't know why or how to workaround (other than this)
                     */
                }
            }
        },
        highlight: dj_anim(function(nodes, startColor, duration, easing, callback) {
            if (!x$.fx.step['backgroundColor']) { // make sure we have backgroundColor handler
                x$.fx.step['backgroundColor'] = function(fx) {
                    if ( fx.state == 0 ) {
                        fx.start = dojo.graphics.color.extractRGB(x$.curCSS(fx.elem,'backgroundColor'));
                        fx.end = dojo.graphics.color.extractRGB(fx.end);
                    }
                    fx.elem.style['backgroundColor'] = rgb([
                        (fx.pos * (fx.end[0] - fx.start[0])) + fx.start[0],
                        (fx.pos * (fx.end[1] - fx.start[1])) + fx.start[1],
                        (fx.pos * (fx.end[2] - fx.start[2])) + fx.start[2]
                    ]);
                }
            }

            if ("string" != typeof startColor) {
                startColor = rgb(startColor);
            }

            x$(nodes).each(function(i,node){
                var bg = dojo.style.getStyle(node, "background-color").toLowerCase();
                var bgImage = dojo.style.getStyle(node, "background-image");
                var wasTransparent = (bg == "transparent" || bg == "rgba(0, 0, 0, 0)");

                if (bgImage) node.style.backgroundImage = "none";
                node.style.backgroundColor = startColor;

                x$(node).animate({ backgroundColor: bg }, duration, function() {

                    if (bgImage) node.style.backgroundImage = bgImage;
                    if (wasTransparent) node.style.backgroundColor = "transparent";

                    (self.onEnd||callback||function(){})();
                })
            });
        })
    };
    for (var i in dojo.lfx.html) {
        dojo.lfx[i] = dojo.lfx.html[i];
    }
    dojo.lfx.easeIn = null;
})();
