dojo.provide('xg.index.membership.list');

xg.index.membership.list = {
    setCheckboxes: function(form, value) {
        dojo.lang.forEach(form.elements, function(elt) {
            if (elt.type == 'checkbox') {
                elt.checked = value;
            }
        });
    },

    submitWithOp: function(operation, formId) {
        // The "profiles/profile/showPending" page uses this function
        // but specifies alternate formIds
        if (! formId) {
            formId = 'xg_member_form';
        }
        var form = dojo.byId(formId);
        form.operation.value = operation;
        form.submit();
    },

    /**
     * Extracts the usernames that the user has selected.
     * Currently works only on the Members tab and the Pending tab
     *
     * @param array  the usernames
     */
    selectedUsernames: function() {
        var selectedUsernames = [];
        var regex = /^(user|inv)_/;
        dojo.lang.forEach(dojo.byId('xg_member_form').getElementsByTagName('input'), function(input) {
            if (input.type != 'checkbox' || ! input.checked) { return; }
            if (input.name.match(regex)) { selectedUsernames.push(input.name.replace(regex, '')); }
            if (input.name == 'selectedIds[]') { selectedUsernames.push(input.value); }  // group members [Jon Aquino 2007-05-01]
        });
        return selectedUsernames;
    }
};

xg.addOnRequire(function() {
    // Alter the Ban button BulkActionLink (or BulkActionLinkWithCheckbox, in the case of groups)
    // to make it operate on the first name, followed by the second name, etc. [Jon Aquino 2007-05-01]
    if (! dojo.byId('ban_button')) { return; }
    var banButtonWidget = dojo.widget.manager.getWidgetByNode(dojo.byId('ban_button'));
    banButtonWidget.originalDoBulkAction = banButtonWidget.doBulkAction;
    banButtonWidget.doBulkAction = function(counter) {
        if (counter == 0) { this.selectedUsernames = xg.index.membership.list.selectedUsernames(); }
        if (this.selectedUsernames.length == 0) { return this.success(); }
        banButtonWidget.originalDoBulkAction(counter);
    };
    banButtonWidget.getPostContent = function(counter) {
        return { user: this.selectedUsernames[0] };
    };
    banButtonWidget.isDone = function(contentRemaining) {
        if (contentRemaining > 0) { return false; }
        this.selectedUsernames.shift();
        return this.selectedUsernames.length == 0;
    };
});
