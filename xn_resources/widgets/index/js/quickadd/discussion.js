/** $Id: $
 * 	Quick Add Discussion dialog scripting
 */
dojo.provide('xg.index.quickadd.discussion');
(function(){
	var qa = xg.index.quickadd;

	xg.index.quickadd.listen('discussion', 'load', function(){
		var f = dojo.byId('xg_quickadd_discussion');

    	var validate = function() {
			var errors = {};
			if (dojo.string.trim(f.title.value).length == 0) {
				errors.title = xg.forum.nls.html('pleaseEnterTitle');
			}
			if (dojo.string.trim(f.description.value).length > f.description.getAttribute('_maxlength')) {
				errors.description = xg.forum.nls.html('numberOfCharactersExceedsMaximum', dojo.string.trim(f.description.value).length, f.description.getAttribute('_maxlength'));
			}
			if (dojo.string.trim(f.description.value).length == 0) {
				errors.description = xg.forum.nls.html('pleaseEnterFirstPost');
			}
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
			qa.submitFormXhr({isContent: true, form: f, title: xg.index.nls.text('addingLabel')});
		});
	});

	xg.index.quickadd.listen('discussion', 'open', function(){
		qa.resetForm(dojo.byId('xg_quickadd_discussion'));
	});

})();
