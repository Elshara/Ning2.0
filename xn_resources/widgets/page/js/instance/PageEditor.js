dojo.provide('xg.page.instance.PageEditor');

dojo.require('xg.page.instance.edit');

/**
 * A set of fields for editing a Page widget instance
 */
dojo.widget.defineWidget('xg.page.instance.PageEditor', dojo.widget.HtmlWidget, {

    /** Text field for the title. */
    titleInput: null,

    /** Text field for the directory. */
    directoryInput: null,

    /** Checkbox for whether to display a tab. */
    displayTabInput: null,

    /** Error list. */
    errorDl: null,

    /**
     * Initializes the PageEditor
     */
    fillInTemplate: function(args, frag) {
        var div = this.getFragNodeRef(frag);
        this.titleInput = div.getElementsByTagName('input')[0];
        this.directoryInput = div.getElementsByTagName('input')[1];
        this.displayTabInput = div.getElementsByTagName('input')[2];
        this.errorDl = dojo.html.getElementsByClass('errordesc', div, 'dl')[0];
        if (this.directoryInput.value.length > 0) {
            // Don't allow renaming the directory, because content is tied to this value (BAZ-7269) [Jon Aquino 2008-04-21]
            dojo.html.addClass(this.directoryInput, 'disabled');
            this.directoryInput.disabled = true;
        }
        if (this.directoryInput.value == 'page') {
            // "page" instance directory must exist [Jon Aquino 2008-04-21]
            dojo.style.hide(dojo.html.getElementsByClass('delete', div, 'a')[0].parentNode);
        }
        dojo.event.connect(dojo.html.getElementsByClass('add', div, 'a')[0], 'onclick', function(event) {
            dojo.event.browser.stopEvent(event);
            xg.page.instance.edit.addPage('', '', true);
        });
        dojo.event.connect(dojo.html.getElementsByClass('delete', div, 'a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            xg.page.instance.edit.removePage(div);
        }));
    },

    /**
     * Displays the given error message.
     *
     * @param errors  array of HTML error messages keyed by field name
     */
    showErrors: function(errors) {
        var innerHTML = '';
        for (fieldName in errors) {
            innerHTML += '<li>' + errors[fieldName] + '</li>';
            if (fieldName == 'title') { dojo.html.addClass(dojo.dom.prevElement(this.titleInput.parentNode), 'error'); }
            if (fieldName == 'directory') { dojo.html.addClass(dojo.dom.prevElement(this.directoryInput.parentNode), 'error'); }
        }
        this.errorDl.getElementsByTagName('ol')[0].innerHTML = innerHTML;
        dojo.style.show(this.errorDl);
    },

    /**
     * Hides the error messages.
     */
    hideErrors: function() {
        dojo.style.hide(this.errorDl);
        dojo.lang.forEach(dojo.html.getElementsByClass('error', this.domNode), function(node) { dojo.html.removeClass(node, 'error'); });
    },

    /**
     * Returns data for the form submission
     *
     * @return  an object with title, directory, and displayTab
     */
    getData: function() {
        return { title: this.titleInput.value, directory: this.directoryInput.value, displayTab: this.displayTabInput.checked };
    }

});

