/** $Id: $
 * 	Core functoins of the Quick Add.
 *
 * 	Contains functions used by all quick add dialogs.
 *	Loaded on-demand.
 */
dojo.provide('xg.index.quickadd.core');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.IframeUpload');

(function(){
	var self = xg.index.quickadd,
		hooks = {};

	self.activeDialog = undefined;

	// self._stub and self._dialogs are defined in the .loader module

	/*
	 *	New content that affects the current page was uploaded.
	 *	If set to true, page is reloaded when dialog is closed.
	 *
	 *	Now works only if xg_quickadd_forceReload is set.
	 */
	var newContentUploaded = false;
	/*
	 *	Current form submit creates a new content.
	 *	newContentUploaded rule is applied only for the content forms.
	 */
	var isContent = false;

//** Public interface

	/*
	 * 	Handles default server response. Display a dialog with the status message, "view object" link (optionally) and OK button.
	 * 	Server must return the following data:
	 * 		status = not_approved
	 * 			message		Message to display
	 * 		status = ok
	 * 			viewUrl		URL for the "view" link
	 * 			viewText	Text for the "view" link
	 * 			message		Message to display
	 * 		status = fail
	 * 			message		Error message
     */
	xg.index.quickadd.onDefaultServerResponse = function(content) {
		if ("object" != typeof content) {
			try { content = eval(content) || {} } catch(e) {content = {}}
		}

		var error = (content['status'] != 'ok' && content['status'] != 'not_approved');
		if (error) {
			xg.index.util.FormHelper.showErrorMessages(
				xg.$('form', self._dialogs[self.activeDialog]),
				{x : content.message || xg.index.nls.text('thereWasAProblem')}
			);
			self.showDialog();
			return;
		}

		if (isContent && "undefined" != typeof xg_quickadd_forceReload && xg_quickadd_forceReload) {
			newContentUploaded = true;
		}
		var dlg = xg.shared.util.createElement(
			'<div class="xg_floating_module" style="visibility:hidden">'+
				'<div style="background-image: none;" class="xg_floating_container xg_module">'+
					'<div class="xg_module_head"><h2></h2></div>'+
					'<div class="xg_module_body">'+
						'<p class="msg ' + (content.status == 'ok' ? 'success' : 'notification') + '">' + content.message + '</p>'+
						(content.viewUrl ? '<p class="view_lnk align-right"><a href="' + xg.qh(content.viewUrl) + '">'+content.viewText+'</a></p>' : '')+
						'<p class="buttongroup"><a href="#" class="button">OK</a></p>'+
					'</div>'+
				'</div>'+
			'</div>'
		);
		xg.$('h2', dlg).innerHTML = xg.$('h2',self._dialogs[self.activeDialog]).innerHTML;
		xg.$('a.button',dlg).onclick = function() {
			document.body.removeChild(dlg);
			self.cancelDialog();
			return false;
		}
		document.body.appendChild(dlg);
		xg.shared.util.fixDialogPosition(dlg);
		dlg.firstChild.style.visibility = 'visible';
	}

	/**
	 *  Adds a dialog event listener
	 *
	 *  @param  dlg  	string		Dialog name (video, blog, etc)
	 *  @param	evt		string		Event name: load, open
	 *  @param	cb		callback	Callback
	 */
	xg.index.quickadd.listen = function(dlg, evt, cb) {
		if (!hooks[dlg]) hooks[dlg] = {};
		if (!hooks[dlg][evt]) hooks[dlg][evt] = [];
		hooks[dlg][evt].push(cb);
	}

	/**
	 *  Fires a dialog event
	 *
	 *  @param  dlg  	string		Dialog name (video, blog, etc)
	 *  @param	evt		string		Event name: load, open
	 */
	xg.index.quickadd.fire = function(dlg, evt) {
		if (!hooks[dlg] || !hooks[dlg][evt]) {
			return;
		}
		for (var i = 0, a = hooks[dlg][evt]; i<a.length; i++) {
			a[i]();
		}
	}

	/**
	 *  Hides all open dialogs and hides the overlay.
	 */
	xg.index.quickadd.openDialog = function(v) {
		dojo.html.hide(self._stub);
		xg.shared.util.showOverlay();

		self.activeDialog = v;

		newContentUploaded = false; // reset the flag

		self.fire(self.activeDialog, 'open');

		self.showDialog(true);
	}
	/**
	 *  Hides all open dialogs and hides the overlay.
	 */
	xg.index.quickadd.cancelDialog = function() {
		self.hideProgress();
		self.hideDialog();
		xg.shared.util.hideOverlay();
		if (newContentUploaded) {
			window.location.reload(true);
		}
		self.activeDialog = undefined;
	}

	/**
	 *  Displays the required dialog or current active dialog
	 */
	xg.index.quickadd.showDialog = function(animation) {
		var dlg = self._dialogs[self.activeDialog];

		dlg.style.visibility = 'hidden';
		dlg.style.display = '';

		xg.shared.util.fixDialogPosition(dlg);

		if (animation) {
			var container = dojo.dom.firstElement(dlg, 'div');
			dojo.style.setOpacity(container, 0);
			dlg.style.visibility = 'visible';
			var anim = dojo.lfx.html.fadeIn(container, 250);
			anim.onEnd = function () {
				var f = xg.$('form',dlg);
				if (!f) return;
				for(var i = 0; i<f.elements.length;i++) {
					if (f.elements[i].tagName != 'FIELDSET' && (!f.elements[i].type || f.elements[i].type!='hidden')) {
						f.elements[i].focus();
						break;
					}
				}
			};
			anim.play();
		} else {
			dlg.style.visibility = 'visible';
		}
	}

	/**
	 *  Hides the current dialog and leaves the overlay open
	 */
	xg.index.quickadd.hideDialog = function() {
		dojo.html.hide(self._dialogs[self.activeDialog]);
	}

	/**
	 *  Displays the "progress" dialog. onCancel specifies the handler for "cancel" button
	 */
	var spinner_fixed = 0;
	xg.index.quickadd.showProgress = function(title, text, onCancel) {
		var p = dojo.byId('xg_quickadd_spinner');
		if (!spinner_fixed) {
			var mb = xg.$('div.xg_module_body', p);
			mb.innerHTML = "" + mb.getAttribute('_spinner') + mb.innerHTML;
			// Fix spinner by moving it out of the #xg.
			p.parentNode.removeChild(p);
			p = document.body.appendChild(p);
			spinner_fixed = 1;
		}
		xg.$('h2',p).innerHTML = title;
		xg.$('.spinner_msg',p).innerHTML = text;
		xg.$('a',p).onclick = function() {
			onCancel();
			return false;
		};
		p.style.visibility = 'hidden';
		p.style.display = '';
		xg.shared.util.fixDialogPosition(p);
		p.style.visibility = 'visible';
	}

	/**
	 *  Hides the "progress" dialog
	 */
	xg.index.quickadd.hideProgress = function() {
		dojo.html.hide(dojo.byId('xg_quickadd_spinner'));
	}

	xg.index.quickadd.gotoMoreOptions = function (form, cb) {
		var nempty = 0;
		for (var els = form.elements, i = 0; i<els.length; i++) {
			if (els[i].type && els[i].type == 'file' && els[i].value != '') {
				nempty++;
			}
		}
		if (nempty && !confirm(xg.index.nls.text('cannotKeepFiles'))) {
			return;
		}
		if (cb) { cb() }
		form.setAttribute('target','');
		form.submit();
	}

	/**
	 *  Resets form.
     */
	xg.index.quickadd.resetForm = function (form) {
		xg.index.util.FormHelper.hideErrorMessages(form);
		form.reset();
    }

	/**
	 *  Validates form.
     */
	xg.index.quickadd.validateForm = function (form, validate) {
		if (xg.index.util.FormHelper.runValidation(form, validate)) {
			return true;
		}
		// Fix the dialog position because of the error messages.
		xg.shared.util.fixDialogPosition(xg.parent(form, 'div.xg_floating_module'));
		return false;
    }

	/**
	 *  Submits dialog's form. Conf:
	 *  	form
	 *  	title		progress dialog title (has default)
	 *  	text		progress dialog text (has default)
	 *  	cancel		callback on submit cancel. default is to cancel the dialog.
	 *  	success		callback on submit success. receives response text as a parameter.
	 *  				see onDefaultServerResponse for details.
	 *  	isContent	the form creates a new content. used to refresh the page when necessary.
	 *
     *  @return     void
     */
	xg.index.quickadd.submitForm = function(conf) {
		setTimeout(function(){ // setTimeout should prevent spinner freezing
			isContent = conf.isContent || false;

			xg.shared.IframeUpload.start(conf.form, function(response) {
				self.hideProgress();
				(conf.success || xg.index.quickadd.onDefaultServerResponse)(response);
			});

			self.hideDialog();
			self.showProgress(
				conf.title || xg.uploader.nls.text('uploadingLabel'),
				conf.text || xg.uploader.nls.text('uploadingInstructions'),
				function () {
					xg.shared.IframeUpload.stop();
					self.hideProgress();
					(conf.cancel||self.cancelDialog)();
				}
			);
		});
	}
	// The same as submitForm() but uses XHR as a transport (instead of iframe). Implies no input type=file fields.
	xg.index.quickadd.submitFormXhr = function(conf) {
		setTimeout(function(){ // setTimeout should prevent spinner freezing
			var http;
			isContent = conf.isContent || false;
			self.hideDialog();
			self.showProgress(
				conf.title || xg.uploader.nls.text('uploadingLabel'),
				conf.text || xg.index.nls.text('addingInstructions'), // todo: should this be uploadingInstructions as per submitForm?
				function () {
					http.abort();
					self.hideProgress();
					(conf.cancel||self.cancelDialog)();
				}
			);
			http = xg.post('', conf.form, function(r,d){
				self.hideProgress();
				(conf.success || xg.index.quickadd.onDefaultServerResponse)(d);
			});
		});
	}
})();
