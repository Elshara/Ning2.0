dojo.provide('dojo.graphics.color');

dojo.graphics.color.named = {
    white:      [255,255,255],
    black:      [0,0,0],
    red:        [255,0,0],
    green:	    [0,255,0],
    blue:       [0,0,255],
    navy:       [0,0,128],
    gray:       [128,128,128],
    silver:     [192,192,192],
	pink:		[255,192,203],
	magenta:	[255,0,255],
	orange:		[255,165,0],
	yellow:		[255,255,0]
};

dojo.graphics.color.extractRGB = function(color) {
    var hex = "0123456789abcdef";
    color = color.toLowerCase();
    if( color.indexOf("rgb") == 0 ) {
        var matches = color.match(/rgba*\((\d+), *(\d+), *(\d+)/i);
        return [parseInt(matches[1]), parseInt(matches[2]), parseInt(matches[3])];
    } else {
        var colors = dojo.graphics.color.hex2rgb(color);
        if(colors) {
            return colors;
        } else {
            // named color (how many do we support?)
            return dojo.graphics.color.named[color] || [255, 255, 255];
        }
    }
}

dojo.graphics.color.hex2rgb = function(hex) {
    var hexNum = "0123456789ABCDEF";
    var rgb = new Array(3);
    if( hex.indexOf("#") == 0 ) { hex = hex.substring(1); }
    hex = hex.toUpperCase();
    if(hex.replace(new RegExp("["+hexNum+"]", "g"), "") != "") {
        return null;
    }
    if( hex.length == 3 ) {
        rgb[0] = hex.charAt(0) + hex.charAt(0)
        rgb[1] = hex.charAt(1) + hex.charAt(1)
        rgb[2] = hex.charAt(2) + hex.charAt(2);
    } else {
        rgb[0] = hex.substring(0, 2);
        rgb[1] = hex.substring(2, 4);
        rgb[2] = hex.substring(4);
    }
    for(var i = 0; i < rgb.length; i++) {
        rgb[i] = hexNum.indexOf(rgb[i].charAt(0)) * 16 + hexNum.indexOf(rgb[i].charAt(1));
    }
    return rgb;
}
