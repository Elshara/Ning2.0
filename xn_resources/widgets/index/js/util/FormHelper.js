dojo.provide("xg.index.util.FormHelper");
dojo.require('xg.shared.IframeUpload');

xg.index.util.FormHelper = {

    runValidation: function(formNode, validationFunction, errorHeadline, event) {
        // Clear any existing errors
        xg.index.util.FormHelper.hideErrorMessages(formNode);
        dojo.lang.forEach(dojo.html.getElementsByClass('success', dojo.byId('xg_body')), function(successMessage) {
            dojo.style.hide(successMessage);
        });
        // Trim whitespace from form elements
        xg.index.util.FormHelper.trimTextInputsAndTextAreas(formNode);
        var errors = validationFunction(formNode);
        // If there are no errors, allow the event to bubble up and be processed
        if (dojo.lang.isEmpty(errors)) { return true; }
        // If there are errors, stop the event and show the error messages
        if (event) { dojo.event.browser.stopEvent(event); }
        xg.index.util.FormHelper.showErrorMessages(formNode, errors, errorHeadline);
        return false;
    },

    configureValidation: function(formOrButton, validationFunction, errorHeadline) {
        var eventName;
        var formNode;
        if (formOrButton.tagName == 'FORM') {
            eventName = 'onsubmit';
            formNode = formOrButton;
        } else {
            eventName = 'onclick';
            formNode = dojo.dom.getFirstAncestorByTag(formOrButton, 'form');
        }
        dojo.event.connect(formNode, eventName, function(event) {
            return xg.index.util.FormHelper.runValidation(formNode, validationFunction, errorHeadline, event);
        });
    },

    validateAndProcess: function(formOrButton, validationFunction, processingFunction, errorHeadline) {
        var eventName;
        var formNode;
        if (formOrButton.tagName == 'FORM') {
            eventName = 'onsubmit';
            formNode = formOrButton;
        } else {
            eventName = 'onclick';
            formNode = dojo.dom.getFirstAncestorByTag(formOrButton, 'form');
        }
        dojo.event.connect(formOrButton, eventName, function(event) {
            // Clear any existing errors
            dojo.lang.forEach(dojo.html.getElementsByClass('error', formNode), function(validationElement) { dojo.html.removeClass(validationElement,'error'); });
            // Trim whitespace from form elements
            xg.index.util.FormHelper.trimTextInputsAndTextAreas(formNode);
            var errors = validationFunction(formNode);
            xg.index.util.FormHelper.hideErrorMessages(formNode);
            // Stop the event, so nothing happens after the processing function
            // or the error display
            dojo.event.browser.stopEvent(event);
            // If there are no errors, call the processing function
            if (dojo.lang.isEmpty(errors)) {
                processingFunction(formNode);
            }
            // If there are errors, stop the event and show the error messages
            else {
                xg.index.util.FormHelper.showErrorMessages(formNode, errors, errorHeadline);
            }
        });
    },

    validateAndSave: function(formOrButton, validationFunction, successFunction, errorHeadline) {
        xg.index.util.FormHelper.validateAndProcess(formOrButton, validationFunction,
            function(form) {
                xg.index.util.FormHelper.save(form, successFunction, form.action)
            },
            errorHeadline);
    },

    trimTextInputsAndTextAreas: function(root) {
        dojo.lang.forEach(root.getElementsByTagName('textarea'), function(textarea) { textarea.value = dojo.string.trim(textarea.value); });
        dojo.lang.forEach(root.getElementsByTagName('input'), function(input) { if (input.type == 'text') { input.value = dojo.string.trim(input.value); } });
    },

    save: function(form, onSuccess, url) {
        if (! xg.index.util.FormHelper.validateFileInputsSpeciallyForIE(form)) { return; }
        var handle = function(data) {
            // Ensure JSON response is wrapped in parentheses; otherwise eval throws "invalid label" errors. Lasse Reichstein Nielsen,
            // "Objects from streams", http://groups.google.com/group/comp.lang.javascript/browse_thread/thread/cb27e65cd1897b2b/0eb38ac5f8e5020e?lnk=st&q=javascript+eval+parentheses&rnum=4&hl=en#0eb38ac5f8e5020e  [Jon Aquino 2006-04-30]
            if (data[0] != '(') { data = '(' + data + ')'; }
            try {
                data = eval(data);
                // Don't say if(errorMessages) otherwise IE throws an error [Jon Aquino 2006-05-10]
                if ('errorMessages' in data) {
                    xg.index.util.FormHelper.showErrorMessages(form, data.errorMessages);
                    return;
                }
                onSuccess(data);
            } catch (e) {
                // Something went wrong evaluating the returned JSON, perhaps it's
                // not actually JSON because of a playground error -- see PHO-226 [ David Sklar 2006-09-13 ]
                xg.index.util.FormHelper.showErrorMessages(form, {});
                return;
            }
        };
        var useIFrameTransport = xg.index.util.FormHelper.hasFileFields(form);
        if (useIFrameTransport) {
            xg.shared.IframeUpload.start(form, handle, url);
        } else {
            dojo.io.bind({
                url: url,
                // text/plain works for both the XmlHttpRequest and IFrame transports (if it is text/javascript, the IFrame transport
                // assumes that it is an HTML document containing the javascript in a textarea) [Jon Aquino 2006-05-06]
                mimetype : 'text/plain',
                formNode: form,
                method: 'post',
                // Must set encoding to preserve UTF-8 input in forms [ David Sklar 2006-05-16 ]
                encoding: 'utf-8',
                preventCache: true,
                load: function(type, data, event) { handle(data); },
                error: function(type, error) { throw error.message; }
            });
        }
    },

    hideErrorMessages : function(form) {
        var errorNode = xg.index.util.FormHelper.notificationNode(form);
        if (errorNode) {
            errorNode.innerHTML = '';
            dojo.html.hide(errorNode);
        }
        dojo.lang.forEach(dojo.html.getElementsByClass('error', form), function(el) { dojo.html.removeClass(el, 'error'); }, true);
    },

    showErrorMessages : function(form, errorMessages, headline) {
        var errorNode = xg.index.util.FormHelper.notificationNode(form);
        var errorHTML = '';
        /* Clear any existing error display */
        xg.index.util.FormHelper.hideErrorMessages(form);
        // If errorMessages is a string, attach it to the first form element
        if (dojo.lang.isString(errorMessages)) {
            var firstFieldname = null;
            var thisField = null;
            var i = 0;
            while((firstFieldname == null) && (thisField = form[i])) {
                if (thisField.tagName != 'FIELDSET') {
                    firstFieldname = thisField.name;
                }
                i++;
            }
            if (firstFieldname) {
                var tmp = { };
                tmp[firstFieldname] = errorMessages;
                errorMessages = tmp;
            }
        }
        // For each error message, add the text to the errorNode and add the error class to the appropriate element
        for (name in errorMessages) {
            // If there are multiple fields with the same name, an "array" will be returned according to "Radio", http://docs.sun.com/source/816-6408-10/radio.htm
            // However this object does not seem to be a true array, as dojo.lang.isArray returns false. So check if it has a length function [Jon Aquino 2006-05-05]
            if(form[name]){
                var field = form[name].length ? form[name][0] : form[name];
                xg.index.util.FormHelper.showErrorMessage(field);
            }
            if (dojo.lang.isArray(errorMessages[name])) {
                dojo.lang.forEach(errorMessages[name], function(n) { errorHTML += '<li>' + n + '</li>'; }, true);
            } else {
                errorHTML += '<li>' + errorMessages[name] + '</li>';
            }
        }
        if (errorNode && errorHTML.length && errorNode.tagName == 'DL') {
            if (! (headline && headline.length)) { headline = 'There has been an error'; }
            errorNode.innerHTML = '<dt>' + headline + '</dt><dd><ol>' + errorHTML + '</ol></dd>';
            dojo.html.setClass(errorNode,'errordesc msg clear');
        }
        if (errorNode && errorHTML.length && errorNode.tagName == 'DIV') { // New style [Jon Aquino 2008-04-16]
            errorNode.innerHTML = '<ul>' + errorHTML + '</ul>';
            dojo.html.setClass(errorNode,'errordesc');
        }
        if (errorNode && errorHTML.length) {
            dojo.html.show(errorNode);
            xg.index.util.FormHelper.scrollIntoView(errorNode);
        }
    },

    notificationNode: function(form) {
        var id;
        if (dojo.lang.isString(form)) {
            id = form + '_notify';
        } else {
            id = form.id + '_notify';
        }
        return dojo.byId(id);
    },

    // Called by showErrorMessages [Jon Aquino 2006-05-29]
    showErrorMessage : function(field) {
        /* If it's the RichTextEditor div, skip adding 'error' to the node, since it won't show through */
        if (field.getAttribute('dojotype') == 'Editor') {
            return false;
        }

        /* The 'error' class is applied to the element containing the field.
         * This is usually a <p/> or a <fieldset/>. */
        var errorTarget = field.parentNode;

        /* Skip containing labels */
        if (errorTarget.tagName == 'LABEL') {
            errorTarget = errorTarget.parentNode;
        }
        /* If it's an <li/>, apply the error class to the parent node (again, usually
         * a <p/> or <fieldset/> containing the <ul/> that contains the <li/>
         */
        if (errorTarget.tagName == 'LI') {
            if(dojo.dom.getFirstAncestorByTag(errorTarget, 'UL')){
                errorTarget = dojo.dom.getFirstAncestorByTag(errorTarget, 'UL').parentNode;
            }else if (dojo.dom.getFirstAncestorByTag(errorTarget, 'OL')){
                errorTarget = dojo.dom.getFirstAncestorByTag(errorTarget, 'OL').parentNode;
            }
        }
        /* Apply the class */
        dojo.html.addClass(errorTarget, 'error');
        /* If the error target is a <dd/>, then the error class must also be applied to the immediately preceding <dt/> */
        if (errorTarget.tagName == 'DD') {
            var errorParent = dojo.dom.prevElement(errorTarget);
            if (errorParent.tagName == 'DT') {
                dojo.html.addClass(errorParent,'error');
            }
        }
    },

    showMessage: function(node, messageClass, headline, body) {
        dojo.html.setClass(node, messageClass + " msg");
        body = dojo.string.trim(body);
        if (body.length && (body.charAt(0) != '<')) {
            body = '<p>' + body + '</p>';
        }
        node.innerHTML = '<dt>' + headline + '</td><dd>' + body + '</dd>';
        dojo.html.show(node);
        // @todo fade?
    },

    hasFileFields: function(form) {
        var inputs = form.getElementsByTagName('input');
        for	(var i = 0; i<inputs.length; i++) {
            if (inputs[i].type && inputs[i].type.toLowerCase() == 'file') {
                return true;
            }
        }
        return false;
    },

    indexOf : function(value, select) {
        for (var i = 0; i < select.length; i++) {
            if (select.options[i].value === value) { return i; }
        }
        return null;
    },

    select : function(value, select) {
        var i = xg.index.util.FormHelper.indexOf(value, select);
        // In Photo embed, if album has been deleted, select will not contain value [Jon Aquino 2006-11-28]
        if (i != null) { select.selectedIndex = i; }
    },

    selectedOption : function(select) {
        return select[select.selectedIndex];
    },

    radioValue : function(radio) {
        for (var i = 0; i < radio.length; i++) {
            if (radio[i].checked) {
                return radio[i].value;
            }
        }
        return null;
    },

    showOrHide : function(element, show) {
        if (show) { dojo.html.show(element); } else { dojo.html.hide(element); }
    },

    iframeTransportSupportsBrowser : function() {
        // Safari does not seem to work with the Dojo IFrameIO transport -- see Jonathan Aquino, "IFrameIO transport
        // does not seem to work in Safari (the test does not return anything in Safari)", http://trac.dojotoolkit.org/ticket/672  [Jon Aquino 2006-05-07]
        return dojo.render.html.ie || dojo.render.html.mozilla;
    },

    /**
     * Replaces <a href="#"> with <a href="javascript:void(0)">. # scrolls the page to the top.
     */
    replaceHashAnchors : function(node) {
        var anchors = node.getElementsByTagName('a');
        for (var i = 0; i < anchors.length; i++) {
            if (anchors[i].href.match(/#$/)) { anchors[i].href = 'javascript:void(0)'; }
        }
    },

    /**
     * Scrolls if necessary to bring the node into view
     */
    scrollIntoView : function(node) {
        var doc = document.body, docEl = document.documentElement, offset = {x:0,y:0},
            dim = { x: docEl.clientWidth || doc.clientWidth, y: document.clientHeight || docEl.clientHeight || doc.clientHeight },
            scroll = { x: window.pageXOffset || docEl.scrollLeft || doc.scrollLeft, y: window.pageYOffset || docEl.scrollTop || doc.scrollTop};
        dim.y = Math.min(dim.y, doc.clientHeight); // FF "transitional" mode fix
        for (var cur = node; cur; cur = cur.offsetParent) {
            offset.x += cur.offsetLeft || 0;
            offset.y += cur.offsetTop || 0;
            if (cur.tagName == 'BODY') break;
        }
        var l = offset.x - scroll.x,
            t = offset.y - scroll.y,
            r = offset.x + node.offsetWidth - dim.x - scroll.x,
            b = offset.y + node.offsetHeight - dim.y - scroll.y;
        var dx = l < 0 ? l : (r > 0 ? Math.min(l,r) : 0),
            dy = t < 0 ? t : (b > 0 ? Math.min(t,b) : 0);
        window.scrollBy(dx,dy);
    },

    /**
     * @see Jonathan Aquino, "IFrame transport: Entering 'abcde' for filename in IE causes Access Denied and prevents further submissions",
     *      http://trac.dojotoolkit.org/ticket/746
     * @see BAR-399
     */
    validateFileInputsSpeciallyForIE : function(form) {
        if (! (dojo.render.html.ie50 || dojo.render.html.ie55 || dojo.render.html.ie60)) { return true; }
        var errorMessages = {};
        var inputs = form.getElementsByTagName('input');
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].tagName != 'INPUT' || inputs[i].type != 'file') { continue; }
            // If blank, let server decide whether to ignore it or flag it as an error. (The Appearance Panel ignores it).
            // Don't allow spaces; otherwise we get the Access Denied error in IE. [Jon Aquino 2006-06-09]
            if (inputs[i].value.length === 0) { continue; }
            if (! inputs[i].value.match(/^[A-Za-z]:\\/)) { errorMessages[inputs[i].name] = xg.index.nls.html('fileNotFound'); }
        }
        xg.index.util.FormHelper.showErrorMessages(form, errorMessages);
        return dojo.lang.isEmpty(errorMessages);
    },

    validateRequired: function(errors, form, elementName, errorMessage) {
        if (form[elementName]) {
            if (typeof(form[elementName].value) == 'undefined') {
                if (xg.index.util.FormHelper.checkedCount(form[elementName]) == 0) {
                    errors = xg.index.util.FormHelper.addValidationError(errors, elementName, errorMessage);
                }
            } else {
                if (! form[elementName].value.length) {
                    errors = xg.index.util.FormHelper.addValidationError(errors, elementName, errorMessage);
                }
            }
        }
        return errors;
    },

    parseDateFromForm: function(form, baseElementName) {
        var monthElement = form[baseElementName + '_month'];
        var dayElement = form[baseElementName + '_day'];
        var yearElement = form[baseElementName + '_year'];
        if (monthElement && dayElement && yearElement) {
            var month = parseInt(monthElement.value);
            var day = (dayElement.value == 'dd') ? 0 : parseInt(dayElement.value.replace(/^0*/, ''));
            var year = (yearElement.value == 'yyyy') ? 0 : parseInt(yearElement.value);
            if ((month == 0) || (day == 0) || (year == 0)) {
                return false;
            } else {
                return { 'month': month, 'day': day, 'year' : year };
            }
        } else {
            return null;
        }
    },

    isDateValid: function(year, month, day) {
        var d = new Date(year, month - 1, day);
        return ((d.getFullYear() == year) && (d.getMonth() == (month -1)) && (d.getDate() == day));

    },

    validateRequiredDate: function(errors, form, baseElementName, dateMissingMessage, dateInvalidMessage) {
        var res = xg.index.util.FormHelper.parseDateFromForm(form, baseElementName);
        if (res === false) {
            errors = xg.index.util.FormHelper.addValidationError(errors, baseElementName + '_month', dateMissingMessage);
        } else if (res && (! xg.index.util.FormHelper.isDateValid(res.year, res.month, res.day))) {
            errors = xg.index.util.FormHelper.addValidationError(errors, baseElementName + '_month', dateInvalidMessage);
        }
        return errors;
    },

    validateDate: function(errors, form, baseElementName, errorMessage) {
        var res = xg.index.util.FormHelper.parseDateFromForm(form, baseElementName);
        if (res && (! xg.index.util.FormHelper.isDateValid(res.year, res.month, res.day))) {
            errors = xg.index.util.FormHelper.addValidationError(errors, baseElementName + '_month', errorMessage);
        }
        return errors;
    },

    validateChoice: function(errors, form, elementName, choices, elementLabel) {
        var count = xg.index.util.FormHelper.checkedCount(form[elementName]);
        if (form[elementName] && (count > 0)) {
            elementLabel = xg.index.util.FormHelper.buildValidationLabel(elementName, elementLabel);
            if (count > 1) {
                errors = xg.index.util.FormHelper.addValidationError(errors, elementName, elementLabel + " can only have one value ");
            }
            var value = null;
            // dojo.lang.isArray() doesn't work here
            if (typeof(form[elementName].length) !== 'undefined') {
                for (var i = 0; i < form[elementName].length; i++) {
                    if (form[elementName][i].checked === true) {
                        value = form[elementName][i].value;
                    }
                }
            } else {
                value = form[elementName].value;
            }
            if (! dojo.lang.inArray(choices, value)) {
                errors = xg.index.util.FormHelper.addValidationError(errors, elementName, elementLabel + " has to be one of: " + choices.join(', '));
            }
        }
        return errors;
    },

     validateMultipleChoice: function(errors, form, elementName, choices, elementLabel) {
        if (form[elementName] && (xg.index.util.FormHelper.checkedCount(form[elementName]) > 0)) {
            elementLabel = xg.index.util.FormHelper.buildValidationLabel(elementName, elementLabel);
            var values = [];
            // dojo.lang.isArray() doesn't work here
            if (typeof(form[elementName].length) !== 'undefined') {
                for (var i in form[elementName]) {
                    if (form[elementName][i].checked === true) {
                        values.push(form[elementName][i].value);
                    }
                }
            } else {
                values.push(form[elementName].value);
            }
            dojo.lang.forEach(values, function (value) {
                if (! dojo.lang.inArray(choices, value)) {
                    errors = xg.index.util.FormHelper.addValidationError(errors, elementName, elementLabel + " has to be some of: " + choices.join(', '));
                }
            }, true);
        }
        return errors;
    },

    capitalize: function (str) {
        var words = str.split(' ');
        for(var i=0; i<words.length; i++){
            words[i] = words[i].charAt(0).toUpperCase() + words[i].substring(1);
        }
        return words.join(" ");
    },

    /**
     * @deprecated Not compatible with I18N
     */
    buildValidationLabel: function(name, label) {
        if (! label) {
            label = xg.index.util.FormHelper.capitalize(name.replace(/_/,' '));
        }
        return label;
    },

    addValidationError: function(errors, name, message) {
        if (errors[name]) {
            errors[name].push(message);
        } else {
            errors[name] = message;
        }
        return errors;
    },

    // How many of the radios or checkboxes with this element name are checked?
    checkedCount: function(element) {
        var count = 0;
        if (element && (typeof(element.length) != 'undefined')) {
            for (var i = 0; i < element.length; i++) {
                if (element[i].checked === true) { count++; }
            }
        }
        return count;
    },

    /**
     * Ensures the given popup node appears above all xg_modules.
     * Workaround for IE 6 and 7.
     *
     * @param HTMLElement popupNode  The popup
     * @see Ben Hollis, "Not honoring z-index", http://brh.numbera.com/experiments/ie7_tests/zindex.html
     * @see Aleksandar Vacic, "Effect of z-index value to positioned elements", http://www.aplus.co.yu/lab/z-pos/
     */
    fixPopupZIndexAfterShow: function(popupNode) {
        if (! dojo.render.html.ie) { return; }
        dojo.lang.forEach(xg.index.util.FormHelper.popupAncestorsForZIndexFix(popupNode), function(ancestor) {
            ancestor.style.zIndex = 10;
        });
    },

    /**
     * Cleans up the popup z-index fix after the popup node is hidden.
     *
     * @param HTMLElement popupNode  The popup
     */
    fixPopupZIndexBeforeHide: function(popupNode) {
        if (! dojo.render.html.ie) { return; }
        dojo.lang.forEach(xg.index.util.FormHelper.popupAncestorsForZIndexFix(popupNode), function(ancestor) {
            ancestor.style.zIndex = null;
        });
    },

    /**
     * Finds the ancestors on which to change the z-index, for the IE popup fix.
     *
     * @param HTMLElement popupNode  The popup
     * @return array The ancestors
     */
    popupAncestorsForZIndexFix: function(popupNode) {
        return dojo.dom.getAncestors(popupNode, function(ancestor) { return dojo.html.hasClass(ancestor, 'xg_module') || dojo.html.hasClass(ancestor, 'xg_module_body'); });
    }

};