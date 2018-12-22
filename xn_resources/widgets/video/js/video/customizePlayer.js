dojo.provide('xg.video.video.customizePlayer');

(function() {

    var isHeaderLogoTabSelected = function() { return dojo.html.hasClass('header_logo_tab', 'this'); };
    var isWatermarkLogoTabSelected = function() { return dojo.html.hasClass('watermark_logo_tab', 'this'); };

    var form = dojo.byId('form_section').getElementsByTagName('form')[0];

    dojo.event.connect(form, 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        if (isHeaderLogoTabSelected()) {
            dojo.dom.removeNode(dojo.byId('watermark_logo_section').getElementsByTagName('input')[0]);
        } else {
            dojo.dom.removeNode(dojo.byId('header_logo_section').getElementsByTagName('input')[0]);
        }
        form.submit();
    });

}());
