dojo.provide('xg.forum.topic.CategoryPicker');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');

/**
 * A link that turns into a drop-down for choosing a topic's category.
 */
dojo.widget.defineWidget('xg.forum.topic.CategoryPicker', dojo.widget.HtmlWidget, {

    /** Endpoint for setting the category */
    _setValueUrl: '',

    /** JSON array of objects, each with the following properties: displayText, id */
    _options: '',

    /** ID of the topic's Category */
    _currentCategoryId: '',

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        var a = this.getFragNodeRef(frag);
        var options = dj_eval(this._options);
        var selectHtml = '<select>';
        for (var i = 0; i < options.length; i++) {
            selectHtml += '<option value="' + dojo.string.escape('html', options[i].id) + '">' + dojo.string.escape('html', options[i].displayText) + '</option>';
        }
        selectHtml += '</select>';
        var select = dojo.html.createNodesFromText(selectHtml)[0];
        xg.index.util.FormHelper.select(this._currentCategoryId, select);
        dojo.event.connect(a, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            var span = a.parentNode;
            span.parentNode.replaceChild(select, span);
        }));
        dojo.event.connect(select, 'onchange', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
			var form = xg.shared.util.createElement('<form method="post"><input type="hidden" name="categoryId"/></form>');
            form.action = this._setValueUrl;
            form.categoryId.value = options[select.selectedIndex].id;
            form.appendChild(xg.shared.util.createCsrfTokenHiddenInput());
            document.body.appendChild(form);
            form.submit();
        }));
    }
});
