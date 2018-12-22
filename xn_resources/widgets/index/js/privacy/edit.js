dojo.provide('xg.index.privacy.edit');

dojo.require('xg.shared.util');

/**
 * Behavior for the privacy/edit page.
 */
xg.index.privacy.edit = {
    submitForm: function() {
        xg.index.privacy.edit.savePrivacySettings();
        // This will only have an effect if they request a different privacy level; otherwise it just sends them to the settings saved page.
        xg.index.privacy.edit.setPrivacyLevel();
    },

    savePrivacySettings: function() {
        var f = dojo.byId('xg_privacy_form');
        dojo.io.bind({
            formNode: f,
            method: f.method,
            url: f.action
        });
    },

    // These are set in edit.php where we have access to the PHP API.
    _setPrivacyUrl: '<required>',
    _setPrivacySuccessUrl: '<required>',

    setPrivacyLevel: function(privacy) {
        dojo.io.bind({
            encoding: "utf-8",
            preventCache: true,
            load: xg.index.privacy.edit.setPrivacyLevelProper,
            mimetype: 'text/json',
            url: "/xn/rest/1.0/application:"+ning.CurrentApp.id
        });
    },

    setPrivacyLevelProper: function(type, data, evt) {
        xg.index.privacy.edit.desiredOnlineStatus = data.application.online;
        var desiredPrivacy = null;
        if (dojo.byId('privacyLevelPublic').checked) {
            desiredPrivacy = 'public';
        } else if (dojo.byId('privacyLevelPrivate').checked) {
            desiredPrivacy = 'private';
        }
        dojo.widget.createWidget('BulkActionLink', {
            title: xg.index.nls.text('changeSettings'),
            _url:  xg.index.privacy.edit._setPrivacyUrl + '&privacyLevel=' + desiredPrivacy + '&finalOnlineStatus=' + xg.index.privacy.edit.desiredOnlineStatus,
            _verb: xg.index.nls.text('change'),
            _progressTitle: xg.index.nls.text('changing'),
            _progressMessage: xg.index.nls.text('keepWindowOpenWhileChanging'),
            _successUrl: xg.index.privacy.edit._setPrivacySuccessUrl

        }).execute();
    },

    desiredOnlineStatus: null,

    enablePrivacyOptions: function() {
        var privacyLevelPublic = dojo.byId('privacyLevelPublic');
        var publicSelected = privacyLevelPublic.checked;
        dojo.byId('nonregVisibility_everything').disabled = ! publicSelected;
        dojo.byId('nonregVisibility_homepage').disabled = ! publicSelected;
        if (dojo.byId('nonregVisibility_message')) {
            dojo.byId('nonregVisibility_message').disabled = ! publicSelected;
        }
        dojo.byId('allowJoin_all').disabled = publicSelected;
        dojo.byId('allowJoin_invited').disabled = publicSelected;
    },

    handleLaunchBarSubmit: function(url, evt) {
        dojo.event.browser.stopEvent(evt);
        var form = dojo.byId('xg_privacy_form');
        if (form.successTarget && url) {
            form.successTarget.value = url;
        }
        xg.index.privacy.edit.submitForm();
    },

    // BAZ-8810
    toggleModerateGroups: function() {
        var userGroups = dojo.byId('xj_user_groups_checkbox');
        var moderateGroups = dojo.byId('xj_moderate_groups_checkbox');
        if (userGroups && moderateGroups) {
            moderateGroups.disabled = ! userGroups.checked;
            if (moderateGroups.disabled) { moderateGroups.checked = false; }
        }
    }
};

xg.addOnRequire(function() {
    var f = dojo.byId('xg_privacy_form');
    dojo.event.connect(f.privacyLevelPublic, 'onclick', xg.index.privacy.edit, 'enablePrivacyOptions');
    dojo.event.connect(f.privacyLevelPrivate, 'onclick', xg.index.privacy.edit, 'enablePrivacyOptions');
    dojo.event.connect(dojo.byId('savePrivacySettings'), 'onclick', xg.index.privacy.edit, 'submitForm');
    var userGroups = dojo.byId('xj_user_groups_checkbox');
    if (userGroups) {
        dojo.event.connect(userGroups, 'onclick', xg.index.privacy.edit, 'toggleModerateGroups');
    }
});

xg.shared.util.selectOnClick(dojo.byId('bulk_invitation_url_field'));
