dojo.provide('xg.photo.index._shared');
dojo.provide('xg.photo');
dojo.require('xg.shared.util');

xg.photo = dojo.lang.mixin(xg.photo, {
    fixImagesInIE: function(imgs, sync, width, height) { xg.shared.util.fixImagesInIE(imgs, sync, width, height); },
    fixTransparencyInIEProper: function(img, width, height) { xg.shared.util.fixTransparencyInIEProper(img, width, height); },
    fixTransparencyInIE: function(node) { xg.shared.util.fixTransparencyInIE(node); },

    trimTextInputsAndTextAreas: function(root) {
        dojo.lang.forEach(root.getElementsByTagName('textarea'), function(textarea) { textarea.value = dojo.string.trim(textarea.value); });
        dojo.lang.forEach(xg.photo.select(root.getElementsByTagName('input'), function(input) { return input.type == 'text'; }), function(textInput) { textInput.value = dojo.string.trim(textInput.value); });
    },

    // From ningbar/js/LangHelper.js  [Jon Aquino 2006-07-03]
    select: function(array, f) {
        var result = [];
        dojo.lang.forEach(array, function(item) {
            if (f(item)) { result.push(item); }
        });
        return result;
    },

    collect: function(array, f) {
        var result = [];
        dojo.lang.forEach(array, function(item) {
            result.push(f(item));
        });
        return result;
    },

    /**
     * Toggles the open/closed state of a section (usuall a DIV) by making it visible/invisible
     * and changing the class of the toggling object to 'open'/'closed', respectively.
     *
     * @param toggleObj      The object that acts as the toggle, e.g. a H4 or P with an onclick
     *                       handler
     * @param idOfSectionObj The id of the section object
     * @param openText       Optional: the text of the toggle object when open
     * @param closedText     Optional: the text of the toggle object when closed
     */
    toggleSection: function(toggleObj, idOfSectionObj, openText, closedText) {
      var sectionObj = dojo.byId(idOfSectionObj);

      if (dojo.html.getClass(toggleObj) == 'open') {
        dojo.html.setClass(toggleObj, 'closed');
        if (closedText) {
            toggleObj.innerHTML = closedText;
        }
        sectionObj.style.display = 'none';
      } else {
        dojo.html.setClass(toggleObj, 'open');
        if (openText) {
            toggleObj.innerHTML = openText;
        }
        sectionObj.style.display = 'block';
      }
    },

    parseUrlParameters: function(url) {
        var urlParts   = url.split('?');
        var urlContent = new Object;

        if (urlParts.length > 1) {
            var urlPairs   = urlParts[1].split('&');

            for (var idx = 0; idx < urlPairs.length; idx++) {
                var kv = urlPairs[idx].split('=');

                urlContent[kv[0]] = kv[1];
            }
        }
        return urlContent;
    }
});

