dojo.provide('xg.photo.photo.editMultiple');

dojo.require('xg.index.util.FormHelper');

(function() {
    var validate = function() {
        var errors = {}
        for (var idx = 0; idx < form.elements.length; idx++) {
            var element = form.elements[idx];
            var name    = element.name;

            if (name && name.match(/description$/)) {
                if (element.value.length > 4000) {
                    errors[name] = xg.photo.nls.html('numberOfCharactersExceedsMaximum', element.value.length, 4000);
                }
            }
        }
        return errors;
    };
    var form = dojo.byId('xg_body').getElementsByTagName('form')[0];
    xg.index.util.FormHelper.configureValidation(form, validate);
    dojo.event.connect(form, 'onsubmit', function(event) {
        if (!dojo.lang.isEmpty(validate())) {
            return;
        }
    });
    var photoNode = function(idx, suffix) {
        return dojo.byId('photo-' + idx + '-' + suffix);
    }
    var applyToAllButton = dojo.byId('apply_to_all_button');
    if (applyToAllButton) {
        dojo.event.connect(applyToAllButton, 'onclick', function(event) {
            dojo.event.browser.stopEvent(event);
            var photoCount = parseInt(applyToAllButton.getAttribute('_photoCount'), 10);
            for (var i = 1; i < photoCount; i++) {
                photoNode(i, 'title').value = photoNode(0, 'title').value;
                photoNode(i, 'description').value = photoNode(0, 'description').value;
                photoNode(i, 'tags').value = photoNode(0, 'tags').value;
                photoNode(i, 'location').value = photoNode(0, 'location').value;
                photoNode(i, 'latInput').value = photoNode(0, 'latInput').value;
                photoNode(i, 'lngInput').value = photoNode(0, 'lngInput').value;
                photoNode(i, 'zoomLevelInput').value = photoNode(0, 'zoomLevelInput').value;
                photoNode(i, 'visibilityAll').checked = photoNode(0, 'visibilityAll').checked;
                photoNode(i, 'visibilityFriends').checked = photoNode(0, 'visibilityFriends').checked;
                photoNode(i, 'visibilityMe').checked = photoNode(0, 'visibilityMe').checked;
            }
            var mapItLinks = dojo.widget.manager.getWidgetsByType('MapItLink');
            if (mapItLinks.length > 1 && mapItLinks[0].getLocationType() == 'latlng') {
                for (var i = 1; i < mapItLinks.length; i++) {
                    mapItLinks[i].setCoordinates(new GLatLng(mapItLinks[0].getLatitude(), mapItLinks[0].getLongitude()), mapItLinks[0].getZoom());
                }
            }
        });
    }

}());
