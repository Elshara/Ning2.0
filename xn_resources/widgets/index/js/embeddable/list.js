dojo.provide('xg.index.embeddable.list');

dojo.require('dojo.lfx.*');

xg.index.embeddable.list = {
    updateIndividualBadge: function(updateUrl) {
        var fieldset = dojo.byId('xg_individual_badge_fieldset');
        var inputs = fieldset.getElementsByTagName('input');
        var customTextInput = inputs[0];
        var embedCodeInput = inputs[1];
        var containers = dojo.html.getElementsByClass('badge-container', fieldset);
        var previewDiv = (containers.length == 0 ? fieldset.getElementsByTagName('div')[1] : containers[0]);
        dojo.io.bind({
            url: updateUrl + '&customText=' + encodeURIComponent(customTextInput.value),
            preventCache: true,
            sync: true,
            encoding: 'utf-8',
            mimetype: 'text/javascript',
            load: dojo.lang.hitch(this, function(type, data, event){
                embedCodeInput.value = data.embedCode;
                previewDiv.innerHTML = data.previewEmbedCode;
				dojo.lfx.highlight(embedCodeInput, /*#ff6*/[255,255,102], 300).play(600);
            })
        });
    },
    
    postToMySpace: function(T, C, U, L) {
        var targetUrl = 'http://www.myspace.com/index.cfm?fuseaction=postto&' + 't=' + encodeURIComponent(T)
        + '&c=' + encodeURIComponent(C) + '&u=' + encodeURIComponent(U) + '&l=' + L;
        window.open(targetUrl);
    },
    
    postToFacebook: function(fbUrl) {
        u=location.href;
        window.open(fbUrl,'sharer','toolbar=0,status=0,width=626,height=436');
        return false;
    }
};
