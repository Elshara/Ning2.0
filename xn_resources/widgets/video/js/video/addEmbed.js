dojo.provide('xg.video.video.addEmbed');


(function() {

    var getEmbedCount = function(embedCode) {
        var a = embedCode.match(/<\s*\bobject\b/gi, embedCode);
        var b = embedCode.match(/<\s*\bembed\b/gi, embedCode);
        return Math.max(a ? a.length : 0, b ? b.length : 0);
    };

    var form = dojo.byId('add_video_module').getElementsByTagName('form')[0];

    var validate = function() {
        var errors = {}
        var embedCodeMaxLength = 4000;
        if (form.embedCode.value.length > embedCodeMaxLength) {
            errors.embedCode = xg.video.nls.html('numberOfCharactersExceedsMaximum', form.embedCode.value.length, embedCodeMaxLength);
        }
        if (dojo.string.trim(form.embedCode.value).length == 0) {
            errors.embedCode = xg.video.nls.html('pasteInEmbedCode');
        }
        var embedCount = getEmbedCount(form.embedCode.value != null ? form.embedCode.value.toLowerCase() : '');
        if (embedCount > 1) {
            errors.embedCode = dojo.string.escape('html', xg.video.nls.html('embedCodeContainsMoreThanOneVideo'));
        }
        if (embedCount == 0 && dojo.string.trim(form.embedCode.value).length > 0) {
            errors.embedCode = xg.video.nls.html('embedCodeMissingTag');
        }
        return errors;
    };

    var showUploadingScreen = function() {
        dojo.html.hide(dojo.byId('add_video_module'));
        dojo.html.show(dojo.byId('adding_video_module'));
        window.scrollTo(0, 0);
    }

    dojo.event.connect(form, 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        dojo.require('xg.index.util.FormHelper');
        if (! xg.index.util.FormHelper.runValidation(form, validate)) {
            return;
        } else {
            var inputs = dojo.html.getElementsByClass('button', form, 'input');
            inputs[0].disabled = true;
            form.submit();
        }
    });

}());