dojo.hostenv = {
	/*
	 * 	/xn/loader wrapps code into dojo.hostenv.findModule and sometimes it returns original
	 * 	dojo modules that we don't need. So we use this hack to prevent dojo modules from being
	 * 	evaluated. findModule() isn't used anywhere else except in /xn/loader results.
	 */
	findModule: function(mod) {
		return mod.match(/^dojo\./) || (mod in dojo._modules);
	}
};
(function() {
	var b = x$.browser;
	dojo.render = {html: {
		ie: b.msie,
		ie50: b.msie && b.version == '5.0',
		ie55: b.msie && b.version == '5.5',
		ie60: b.msie && b.version == '6.0',
		ie70: b.msie && b.version == '7.0',
		mozilla: b.mozilla,
		safari: b.safari,
		opera: b.opera
	}};
})();
