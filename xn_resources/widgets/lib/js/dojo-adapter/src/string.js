dojo.provide('dojo.string');
dojo.string = {
	trim: function(str, wh) {
		if(!str.replace){ return str; }
		if(!str.length){ return str; }
		var re = (wh > 0) ? (/^\s+/) : (wh < 0) ? (/\s+$/) : (/^\s+|\s+$/g);
		return str.replace(re, "");
	},
	escape: function(type, str) {
		if (type != "html") dojo.raise("dojo.string.escape: Type must be html. Use dojo.js instead.");
		return str.replace(/&/gm, "&amp;")
				.replace(/</gm, "&lt;")
				.replace(/>/gm, "&gt;")
				.replace(/"/gm, "&quot;")
				.replace(/'/gm, "&#39;");
	}
};
