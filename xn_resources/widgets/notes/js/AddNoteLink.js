dojo.provide('xg.notes.AddNoteLink');
dojo.require('xg.shared.util');

/**
 * "Add note" link in Notes module. Requires #add_note_form DIV element with the form content.
 */
dojo.widget.defineWidget('xg.notes.AddNoteLink', dojo.widget.HtmlWidget, {
    _baseUrl: '',			// base URL for note edit. Title + "?edit=true" will be added.
    _maxLength: 0,

    fillInTemplate: function(args, frag) {
        this.module = this.getFragNodeRef(frag);
        dojo.event.connect(this.module, 'onclick', dojo.lang.hitch(this, this.onClick));
        dojo.html.show(this.module);
    },

    onClick: function(event) {
        dojo.event.browser.stopEvent(event);
        xg.shared.util.confirm({
            title: xg.notes.nls.text('addNewNote'),
            bodyHtml: dojo.byId("add_note_form").innerHTML,
            closeOnlyIfOnOk: 1,
            onOk: dojo.lang.hitch(this, this.onOk)
        });
    },

    onOk: function(div) {
        var title = dojo.html.getElementsByClass('textfield', div)[0].value.replace(/^\s+/g,"").replace(/\s+$/,"").replace(/\s/g,"_");
        if (title == "") {
            alert(xg.notes.nls.text('pleaseEnterNoteTitle'));
            return false;
        }
        if (title.length > this._maxLength) {
            alert(xg.notes.nls.text('noteTitleTooLong'));
            return false;
        }
		if (title.match(/[|?#\/%.]/)) {
            window.location = this._baseUrl + "index/edit?noteKey="+encodeURIComponent(title) + "&create=1";
        } else {
            window.location = this._baseUrl + encodeURIComponent(title) + "?edit=true&create=1";
        }
        return true;
    }
});
