/** $Id: $
 * 	Quick Add Notes dialog scripting
 */
dojo.provide('xg.index.quickadd.note');
(function(){
	var qa = xg.index.quickadd;

	xg.index.quickadd.listen('note', 'load', function(){
		var f = dojo.byId('xg_quickadd_note');

    	var validate = function() {
			var errors = {};
			if (dojo.string.trim(f.noteKey.value) == '') {
				errors.noteKey = xg.notes.nls.text('pleaseEnterNoteTitle');
			}
			if (dojo.string.trim(f.content.value) == '') {
				errors.content = xg.notes.nls.text('pleaseEnterNoteEntry');
			}
			return errors;
		}

		xg.listen(f.cancel, 'onclick', qa, qa.cancelDialog);

		var mo = xg.$('a.more_options',f);
		xg.listen(mo, 'onclick', function(evt){
			xg.stop(evt);
			if (dojo.string.trim(f.noteKey.value) == '') {
				xg.index.util.FormHelper.showErrorMessages(f, {noteKey: xg.notes.nls.text('pleaseEnterNoteTitle')});
				return;
			}
			qa.gotoMoreOptions(f, function() { f.setAttribute('action', mo.href); } );
		})

		xg.listen(f, 'onsubmit', function(evt) {
			xg.stop(evt);
			if (!qa.validateForm(f, validate)) {
				return;
			}
			qa.submitFormXhr({isContent: true, form: f, title: xg.index.nls.text('addingLabel')});
		});
	});

	xg.index.quickadd.listen('note', 'open', function(){
		qa.resetForm(dojo.byId('xg_quickadd_note'));
	});

})();
