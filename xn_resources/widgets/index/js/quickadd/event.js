/** $Id: $
 * 	Quick Add Event dialog scripting
 */
dojo.provide('xg.index.quickadd.event');
dojo.require('xg.shared.BazelImagePicker'); /** @explicit for quickadd/event.php */
(function(){
    var qa = xg.index.quickadd;

    xg.index.quickadd.listen('event', 'load', function(){
        var f = dojo.byId('xg_quickadd_event');

        var validate = function() {
            var errors = {};
            if (f.photo.value == '') {
                errors.photo = xg.events.nls.html('pleaseChooseImage');
            }
            if (dojo.string.trim(f.title.value).length == 0) {
                errors.title = xg.events.nls.html('pleaseEnterTitle');
            }
            if (dojo.string.trim(f.description.value).length == 0) {
                errors.description = xg.events.nls.html('pleaseEnterDescription');
            }
            if (dojo.string.trim(f.type.value).length == 0) {
                errors.type = xg.events.nls.html('pleaseEnterType');
            }
            if (dojo.string.trim(f.location.value).length == 0) {
                errors.location = xg.events.nls.html('pleaseEnterLocation');
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
            qa.submitForm({isContent: true, form: f, title: xg.index.nls.text('addingLabel')});
        });
    });

    xg.index.quickadd.listen('event', 'open', function(){
        qa.resetForm(dojo.byId('xg_quickadd_event'));
        var picker = dojo.byId('xg_quickadd_event_img').nextSibling;
        dojo.widget.manager.byNode(picker).clearImage();
    });

})();
