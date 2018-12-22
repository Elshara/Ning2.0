/** $Id: $
 * 	Quick Add Photo dialog scripting
 */
dojo.provide('xg.index.quickadd.photo');
(function(){
	var qa = xg.index.quickadd;

	// Files iterator
	var files = function(){
		var i = 0, f = dojo.byId('xg_quickadd_photo');
		return function() {
			for (;i<f.elements.length;i++) {
				if (f.elements[i].type == 'file') {
					return f.elements[i++];
				}
			}
			return undefined;
		}
	};

	xg.index.quickadd.listen('photo', 'load', function(){
		var f = dojo.byId('xg_quickadd_photo');

    	var validate = function() {
			var errors = {}, nempty = 0;
			var it = files(), el;
			while (el = it()) nempty += (el.value != '');
			if(!nempty) errors.x = xg.index.nls.text('pleaseSelectPhotoToUpload');
			return errors;
		}

		xg.listen(f.cancel, 'onclick', qa, qa.cancelDialog);

		var mo = xg.$('a.more_options',f);
		xg.listen(mo, 'onclick', function(evt){
			xg.stop(evt);
			qa.gotoMoreOptions(f, function() { f.setAttribute('action', mo.href); } );
		})

		xg.listen(f, 'onsubmit', function(evt) {
			xg.stop(evt);
			if (!qa.validateForm(f, validate)) {
				return;
			}
			// run simple euristic to check files on early stage
			var it = files(), el, bad = 0;
			while(el = it()) {
				if (el.value != '' && !el.value.match(/\.(jpeg|jpg|jpe|gif|png|bmp)$/i)) {
					bad = 1;
				}
			}
			if (bad && !confirm(xg.index.nls.text('looksLikeNotImage'))) return;

			qa.submitForm({isContent: true, form: f});
		});
	});

	xg.index.quickadd.listen('photo', 'open', function(){
		qa.resetForm(dojo.byId('xg_quickadd_photo'));
	});

})();
