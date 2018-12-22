dojo.provide('dojo.json');
dojo.json = {
	evalJson: function(/* jsonString */ json){
		return x$.evalJSON(json);
	},
	serialize: function(o){
		return x$.toJSON(o);
	}
}
