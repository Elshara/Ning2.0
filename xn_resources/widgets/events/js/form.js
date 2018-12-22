dojo.provide('xg.events.form');

dojo.require('xg.shared.util');

(function() {
    var form = dojo.byId('event_form');
    xg.shared.util.setMaxLength(form.description, parseInt(form.description.getAttribute('_maxlength')));

    event_showEndTime = function (){
        dojo.style.hide('addEndTime');
        dojo.style.show('removeEndTime');
        form.hideEnd.value = 0;
        // init to the same value
        var els = form.elements;
        for(var i = 0;i<els.length;i++) {
            if (els[i].name && els[i].name.match(/^start([A-Z])$/)) {
                form.elements["end"+RegExp.$1].value = els[i].value;
            }
        }
    }

    event_hideEndTime = function (){
        dojo.style.show('addEndTime');
        dojo.style.hide('removeEndTime');
        form.hideEnd.value = 1;
    }

    var validate = function() {
        var errors = {};
        if (dojo.string.trim(form.title.value).length == 0) {
            errors.title = xg.events.nls.html('pleaseEnterTitle');
        }
        /*if (dojo.string.trim(form.image.value).length == 0) {
            errors.type= xg.events.nls.html('pleaseChooseImage');
        }*/
        if (dojo.string.trim(form.description.value).length == 0) {
            errors.description = xg.events.nls.html('pleaseEnterDescription');
        }
        if (dojo.string.trim(form.type.value).length == 0) {
            errors.type = xg.events.nls.html('pleaseEnterType');
        }
        if (dojo.string.trim(form.location.value).length == 0) {
            errors.location = xg.events.nls.html('pleaseEnterLocation');
        }
        return errors;
    };
    dojo.event.connect(form, 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        dojo.require('xg.index.util.FormHelper');
        if (! xg.index.util.FormHelper.runValidation(form, validate)) { return; }
		var inp = form.getElementsByTagName('input');
		for(var i = 0;i<inp.length;i++) {
			if (inp[i].type == 'submit') {
				inp[i].disabled = true;
			}
		}
        // Show the spinner_section in a setTimeout call; otherwise the spinner
        // may fail to appear or stop spinning during the upload  [Jon Aquino 2007-01-17]
        window.setTimeout(function() { form.submit(); }, 0);
    });
    form.title.focus();
    xg.shared.util.setPlaceholder(form.website,'http://');

    dojo.event.connect(form.disableRsvp, 'onclick', function(event) {
        var v = form.disableRsvp.checked;
        form.hideGuests.disabled = v ? true : false;
        if (form.isClosed) {
            form.isClosed.disabled = v ? true : false;
        }
    });
}());
