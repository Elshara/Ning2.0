dojo.provide('xg.index.invitation.contactList');

dojo.require('xg.shared.util');
dojo.require('xg.shared.SpamWarning');

/**
 * Behavior for the contactList page.
 */
xg.index.invitation.contactList = {

    /** The div element */
    div: null,

    /** All contacts. */
    contactList: [],

    /** The set of contacts filtered by the current search term. */
    filteredContactList: [],

    /** The URL for processing the Contact List form. */
    createWithContactListUrl: '',

    /** The URL to go to when Cancel is pressed. */
    cancelUrl: '',

    /** The current context: "invite" or "share". */
    inviteOrShare: '',

    /** The number of contacts that are visible and currently selected. */
    visibleSelectedContactCount: 0,

    /** The number of contacts that are visible. */
    visibleContactCount: 0,

    /** SpamWarning variables */
    _spamUrl: '',
    _spamMessageParts: '',

    /**
     * Initializes the behavior.
     */
    initialize: function() {
        this.div = dojo.byId('contact_list_module');
        this.contactList = dojo.json.evalJson(dojo.byId('contact_list').value);
        dojo.lang.forEach(this.contactList, function(contact) { contact.selected = true; });
        this.createWithContactListUrl = this.div.getAttribute('_createWithContactListUrl');
        this.cancelUrl = this.div.getAttribute('_cancelUrl');
        this.inviteOrShare = this.div.getAttribute('_inviteOrShare');
        this._spamMessageParts=this.div.getAttribute('_spamMessageParts');
        this._spamUrl=this.div.getAttribute('_spamUrl');
        this.initializeSearching();
        this.initializeToggleAllCheckbox();
        this.displayContacts();
        this.initializeInviteButton();
        this.initializeCancelButton();
    },

    /**
     * Sets up the search functionality.
     */
    initializeSearching: function() {
        dojo.event.connect(dojo.byId('search_form'), 'onsubmit', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            this.displayContacts();
        }));
    },

    /**
     * Sets up the header checkbox that toggles all the checkboxes.
     */
    initializeToggleAllCheckbox: function() {
        var toggleAllCheckbox = dojo.byId('toggle_all_checkbox');
        dojo.event.connect(toggleAllCheckbox, 'onclick', dojo.lang.hitch(this, function(event) {
            // TODO: Call dojo.event.browser.stopEvent(event) here? [Jon Aquino 2008-04-01]
            var checkboxes = this.getCheckboxes();
            dojo.lang.forEach(checkboxes, dojo.lang.hitch(this, function(checkbox) {
                checkbox.checked = toggleAllCheckbox.checked;
            }));
            this.setVisibleSelectedContactCount(toggleAllCheckbox.checked ? checkboxes.length : 0);
        }));
    },

    /**
     * Updates contact.selected with the checkbox values.
     */
    storeCheckboxValues: function() {
        // BAZ-5508 [Jon Aquino 2007-12-10]
        dojo.lang.forEach(this.getCheckboxes(), dojo.lang.hitch(this, function(checkbox) {
            this.filteredContactList[checkbox.getAttribute('_i')].selected = checkbox.checked;
        }));
    },

    /**
     * Rebuilds the table, filtering contacts by the current search term.
     */
    displayContacts: function() {
        this.storeCheckboxValues();
        dojo.style.hide(dojo.byId('search_form'));
        dojo.style.hide(dojo.byId('contact_section'));
        dojo.style.show(dojo.byId('loading_message'));
        setTimeout(dojo.lang.hitch(this, function() {
            var searchTerm = dojo.string.trim(dojo.byId('search_form').q.value.toLowerCase());
            this.filteredContactList = [];
            var preIe7 = dojo.render.html.ie50 || dojo.render.html.ie55 || dojo.render.html.ie60;
            var strings = [];
            var j = 0;
            this.visibleContactCount = 0;
            var visibleSelectedContactCount = 0;
            strings[j++] = '<table class="members">';
            dojo.lang.forEach(this.contactList, dojo.lang.hitch(this, function(contact) {
                if (searchTerm == '' || contact.name.toLowerCase().indexOf(searchTerm) != -1 || contact.emailAddress.toLowerCase().indexOf(searchTerm) != -1) {
                    this.filteredContactList.push(contact);
                    // Array.join is much faster than + in IE (1s vs. 25s for 5000 contacts) [Jon Aquino 2007-12-04]
                    strings[j++] = '<tr ';
                    // class="alt" causes out-of-memory error in IE6 (BAZ-5523)  [Jon Aquino 2007-12-10]
                    if (! preIe7) { strings[j++] = this.visibleContactCount % 2 == 0 ? 'class="alt"' : ''; }
                    strings[j++] = '><td';
                    strings[j++] = this.visibleContactCount == 0 ? ' style="width:20px"' : '';
                    strings[j++] = '><input _i="';
                    strings[j++] = this.visibleContactCount;
                    strings[j++] = '" type="checkbox" ';
                    if (contact.selected) { strings[j++] = 'checked="checked" '; }
                    strings[j++] = 'onclick="xg.index.invitation.contactList.checkboxClicked(this)" /></td><td><strong>';
                    strings[j++] = dojo.string.escape('html', contact.name);
                    strings[j++] = '</strong></td><td>';
                    strings[j++] = dojo.string.escape('html', contact.emailAddress);
                    strings[j++] = '</td></tr>';
                    if (contact.selected) { visibleSelectedContactCount++; }
                    this.visibleContactCount++;
                }
            }));
            strings[j++] = '</table>';
            if (this.filteredContactList.length == 0) {
                dojo.byId('table_container').innerHTML = '<h4 style="text-align:center;margin:2em 0;">' + xg.index.nls.html('noFriendsFound') + '</h4>';
            } else {
                dojo.byId('table_container').innerHTML = strings.join('');
            }
            this.setVisibleSelectedContactCount(visibleSelectedContactCount);
            dojo.style.show(dojo.byId('search_form'));
            dojo.style.show(dojo.byId('contact_section'));
            dojo.style.hide(dojo.byId('loading_message'));
            dojo.byId('search_controls').className = searchTerm.length > 0 ? '' : 'last-child';
            dojo.style.setDisplay(dojo.byId('search_description'), searchTerm.length > 0);
            dojo.byId('search_description').innerHTML = '<small>' + xg.index.nls.html('showingNFriends', this.visibleContactCount, dojo.string.escape('html', searchTerm)) + '</small>';
            dojo.event.connect(dojo.byId('search_description').getElementsByTagName('a')[0], 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                dojo.byId('search_form').q.value = '';
                this.displayContacts();
            }));
            dojo.byId('search_form').q.focus();
        }), 0);
    },

    /**
     * Called when the user selects a checkbox.
     *
     * @param checkbox  the checkbox input element
     */
    checkboxClicked: function(checkbox) {
        this.setVisibleSelectedContactCount(Math.max(0, this.visibleSelectedContactCount + (checkbox.checked ? 1 : -1)));
    },

    /**
     * Returns the checkboxes in the table.
     *
     * @return the checkbox input elements.
     */
    getCheckboxes: function() {
        var tables = this.div.getElementsByTagName('table');
        return tables.length > 0 ? tables[0].getElementsByTagName('input') : [];
    },

    /**
     * Sets the number of contacts that are visible and currently selected.
     *
     * @param visibleSelectedContactCount  the number of contacts that the user has chosen
     */
    setVisibleSelectedContactCount: function(visibleSelectedContactCount) {
        this.visibleSelectedContactCount = visibleSelectedContactCount;
        dojo.byId('friend_count').innerHTML = this.inviteOrShare == 'invite' ? xg.index.nls.html('invitingNFriends', this.visibleSelectedContactCount) : xg.index.nls.html('sendingMessageToNFriends', this.visibleSelectedContactCount);
        dojo.byId('toggle_all_checkbox').checked = this.visibleSelectedContactCount == this.visibleContactCount;
    },

    /**
     * Sets up the Invite button.
     */
    initializeInviteButton: function() {
        dojo.event.connect(dojo.byId('invite_button'), 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            var visibleSelectedContactList = [];
            dojo.lang.forEach(this.getCheckboxes(), dojo.lang.hitch(this, function(checkbox) {
                if (checkbox.checked) { visibleSelectedContactList.push(this.filteredContactList[checkbox.getAttribute('_i')]); }
            }));
            if (visibleSelectedContactList.length == 0) { return xg.shared.util.alert({ title: xg.index.nls.text('noPeopleSelected'), bodyHtml: xg.index.nls.html('pleaseChoosePeople') }); }
            var title = this.inviteOrShare == 'invite' ? xg.index.nls.text('sendInvitation') : xg.index.nls.text('sendMessage');
            var body = this.inviteOrShare == 'invite' ? xg.index.nls.html('sendInvitationToNFriends', visibleSelectedContactList.length) : xg.index.nls.html('sendMessageToNFriends', visibleSelectedContactList.length);
            var _this = this;
            var maxMsgLength = 200;
            var dlg = xg.shared.util.confirm({
                title: title,
                bodyHtml: ' \
                        <p>' + body + '</p> \
                        <p> \
                            ' + xg.index.nls.html('yourMessageOptional') + '<br /> \
                            <textarea name="message" cols="30" rows="4" style="width:230px"></textarea> \
                            <input type="hidden" name="contactListJson" /> \
                        </p>',
                okButtonText: xg.index.nls.text('send'),
                closeOnlyIfOnOk: true,
                onOk: function(dialog) {
                    var form = dialog.getElementsByTagName('form')[0];
                    if (form.message.value.length > maxMsgLength) {
                        dojo.style.hide(dialog);
                        var dlg = xg.shared.util.alert({
                            title: xg.index.nls.html('error'),
                            bodyHtml: xg.index.nls.html('messageIsTooLong',maxMsgLength)
                        });
                        dojo.event.connect(dlg.getElementsByTagName('input')[0], 'onclick', function() {
                            dojo.style.show(dialog);
                            xg.shared.util.showOverlay();
                        });
                        return;
                    }
                    xg.shared.SpamWarning.checkForSpam( {
                        url: _this._spamUrl,
                        messageParts: _this._spamMessageParts,
                        form: form,
                        onContinue: function () {
                            dojo.style.hide(dialog);
                            form.action = _this.createWithContactListUrl;
                            form.contactListJson.value = dojo.json.serialize(visibleSelectedContactList);
                            form.method = 'post';
                            form.submit();
                        },
                        onBack: function () { dojo.style.show(dialog); },
                        onWarning: function () { dojo.style.hide(dialog); }
                    } );
                }
            });
            xg.shared.util.setAdvisableMaxLength(dlg.getElementsByTagName('textarea')[0], maxMsgLength);
        }));
    },

    /**
     * Sets up the Cancel button.
     */
    initializeCancelButton: function() {
        dojo.event.connect(dojo.byId('cancel_button'), 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            var form = document.createElement('form');
            form.method = 'post';
            form.action = this.cancelUrl;
            form.appendChild(xg.shared.util.createCsrfTokenHiddenInput());
            document.body.appendChild(form);
            form.submit();
        }));
    }

};

xg.index.invitation.contactList.initialize();
