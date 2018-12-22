dojo.provide('xg.index.mediauploader.container');

/**
 * Behavior for the Media Uploader pages.
 */
(function () {
    if (PluginDetect.isMinVersion('Java', '1.5') >= 0) {
        window.setInnerHtmlFromExternalScript(dojo.byId('uploader_container'), dojo.byId('uploader_html').value);
		// Hack to prevent jQuery from "unbinding" <object>'s and document objects. BAZ-9970 [Andrey 2008-09-17]
		x$(window).unbind().bind("unload",function(){ x$("*").not('object').unbind() });
	} else {
        dojo.style.hide(dojo.byId('uploader_container'));
        dojo.style.hide(dojo.byId('accepted_formats_message'));
        dojo.style.hide(dojo.byId('help_message'));
        dojo.style.show(dojo.byId('java_required_message'));
    }
})();
