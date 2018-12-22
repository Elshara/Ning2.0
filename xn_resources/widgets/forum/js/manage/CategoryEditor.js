dojo.provide('xg.forum.manage.CategoryEditor');

dojo.require('xg.forum.manage.index');
dojo.require('dojo.dnd.HtmlDragSource');
dojo.require('xg.shared.util');

/**
 * A set of fields for editing a category
 */
dojo.widget.defineWidget('xg.forum.manage.CategoryEditor', dojo.widget.HtmlWidget, {

    /** ID of the category object, or an empty string if the category is new */
    _id: '',

    /**
     * Space-delimited string of IDs from deleted categories whose Topics
     * have been moved to this category; this category's ID is also included, for convenience.
     * The maximum number of IDs is 100, which is the limit on an "in" filter.
     * The ID string "null" is used for Topics with null categoryIds.
     */
    _alternativeIds: '',

    /** the number of topics in this category */
    _topicCount: 0,

    fillInTemplate: function(args, frag) {
        var fieldset = this.getFragNodeRef(frag);
        new dojo.dnd.HtmlDragSource(fieldset);
        this.membersCanAddTopicsRadioButton = dojo.html.getElementsByClass('radio', fieldset, 'input')[0];
        this.onlyICanAddTopicsRadioButton = dojo.html.getElementsByClass('radio', fieldset, 'input')[1];
        this.membersCanReplyCheckbox = dojo.html.getElementsByClass('checkbox', fieldset, 'input')[0];
        var span = dojo.dom.getAncestorsByTag(this.membersCanReplyCheckbox, 'span', true);
        dojo.event.connect([this.membersCanAddTopicsRadioButton, this.onlyICanAddTopicsRadioButton], 'onclick', dojo.lang.hitch(this, function(event) {
            // Keep the checked/disabled logic in sync with fragment_categoryEditor.php [Jon Aquino 2007-03-27]
            if (this.membersCanAddTopicsRadioButton.checked) {
                this.membersCanReplyCheckbox.checked = true;
                this.membersCanReplyCheckbox.disabled = true;
                dojo.html.addClass(span, 'disabled');
            } else {
                this.membersCanReplyCheckbox.disabled = false;
                dojo.html.removeClass(span, 'disabled');
            }
        }));
        dojo.event.connect(dojo.html.getElementsByClass('add', fieldset, 'a')[0], 'onclick', function(event) {
            dojo.event.browser.stopEvent(event);
            xg.forum.manage.index.addCategory(fieldset);
        });
        dojo.event.connect(dojo.html.getElementsByClass('delete', fieldset, 'a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.remove();
        }));
        dojo.html.show(dojo.byId('save_button'));
        if (window.location.href.match(/test=yes/)) {
            var dl = fieldset.getElementsByTagName('dl')[0];
            dl.appendChild(dojo.html.createNodesFromText('<dt>Alternative IDs:</dt>')[0]);
            dl.appendChild(this.alternativeIdsElement = dojo.html.createNodesFromText('<dd class="test alternative_ids"></dd>')[0]);
            dl.appendChild(dojo.html.createNodesFromText('<dt>Topic Count:</dt>')[0]);
            dl.appendChild(this.topicCountElement = dojo.html.createNodesFromText('<dd class="test topic_count"></dd>')[0]);
            this.updateDebugSection();
        }
    },
    /**
     * Removes this CategoryEditor from the list
     */
    remove: function() {
        if (this._topicCount == 0) {
            xg.forum.manage.index.removeCategory(this.domNode);
            return;
        }
        this.showDeleteCategoryDialog();
    },
    /**
     * Updates the data shown in the debug display
     */
    updateDebugSection: function() {
        if (! this.alternativeIdsElement) { return; }
        this.alternativeIdsElement.innerHTML = this._alternativeIds + '&nbsp;';
        this.topicCountElement.innerHTML = this._topicCount + '&nbsp;';
    },
    /**
     * Returns an object containing data for the category.
     *
     * @return object  category metadata
     */
    createCategory: function() {
        return {
            id: this._id ? this._id : null,
            title: this.getTitle(),
            description: this.domNode.getElementsByTagName('textarea')[0].value,
            membersCanAddTopics: this.membersCanAddTopicsRadioButton.checked,
            membersCanReply: this.membersCanReplyCheckbox.checked,
            alternativeIds: this._alternativeIds
        };
    },
    /**
     * Displays the dialog box for deleting a category.
     */
    showDeleteCategoryDialog: function() {
        var availableCategoryEditors = this.availableCategoryEditors();
        if (availableCategoryEditors.length == 0) {
            var html = '<p>' + xg.forum.nls.html('discussionsWillBeDeleted') + '</p>';
        } else {
            var html = ' \
                    <p>' + xg.forum.nls.html('whatDoWithDiscussions') + '</p> \
                    <fieldset> \
                        <ul class="options"> \
                            <li> \
                                <label><input name="action" type="radio" class="radio" checked="checked" />' + xg.forum.nls.html('moveDiscussionsTo') + '</label><br /> \
                                <select style="margin:0 0 0.5em 1.5em"> \
                                    ' + dojo.lang.map(availableCategoryEditors, function(editor) { return '<option value="' + editor.widgetId + '">' + dojo.string.escape('html', editor.getTitle(), true) + '</option>' }) + ' \
                                </select> \
                            </li> \
                            <li><label><input name="action" type="radio" class="radio"/>' + xg.forum.nls.html('deleteDiscussions') + '</label></li> \
                        </ul> \
                    </fieldset>';
        }
        xg.shared.util.confirm({
            title: xg.forum.nls.text('deleteCategory'),
            bodyHtml: html,
            onOk: dojo.lang.hitch(this, function(dialog) {
                if (availableCategoryEditors.length > 0 && dojo.html.getElementsByClass('radio', dialog)[0].checked) {
                    var select = dialog.getElementsByTagName('select')[0];
                    var selectedCategoryEditor = dojo.widget.manager.getWidgetById(select[select.selectedIndex].value);
                    selectedCategoryEditor.addAlternativeIds(this.getAlternativeIds());
                    selectedCategoryEditor.addToTopicCount(this._topicCount);
                }
                xg.forum.manage.index.removeCategory(this.domNode);
            })
        });
    },
    /**
     * Finds categories that are able to take the discussions of this category upon deletion.
     *
     * @return The CategoryEditors
     */
    availableCategoryEditors: function() {
        var categoryEditors = dojo.lang.map(dojo.html.getElementsByClass('category', dojo.byId('category_container')), function(fieldset) {
            return dojo.widget.manager.getWidgetByNode(fieldset);
        });
        return dojo.lang.filter(categoryEditors, dojo.lang.hitch(this, function(editor) {
            if (editor === this) { return false; }
            return editor.getAlternativeIds().length + this.getAlternativeIds().length < 100; // Filter limit [Jon Aquino 2007-03-30]
        }));
    },
    /**
     * Returns the set of alternative IDs: IDs from deleted categories whose Topics
     * have been moved to this category; this category's ID is also included, for convenience.
     * The maximum number of IDs is 100, which is the limit on an "in" filter.
     * The ID string "null" is used for Topics with null categoryIds.
     *
     * @return the array of IDs
     */
    getAlternativeIds: function() {
        return this._alternativeIds ? this._alternativeIds.split(' ') : [];
    },
    /**
     * Adds to my list of alternative category IDs.
     *
     * @param newAlternativeIds  array of ID strings
     */
    addAlternativeIds: function(newAlternativeIds) {
        this._alternativeIds = this.getAlternativeIds().concat(newAlternativeIds).join(' ');
        this.updateDebugSection();
    },
    /**
     * Adds to my topic count
     *
     * @param n  amount to add
     */
    addToTopicCount: function(n) {
        this._topicCount += n;
        this.updateDebugSection();
    },
    /**
     * Returns the current title of this category.
     *
     * @return the title
     */
    getTitle: function() {
        return this.domNode.getElementsByTagName('input')[0].value;
    }
});

