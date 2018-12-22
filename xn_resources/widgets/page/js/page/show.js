dojo.provide('xg.page.page.show');

(function() {

    xg.page.validate = function() {
        var errors = {}
        if (form.text.value.length == 0) {
            errors['text'] = xg.page.nls.html('pleaseEnterAComment');
        }
        if (form.text.value.length > 4000) {
            errors['text'] = xg.page.nls.html('numberOfCharactersExceedsMaximum', form.text.value.length, 4000);
        }
        return errors;
    };

    var form = dojo.byId('comment-form');
    if (form) {
        dojo.require('xg.index.util.FormHelper');
        xg.index.util.FormHelper.configureValidation(form, xg.page.validate);
    }

}());

function incrementViewCount(pageId) {
    window.setTimeout(dojo.lang.hitch(this, function() {
        dojo.io.bind({
            url     : '/index.php/'+xg.global.currentMozzle+'/page/registershown?xn_out=json',
            content : { id: pageId },
            method  : 'post',
            encoding: 'utf-8',
            load    : dojo.lang.hitch(this, function(type, data, event) {})
        });
    }), 5000);
}
