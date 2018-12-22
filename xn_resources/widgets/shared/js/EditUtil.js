dojo.provide('xg.shared.EditUtil');

dojo.require('dojo.lfx.html');

xg.shared.EditUtil = {
    /**
     * Displays the form for editing the properties of a module
     * on, for example, the homepage.
     *
     * @param form  the <form> node
     * @param formHeight  the original height of the form, in pixels
     * @param editButton  the Edit <a>
     */
    showModuleForm: function(form, formHeight, editButton) {
        dojo.html.addClass(editButton, 'close');
		form.style.height = "0px";
        dojo.html.show(form);
		dojo.lfx.html.wipeIn(form, 200).play();
    },

    /**
     * Hides the form for editing the properties of a module
     * on, for example, the homepage.
     *
     * @param form  the <form> node
     * @param formHeight  the original height of the form, in pixels
     * @param editButton  the Edit <a>
	 * @param callback    optional callback to call on animation end
     */
    hideModuleForm: function(form, formHeight, editButton, callback) {
        dojo.html.removeClass(editButton, 'close');
		dojo.lfx.html.wipeOut(form, 200, null, function() {
			dojo.html.hide(form);
			if (callback) callback();
		}).play();
	}
}
