dojo.provide('xg.index.language.edit');

dojo.require('xg.index.language.jslint');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

/**
 * Behavior for the Edit Language page.
 */
(function() {
    if (! dojo.byId('translation_form')) { return; }

    var targetTextareas = dojo.lang.filter(dojo.byId('translation_form').getElementsByTagName('textarea'), function(textarea) { return textarea.name.indexOf('messages') === 0; });

    /**
     * Checks the JavaScript functions that the user has entered, to see if they contain
     * syntax errors. If so, error messages are displayed.
     *
     * @return boolean  whether the JavaScript functions on the current page look syntactically valid
     */
    var validateJavaScriptFunctions = function() {
        var errorOl = dojo.byId('translation_error_list').getElementsByTagName('ol')[0];
        var result = true;
        var firstErrorTr = null;
        dojo.lang.forEach(targetTextareas, function(textarea) {
            if (! textarea.value.match(/^\s*function\s*\(/)) { return; }
            if (JSLINT('var f = ' + textarea.value + ';')) { return; }
            var errorMessages = [];
            dojo.lang.forEach(JSLINT.errors, function(error) {
                if (! error) { return; } // error is null sometimes, for some reason  [Jon Aquino 2007-08-13]
                if (error.reason.match('Missing semicolon')) { return; }
                if (error.reason.match('Unnecessary')) { return; }
                if (error.reason.match("This 'switch' should be an 'if'.")) { return; }
                if (error.reason.match('Stopping, unable to continue')) { return; }
                if (errorMessages.length > 0) { return; } // Report just the first error message [Jon Aquino 2007-08-18]
                if (result) {
                    dojo.lang.forEach(dojo.html.getElementsByClass('errordesc', dojo.byId('translation_form'), 'p'), function(p) { dojo.dom.removeNode(p); });
                    dojo.lang.forEach(dojo.html.getElementsByClass('error', dojo.byId('translation_form'), 'td'), function(td) { dojo.html.removeClass(td, 'error'); });
                    errorOl.innerHTML = '';
                    dojo.style.show(dojo.byId('translation_error_list'));
                    result = false;
                }
                var errorMessage = error.reason;
                errorOl.innerHTML += '<li>' + dojo.string.escape('html', errorMessage) + '</li>';
                errorMessages.push(errorMessage);
            });
            if (errorMessages.length > 0) {
                var h4 = dojo.dom.prevElement(dojo.dom.getFirstAncestorByTag(textarea, 'tr')).getElementsByTagName('h4')[0];
                var errorTr = dojo.dom.prevElement(dojo.dom.getFirstAncestorByTag(textarea, 'tr'));
                errorTr.getElementsByTagName('td')[0].innerHTML += '<p class="errordesc">' + dojo.lang.map(errorMessages, function(errorMessage) { return dojo.string.escape('html', errorMessage); }).join('<br />') + '</p>';
                dojo.style.show(errorTr);
                dojo.html.addClass(dojo.dom.getFirstAncestorByTag(textarea, 'td'), 'error');
                if (! firstErrorTr) { firstErrorTr = errorTr; }
            }
        });
        if (! result) {
            xg.index.util.FormHelper.scrollIntoView(dojo.dom.nextElement(firstErrorTr));
            xg.index.util.FormHelper.scrollIntoView(firstErrorTr); // Ensure both elements are in view [Jon Aquino 2007-08-18]
        }
        return result;
    }

    var saving = false;
    dojo.event.connect(dojo.byId('translation_form'), 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        window.submitTranslationForm();
    });

    /**
     * Validates the form, then submits it.
     */
    window.submitTranslationForm = function() {
        if (! validateJavaScriptFunctions()) { return; }
        saving = true;
        dojo.byId('translation_form').submit();
    }

    var textAreasChanged = dojo.html.getElementsByClass('errordesc', dojo.byId('translation_form')).length > 0;
    dojo.lang.forEach(targetTextareas, function(textarea) {
        dojo.event.connect(textarea, 'onchange', function() { textAreasChanged = true; });
    });

    // onbeforeunload doesn't work on older browsers, but that's OK as this is just precautionary.
    // See http://www.sitepoint.com/forums/showpost.php?p=1976587&postcount=5  [Jon Aquino 2007-08-08]
    window.onbeforeunload = function() {
        if (textAreasChanged && ! saving) { return xg.index.nls.text('youHaveUnsavedChanges'); }
    };

    if (dojo.byId('restore_defaults_form')) {
        dojo.event.connect(dojo.byId('restore_defaults_form'), 'onsubmit', function(event) {
            dojo.event.browser.stopEvent(event);
            xg.shared.util.confirm({
                    title: xg.index.nls.text('resetTextQ'),
                    bodyHtml: '<p>' + xg.index.nls.html('resetTextToOriginalVersion') + '</p>',
                    onOk: function() { dojo.byId('restore_defaults_form').submit(); }
            });
        });
    }

}());
