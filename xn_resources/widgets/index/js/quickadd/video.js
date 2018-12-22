/** $Id: $
 * 	Quick Add Video dialog scripting
 */
dojo.provide('xg.index.quickadd.video');
(function(){
	var qa = xg.index.quickadd;

    xg.index.quickadd.listen('video', 'load', function(){
		var f = dojo.byId('xg_quickadd_video');

        var validate = function() {
            var errors = {};
            if (!f.file.value && !f.embedCode.value) {
                errors.x = xg.index.nls.text('selectOrPaste');
            }
            return errors;
        }

        var disable = function (evt){
            var src = evt.srcElement || evt.target;
            (src == f.file ? f.embedCode : f.file).disabled = !src.value.match(/^\s*$/);
        }

        xg.listen(f.cancel, 'onclick', qa, qa.cancelDialog);

		var mo = xg.$('a.more_options',f);
		xg.listen(mo, 'onclick', function(evt){
			xg.stop(evt);
			qa.gotoMoreOptions(f, function() { f.setAttribute('action', f.file.disabled ? mo.getAttribute('lnkEmbed') : mo.href); } );
		})

        dojo.lang.forEach([f.file, f.embedCode], function(el) {
            dojo.lang.forEach(['onkeyup', 'onclick', 'onkeypress', 'onblur', 'oncut', 'onpaste', 'onchange'], function(evt) {
                xg.listen(el, evt, disable);
            });
        });

        xg.listen(f, 'onsubmit', function(evt) {
            xg.stop(evt);
			if (!qa.validateForm(f, validate)) {
				return;
			}
			if (f.file.value && !f.file.value.match(/\.(mov|mpe?g|mp4|avi|3gp|wmv)$/i) && !confirm(xg.index.nls.text('looksLikeNotVideo'))) {
				return;
			}
            qa.submitForm({isContent: true, form: f});
        });
    });

    xg.index.quickadd.listen('video', 'open', function(){
        var f = dojo.byId('xg_quickadd_video');
		qa.resetForm(f);
        f.file.disabled = false;
        f.embedCode.disabled = false;
    });

})();
