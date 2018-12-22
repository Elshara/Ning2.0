/** $Id: $
 * 	Quick Add Post dialog scripting
 */
dojo.provide('xg.index.quickadd.post');
(function(){
	var qa = xg.index.quickadd;

	xg.index.quickadd.listen('post', 'load', function(){
		var f = dojo.byId('xg_quickadd_post');

    	var validate = function() {
			var errors = {};
	        f['post_title'].value = dojo.string.trim(f['post_title'].value);
			if (dojo.string.trim(f['post_body'].value) == '') {
				errors.post_body = xg.profiles.nls.html('pleaseEnterValueForPost');
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

	xg.index.quickadd.listen('post', 'open', function(){
		qa.resetForm(dojo.byId('xg_quickadd_post'));
	});

})();
