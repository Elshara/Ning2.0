dojo.provide('xg.forum.manage.index');

dojo.require('xg.shared.util');
dojo.require('dojo.lfx.html');
dojo.require('xg.index.util.FormHelper');
dojo.require('dojo.dnd.HtmlDropTarget');

/**
 * Behavior for the Manage Categories page.
 */
xg.forum.manage.index = {
    /**
     * Adds a category editor to the category container.
     *
     * @param fieldsetBefore  (optional) The fieldset after which to insert the new fieldset
     */
    addCategory: function(fieldsetBefore) {
        dojo.style.show(dojo.byId('category_container').parentNode);
        // Set radio-button names in string passed to createNodesFromText, as IE doesn't allow
        // us to change the name. See "NAME Attribute", http://msdn.microsoft.com/workshop/author/dhtml/reference/properties/name_2.asp
        // [Jon Aquino 2007-04-16]
        var n = xg.forum.manage.index.n = xg.forum.manage.index.n ? xg.forum.manage.index.n + 1 : 1;
        var fieldset = dojo.html.createNodesFromText(dojo.byId('category_editor_template').value.replace(/members_can_add_topics_[a-z0-9]*/ig, 'members_can_add_topics_' + n))[0];
        dojo.style.setOpacity(fieldset, 0);
        if (fieldsetBefore) {
            dojo.dom.insertAfter(fieldset, fieldsetBefore);
        } else {
            dojo.byId('category_container').appendChild(fieldset);
        }
        xg.shared.util.fixImagesInIE(fieldset.getElementsByTagName('img'));
        xg.shared.util.parseWidgets(fieldset);
        // BAZ-5468 Need to set this explicitly for Safari 3 even though it's in the template.
        var FIRST_RADIO_BUTTON_POSITION = 1;
        fieldset.getElementsByTagName('input')[FIRST_RADIO_BUTTON_POSITION].checked = true;
        xg.index.util.FormHelper.scrollIntoView(fieldset);
        dojo.lfx.html.fadeIn(fieldset, 500, null, function() {
            fieldset.getElementsByTagName('input')[0].focus();
        }).play();
        this.updateCategoryState();
        this.updateDeleteLinksVisibility();
    },
    /**
     * Removes a category editor
     *
     * @param fieldset  the fieldset element to remove
     */
    removeCategory: function(fieldset) {
        dojo.lfx.html.fadeOut(fieldset, 500, null, dojo.lang.hitch(this, function() {
            fieldset.parentNode.removeChild(fieldset);
            this.updateDeleteLinksVisibility();
            this.updateCategoryState();
        })).play();
    },
    /**
    * Shows or hides delete links on category fragments; link is hidden if a single category is showing, and made visible if more than one category shows.
    */
    updateDeleteLinksVisibility: function() {
        var container = dojo.byId('category_container');
        var fieldSets = dojo.html.getElementsByClass('category', container);
        if (fieldSets.length == 1) {
            var action = container.getElementsByTagName('li')[0];
            dojo.style.hide(action);
        } else {
            var actions = container.getElementsByTagName('li');
            for (var i = 0; i < actions.length; i++) {
                dojo.style.show(actions[i]);
            }
        }
    },
    /**
     * disables or enables the category option depending on whether there are currently > 1 categories
     *
     * @return void
     */
    updateCategoryState: function() {
        var categories = dojo.byId('category_container').getElementsByTagName('fieldset');
        var categoryOption = dojo.byId('categoryStyleOption');
        var latestByCatOption = dojo.byId('latestByCategory');
        if (categories.length > 1) {
            categoryOption.disabled = false;
            latestByCatOption.disabled = false;
            categoryOption.className = 'radio';
            latestByCatOption.className = 'radio';
            categoryOption.parentNode.className = '';
            latestByCatOption.parentNode.className = '';
        } else {
            if (categoryOption.checked == true) {
                dojo.byId('byTimeStyleOption').checked = true;
            }
            categoryOption.disabled = true;
            latestByCatOption.disabled = true;
            categoryOption.className = 'radio disabled';
            latestByCatOption.className = 'radio disabled';
            categoryOption.parentNode.className = 'disabled';
            latestByCatOption.parentNode.className = 'disabled';
        }
    }
};

(function() {
    xg.forum.manage.index.updateCategoryState();
    xg.forum.manage.index.updateDeleteLinksVisibility();
    var dropTarget = new dojo.dnd.HtmlDropTarget(dojo.byId('category_container'), '*');
    dropTarget.createDropIndicator = function() {
        this.dropIndicator = document.createElement("div");
        dojo.html.addClass(this.dropIndicator, 'fieldset_drop_indicator');
        this.dropIndicator.style.left = dojo.style.getAbsoluteX(this.domNode, true) + "px";
    };

    var form = dojo.byId('xg_body').getElementsByTagName('form')[0];
    dojo.event.connect(form, 'onsubmit', function(event) {
        var data = [];
        dojo.lang.forEach(dojo.html.getElementsByClass('category', dojo.byId('category_container')), function(categoryNode) {
            var categoryEditor = dojo.widget.manager.getWidgetByNode(categoryNode);
            data.push(categoryEditor.createCategory());
        });
        form.data.value = dojo.json.serialize(data);
    });

})();
