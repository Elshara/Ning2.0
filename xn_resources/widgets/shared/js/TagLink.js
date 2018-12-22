dojo.provide('xg.shared.TagLink');

dojo.require('xg.shared.util');

/**
 * A link that shows a dialog for specifying tags.
 */
dojo.widget.defineWidget('xg.shared.TagLink', dojo.widget.HtmlWidget, {
    /** Endpoint that sets the tags */
    _actionUrl: '<required>',
    /** Comma-delimited list of tags */
    _tags: '',
    /** can the form be submitted without a value?  true or false */
    _allowEmptySubmission: true,
    /** Warning message to display if submitted empty */
    _emptySubmissionMessage: '',
    /** The maximum allowed length (optional) */
    _maxlength: 0,
    /** optional id of the html field to update with new tags */
    _updateId: 'tagsList',
    /** optional js language key for 'Add Tags' */
    _addKey: 'addTags',
    /** optional js language key for 'Edit Your Tags' */
    _editKey: 'editYourTags',
    /** optional class for the div */
    _popOver: false,
    fillInTemplate: function(args, frag) {
        var li = this.getFragNodeRef(frag);
        var maxlength = this._maxlength ? 'maxlength="' + this._maxlength + '"' : '';
        var formblock = dojo.html.createNodesFromText(dojo.string.trim(' \
        <div class="desc" style="display: none;"> \
            <form> \
                <input class="textfield" type="text" style="width: 95%;" ' + maxlength + ' /> \
                <div class="align-right pad5"> \
                    <input class="button small" type="submit" value="' + xg.shared.nls.html('save') + '"/> \
                </div> \
            </form> \
        </div>'))[0];
        if (this._popOver) {
            var formblock = dojo.html.createNodesFromText(dojo.string.trim(' \
            <small class="showembed" style="display:none;"> \
                <form> \
                    <input class="textfield" type="text" style="width: 160px;" ' + maxlength + ' /> \
                    <div class="align-right pad5"> \
                        <input class="button small" type="submit" value="' + xg.shared.nls.html('save') + '"/> \
                    </div> \
                </form> \
            </small>'))[0];
        }
        dojo.html.insertAfter(formblock,li);
        var href = li.getElementsByTagName('a')[0];
        // attach onclick toggle
        dojo.event.connect(href, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (dojo.style.isShowing(formblock)) {
                dojo.html.hide(formblock);
            } else {
                dojo.html.show(formblock);
            }
        }));
        // attach form button
        var inputs = formblock.getElementsByTagName('input');
        var tagField = inputs[0];
        tagField.value = this._tags;
        dojo.event.connect(formblock.getElementsByTagName('form')[0], 'onsubmit', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (this._allowEmptySubmission == false && tagField.value.length == 0) {
                xg.shared.util.alert(this._emptySubmissionMessage);
            } else {
                href.className = "desc working";
                dojo.io.bind({
                    url: this._actionUrl,
                    method  : "post",
                    content: {tags: tagField.value},
                    encoding:'utf-8',
                    preventCache: true,
                    mimetype: 'text/javascript',
                    load: dojo.lang.hitch(this, function(type, data, event){
                        if (tagField.value.length) {
                            href.className = "desc edit";
                            href.innerHTML = xg.shared.nls.html(this._editKey);
                            dojo.html.hide(formblock);
                        } else {
                            href.className = "desc add";
                            href.innerHTML = xg.shared.nls.html(this._addKey);
                            dojo.html.hide(formblock);
                        }
                        if ("undefined" != typeof data["html"]) {
                            var tagsList = dojo.byId(this._updateId);
                            if (tagsList) {
                                tagsList.innerHTML = data.html;
                            }
                        }
                    })
                });
            }
        }));
    }
});
