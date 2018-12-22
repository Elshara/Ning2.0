/** $Id: $
 * 	Quick Add Music dialog scripting
 */
dojo.provide('xg.index.quickadd.music');
(function(){
	var qa = xg.index.quickadd;

    xg.index.quickadd.listen('music', 'load', function(){
		var f = dojo.byId('xg_quickadd_music');

        var validate = function () {
            var errors = {};
			if (!f.track_01.value && !f.track_02.value) {
                errors.x = xg.index.nls.text('selectOrPasteMusic');
            }
            return errors;
        }

        var disable = function (evt){
            var src = evt.srcElement || evt.target;
			(src == f.track_01 ? f.track_02 : f.track_01).disabled = !src.value.match(/^\s*$/);
        }

        xg.listen(f.cancel, 'onclick', qa, qa.cancelDialog);

		var mo = xg.$('a.more_options',f);
		xg.listen(mo, 'onclick', function (evt){
			xg.stop(evt);
			qa.gotoMoreOptions(f, function() {
				if (!f.track_01.disabled) {
					f.setAttribute('action', mo.href);
					dojo.html.removeNode(f.track_02);
				} else {
					var e = f.track_02;
					f.setAttribute('action', mo.getAttribute('lnkEmbed'));
					dojo.html.removeNode(f.track_01);
					e.name = "track_01";
					e.setAttribute('name', 'track_01');
				}
			});
		})

		dojo.lang.forEach([f.track_01, f.track_02], function (el) {
            dojo.lang.forEach(['onkeyup', 'onclick', 'onkeypress', 'onblur', 'oncut', 'onpaste', 'onchange'], function(evt) {
                xg.listen(el, evt, disable);
            });
        });

        xg.listen(f, 'onsubmit', function (evt) {
            xg.stop(evt);
			if (!qa.validateForm(f, validate)) {
				return;
			}
            f.linkMode.value = f.track_01.value == '' ? 1 : 0;
			if (f.track_01.value && !f.track_01.value.match(/\.(mp3)$/i) && !confirm(xg.index.nls.text('looksLikeNotMusic'))) {
				return;
			}
			qa.submitForm({isContent: true, form: f});
        });
    });

    xg.index.quickadd.listen('music', 'open', function(){
        var f = dojo.byId('xg_quickadd_music');
		qa.resetForm(f);
        f.track_01.disabled = false;
		f.track_02.disabled = false;
    });

})();
