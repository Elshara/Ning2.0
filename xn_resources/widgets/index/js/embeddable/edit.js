dojo.provide('xg.index.embeddable.edit');

xg.index.embeddable.edit = {
	initialized: 0,

    updateBothPreviews: function() {
		if (!this.initialized) return;
        this.updateBadgePreview();
        this.updatePlayerPreview();
    },

    updateBadgePreview: function() {
		if (!this.initialized) return;
        var form = dojo.byId('xg_player_cust_form');

        // Build badge-specific arguments for preview update URL
        if (form.badgeBranding[0].checked) {
            var addlParams = '&fgColor=' + form.badgeFgColor.value.match('[0-9A-F]+')
                    + '&fgImage=none';
        } else if (this.radioValue(form.badgeFgImage_action) == 'remove') {
			var addlParams = '&fgImage=none&fgColor=none';
        } else if (form.badgeFgImage_currentUrl) {
            var addlParams = '&fgImage=' + encodeURIComponent(form.badgeFgImage_currentUrl.value)
                    + '&fgColor=none';
        }

        this.updatePreviewSection(dojo.byId('xg_badge_preview'), addlParams);
    },

    updatePlayerPreview: function() {
		if (!this.initialized) return;
        var form = dojo.byId('xg_player_cust_form');

        // Build badge-specific arguments for preview update URL
        currentBranding = this.radioValue(form.playerBranding);
        if (currentBranding == 'logo' && form.playerLogoImage_currentUrl && this.radioValue(form.playerLogoImage_action) != 'remove') {
            var addlParams = '&brand=logo&logoImage=' + encodeURIComponent(form.playerLogoImage_currentUrl.value);
        } else if (currentBranding == 'name') {
            var addlParams = '&brand=name';
        } else {
            var addlParams = '&brand=none';
        }

        this.updatePreviewSection(dojo.byId('xg_player_preview'), addlParams);
    },

    //  update the flash preview in the specified section
    updatePreviewSection: function(previewDiv, addlParams) {
        var form = dojo.byId('xg_player_cust_form');

        this.checkForNewImage();
        //  Don't continue updating if we're submitting...
        if (form.submitAction.value == 'preview') {
            return;
        }

        //  Update preview div
        var previewSelect = previewDiv.getElementsByTagName('select')[0];
        var previewOption = previewSelect.options[previewSelect.selectedIndex];
        var embedDiv = previewDiv.getElementsByTagName('div')[0];
        var updateUrl = previewOption.getAttribute('_url') + '&xn_out=htmljson'
                + '&bgColor=' + form.bgColor.value.match('[0-9A-F]+');
        if (addlParams) {
            updateUrl += addlParams;
        }
        if (this.radioValue(form.bgImage_action) == 'keep') {
            updateUrl += '&bgImage=' + encodeURIComponent(form.bgImage_currentUrl.value);
        } else {
            updateUrl += '&bgImage=none';
        }
        dojo.io.bind({
            url: updateUrl,
            preventCache: true,
            encoding: 'utf-8',
            mimetype: 'text/javascript',
            load: dojo.lang.hitch(this, function(type, data, event){
                embedDiv.innerHTML = data.html;
            })
        });
    },

    checkForNewImage: function() {
        dojo.lang.forEach(dojo.byId('xg_body').getElementsByTagName('input'), function(input) {
            if (input.type == 'radio' && input.name.match('_action$')
                    && input.value == 'add' && input.checked) {
                var parentRadioButtonLi = dojo.html.getFirstAncestorByTag(input.parentNode.parentNode, 'li');
                if (parentRadioButtonLi) {
                    var parentRadioButton = parentRadioButtonLi.getElementsByTagName('input')[0];
                    parentRadioButton.checked = true;
                }
                xg.index.embeddable.edit.submitForPreview();
            }
        });
    },

    submitForPreview: function() {
        var form = dojo.byId('xg_player_cust_form');
        form.submitAction.value = 'preview';
        form.submit();
    },

    radioValue: function(buttonSet) {
        for (var n = 0; n < buttonSet.length; n++) {
            if (buttonSet[n].checked) {
                return buttonSet[n].value;
            }
        }
    }

};
xg.addOnRequire(function() {
	dojo.byId('xj_badge_preview_refresh').onclick = function() { xg.index.embeddable.edit.updateBadgePreview(); return false; }
	dojo.byId('xj_player_preview_refresh').onclick = function() { xg.index.embeddable.edit.updatePlayerPreview(); return false; }
	xg.index.embeddable.edit.initialized = 1;
	xg.index.embeddable.edit.updateBothPreviews();
});

