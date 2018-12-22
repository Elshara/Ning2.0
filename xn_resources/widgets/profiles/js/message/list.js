dojo.provide('xg.profiles.message.list');

dojo.require('xg.shared.CountUpdater');
dojo.require('xg.shared.util');
dojo.require('xg.shared.CountUpdater');
/**
 * Behavior for the mailbox page
 */
xg.profiles.message.list = {

    // dom node of the xj_list_body container
    listBodyNode: null,
    // dom node of the mailbox form
    formNode: null,

    /**
     * Sets up this object. May take a few seconds, so delay it as long as possible.
     */
    setup: function() {
        this.listBodyNode = dojo.byId('xj_list_body');
        this.formNode = dojo.byId('xj_mailbox');

        // if there are no messages in the folder, the form and other navigation components are not displayed
        if (this.formNode) {
            // connect selectAll, selectNone, selectRead, selectUnread links
            dojo.event.connect(dojo.byId('selectAll'), 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                this.selectAll();
            }));
            dojo.event.connect(dojo.byId('selectNone'), 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                this.selectNone();
            }));
            if (dojo.byId('selectRead'))
                dojo.event.connect(dojo.byId('selectRead'), 'onclick', dojo.lang.hitch(this, function(event) {
                    dojo.event.browser.stopEvent(event);
                    this.selectRead();
                }));
            if (dojo.byId('selectUnread'))
                dojo.event.connect(dojo.byId('selectUnread'), 'onclick', dojo.lang.hitch(this, function(event) {
                    dojo.event.browser.stopEvent(event);
                    this.selectUnread();
                }));
    
            // connect actions dropdown
            dojo.event.connect(dojo.byId('actions'), 'onchange', dojo.lang.hitch(this, function(event) {
                var actionsNode = dojo.byId('actions');
                var action = actionsNode.options[actionsNode.selectedIndex];
                var ids = this.getSelectedMessageIds();

                // any messages selected?
                var statusContainer = dojo.byId('xj_error_container');
                var innerContainer = dojo.byId('xj_error_inner_container');
                var statusSpan = dojo.byId('xj_error_span');
                if (ids.length < 1) {
                    if (statusSpan && statusContainer && innerContainer) {
                        this.setContainerStatusClass(innerContainer, 'errordesc');
                        statusSpan.innerHTML = xg.profiles.nls.html('selectAtLeastOneMessage');
                        dojo.html.show(statusContainer);
                    }
                    dojo.byId('actions').selectedIndex = 0;
                    return;
                }

                // hide xj_error_container
                if (statusContainer) { dojo.html.hide(statusContainer); }

                // does the action require confirmation?
                if (action.getAttribute('_confirm')) {
                    xg.shared.util.confirm({
                        bodyHtml: '<p>' + xg.profiles.nls.html('bulkConfirm_' + action.value) + '</p>',
                        onOk: dojo.lang.hitch(this, this.bulkActionConfirmed),
                        onCancel: dojo.lang.hitch(this, this.bulkActionCancelled)
                    });
                } else {
                    // no confirmation is required
                    if (action.value && (action.value.length > 0))
                        this.submitBatchAction(action.value, ids);
                }
            }));
        }
    },

    /**
     * called when a bulk action requiring confirmation is confirmed
     */
    bulkActionConfirmed: function() {
        var actionsNode = dojo.byId('actions');
        var action = actionsNode.options[actionsNode.selectedIndex];
        var ids = this.getSelectedMessageIds();

        this.submitBatchAction(action.value, ids);
    },

    /**
     * called when a bulk action requiring confirmation is cancelled
     */
    bulkActionCancelled: function() {
        var actions = dojo.byId('actions');
        if (actions) {
            // reset actions drop-down
            actions.selectedIndex = 0;
        }
    },

    /**
     * sets a container's status class of: errordesc, notification, success
     */
    setContainerStatusClass: function(node, status) {
        if (node) {
            dojo.html.removeClass(node, 'errordesc');
            dojo.html.removeClass(node, 'notification');
            dojo.html.removeClass(node, 'success');
            dojo.html.addClass(node, status);
        }
    },

    /**
     * Submits a batch of message ids and desired action; all actions use message ids
     * except blockSender, which uses sender screennames.  Updates the message list
     * body and pagination body as needed
     *
     * @param action string     the action to perform (markRead, markUnread, archive, blockSender, delete)
     */
    submitBatchAction: function(action, ids) {
        var page = this.formNode.getAttribute('_page');
        var folder = this.formNode.getAttribute('_folder');
        var actionUrl = this.formNode.getAttribute('_actionurl');
        var screenNames = (action == "blockSender") ?
                        this.getSelectedMessageSenderScreenNames() :
                        [];
        var selectedUnreadMessageCount = this.getSelectedUnreadMessageCount();
        var selectedReadMessageCount = this.getSelectedReadMessageCount();
        var statusContainer = dojo.byId('xj_error_container');
        var innerContainer = dojo.byId('xj_error_inner_container');
        var statusSpan = dojo.byId('xj_error_span');

        // show spinner
        dojo.byId('xg_spinner').style.visibility = '';

        // disable action drop down while performing this request
        dojo.byId('actions').disabled = true;
        dojo.io.bind({
            url: actionUrl,
            method: 'post',
            content: { page: page, folder: folder, ids: dojo.json.serialize(ids), screenNames: dojo.json.serialize(screenNames), action: action },
            preventCache: true,
            mimetype: 'text/json',
            encoding: 'utf-8',
            load: dojo.lang.hitch(this, function(type, data, event) {
                var hasError = false;
                if (data) {
                    if ('listBodyHTML' in data) {
                        // update list body and pagination body
                        this.listBodyNode.innerHTML = data.listBodyHTML;

                        // display success banner for blocking as there's no other visual cue of success (BAZ-8051) [ywh 2008-07-29]
                        if (action == 'blockSender' && statusSpan && statusContainer && innerContainer) {
                            if ('warning' in data) {
                                this.setContainerStatusClass(innerContainer, 'notification');
                                statusSpan.innerHTML = data.warning;
                                dojo.html.show(statusContainer);
                            } else if ('error' in data) {
                                this.setContainerStatusClass(innerContainer, 'errordesc');
                                statusSpan.innerHTML = data.error;
                                dojo.html.show(statusContainer);
                            } else {
                                this.setContainerStatusClass(innerContainer, 'success');
                                statusSpan.innerHTML = xg.profiles.nls.html('selectedSendersBlocked', screenNames.length);
                                dojo.html.show(statusContainer);
                            }
                        }
                    } else if ('refreshUrl' in data) {
                        window.location.href = data.refreshUrl;
                    } else {
                        // error occurred
                        if (statusSpan && statusContainer && innerContainer) {
                            this.setContainerStatusClass(innerContainer, 'errordesc');
                            statusSpan.innerHTML = xg.profiles.nls.html('unableToCompleteAction');
                            dojo.html.show(statusContainer);
                        }

                        hasError = true;
                    }
                }

                // reset actions drop-down
                var actions = dojo.byId('actions');
                if (actions) {
                    actions.selectedIndex = 0;
                    actions.disabled = false;
    
                    // hide spinner
                    dojo.byId('xg_spinner').style.visibility = 'hidden';
                }

                // update inbox unread message count and reinitialize js events
                if (! hasError) {
                    // update inbox unread message count
                    this.updateInboxUnreadMessageCount(action, selectedUnreadMessageCount, selectedReadMessageCount);

                    // reinitialize js events
                    this.setup();
                }
            })
        });
    },

    /**
     * for bulk actions, dynamically update the inbox unread message count.
     *
     * @param action string  the bulk action that was performed
     * @param selectedUnreadMessageCount integer  the number of unread messages in the original bulk action selection
     * @param selectedReadMessageCount integer  the number of read messages in the original bulk action selection
     */
    updateInboxUnreadMessageCount: function(action, selectedUnreadMessageCount, selectedReadMessageCount) {
        var isInbox = this.formNode.getAttribute('_isinbox') == '1';
        var isArchive = this.formNode.getAttribute('_isarchive') == '1';
        var isAlerts = this.formNode.getAttribute('_isalerts') == '1';

        if (isInbox) {
            /**
             * on the inbox, archiving, deleting or marking as read any unread messages
             * should decrease the inbox unread message count.  marking read messages
             * as unread should increase the inbox unread message count.
             */
            if ((action == 'archive') || (action == 'markRead') || (action == 'delete')) {
                xg.shared.CountUpdater.decrement('unreadmessages', selectedUnreadMessageCount);
            } else if (action == 'markUnread') {
                xg.shared.CountUpdater.increment('unreadmessages', selectedReadMessageCount);
            }
        } else if (isArchive) {
            /**
             * on the archive folder, moving unread messages to the inbox should increase
             * the inbox unread message count.
             */
            if (action == 'inbox') {
                xg.shared.CountUpdater.increment('unreadmessages', selectedUnreadMessageCount);
            }
        } else if (isAlerts) {
            if ((action == 'archive') || (action == 'markRead') || (action == 'delete')) {
                xg.shared.CountUpdater.decrement('unreadalerts', selectedUnreadMessageCount);
            } else if (action == 'markUnread') {
                xg.shared.CountUpdater.increment('unreadalerts', selectedReadMessageCount);
            }
        }
    },

    /**
     * Returns an array of the unique sender screennames of all selected messages
     *
     * @returns array  array of sender screennames of all selected messages
     */
    getSelectedMessageSenderScreenNames: function() {
        var names = new Array();
        var namesHash = {};
        dojo.lang.forEach(this.formNode.getElementsByTagName('input'), function(input) {
            if (input.checked) {
                namesHash[input.getAttribute('_sender')] = 1;
            }
        });
        for (var name in namesHash) {
            names.push(name);
        }
        return names;
    },

    /**
     * Returns the number of selected messages that are unread
     */
    getSelectedUnreadMessageCount: function() {
        return x$('input.unread:checked').length;
    },

    /**
     * Returns the number of selected messages that are read
     */
    getSelectedReadMessageCount: function() {
        return x$('input:checked').length - this.getSelectedUnreadMessageCount();
    },

    /**
     * Returns an array of ids or sender screennames of all selected messages
     *
     * @returns array   array of ids or sender screennames of all selected messages
     */
    getSelectedMessageIds: function() {
        var ids = new Array();
        var names = {};
        dojo.lang.forEach(this.formNode.getElementsByTagName('input'), function(input) {
            if (input.checked) {
                ids.push(input.id);
            }
        });
        return ids;
    },

    /**
     * Selects/unselects all messages on the current page
     *
     * @param select boolean    true to select, false to unselect
     */
    selectAllOrNone: function(select) {
        dojo.lang.forEach(this.formNode.getElementsByTagName('input'), function(input) {
            input.checked = select;
        });
    },

    /**
     * Selects all messages on the current page
     */
    selectAll: function() {
        this.selectAllOrNone(true);
    },

    /**
     * Unselects all messages on the current page
     */
    selectNone: function() {
        this.selectAllOrNone(false);
    },

    /**
     * Selects all read/unread messages on the current page
     *
     * @param read boolean  true to select read, false to select unread
     */
    selectReadOrUnread: function(read) {
        dojo.lang.forEach(this.formNode.getElementsByTagName('input'), function(input) {
            input.checked = read ^ dojo.html.hasClass(input, 'unread');
        });
    },

    /**
     * Selects all read messages on the current page
     */
    selectRead: function() {
        this.selectReadOrUnread(true);
    },

    /**
     * Selects all unread messages on the current page
     */
    selectUnread: function() {
        this.selectReadOrUnread(false);
    }

};

xg.profiles.message.list.setup();
