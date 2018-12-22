dojo.provide('xg.music.track.new');

dojo.require('xg.shared.util');

/**
 * Javascript for the new and edit actions.
 */
(function() {

    var trackCount = dojo.byId('numTracks').value;

    var isAddTrackPage = function() {
        return dojo.byId('upload-track-step') != null;
    }

    var validate = function() {
        var errors = {}

        if (isAddTrackPage()) {
            var numTracks = 0;
            var urlsValid = true;
            for (var idx = 1; idx <= trackCount; idx++) {
                if (dojo.string.trim(form.elements['track_0' + idx].value).length > 0) {
                    numTracks++;
                    if (! form.elements['track_0' + idx].value.match('://')) { urlsValid = false; }
                }
            }
            if(dojo.byId('linkMode')) {
                if (numTracks == 0) { errors.track01 = xg.music.nls.html('pleaseEnterTrackLink'); }
                if (! urlsValid) { errors.track01 = xg.music.nls.html('entryNotAUrl'); }
            } else {
                if (numTracks == 0) { errors.track01 = xg.music.nls.html('pleaseSelectTrackToUpload'); }
            }
        }
        return errors;
    };

    var form = dojo.byId('xg_body').getElementsByTagName('form')[0];

    /**
     * Returns whether all filenames look like MP3s
     */
    var areAllFilesMp3s = function() {
        if(dojo.byId('linkMode')) { return true; }
        var nonMp3s = false;
        for (var i = 1; i <= trackCount; i++) {
            var filename = dojo.string.trim(form.elements['track_0' + i].value);
            if (filename.length == 0) { continue; }
            if (! filename.match(/\.mp3/i)) { nonMp3s = true; }
        }
        return ! nonMp3s;
    }

    dojo.require('xg.index.util.FormHelper');
    xg.index.util.FormHelper.configureValidation(form, validate);

    dojo.event.connect(form, 'onsubmit', function(event) {
        if (!dojo.lang.isEmpty(validate())) {
            dojo.event.browser.stopEvent(event);
            return;
        }
        if (!isAddTrackPage()) {
            return;
        }
        dojo.event.browser.stopEvent(event);
        var submit = dojo.lang.hitch(this, function() {
            // Show the Uploading Song message in a setTimeout call; otherwise the spinner
            // may fail to appear or stop spinning during the upload  [Jon Aquino 2006-07-25]
            window.setTimeout(function() {
                form.submit(); // Submit form before hiding it, as Safari ignores fields that are not displayed [Jon Aquino 2007-05-30]
                if(!dojo.byId('linkMode')) {
                    dojo.html.hide(dojo.byId('add_tracks_module'));
                    dojo.html.show(dojo.byId('adding_music_module'));
                    window.scrollTo(0, 0);
                }
            }, 0);
        });
        if (areAllFilesMp3s()) { submit(); }
        else { xg.shared.util.confirm({ bodyText: xg.music.nls.text('fileIsNotAnMp3'), onOk: submit }); }
    });

}());