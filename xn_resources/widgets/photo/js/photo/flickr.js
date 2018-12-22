dojo.provide('xg.photo.photo.flickr');

function toggleFlickrNotification(input) {
    var toggleValue = 'N';
    if (input.checked == false) {
        toggleValue = 'Y';
    }
    dojo.io.bind({
        url: '/main/flickr/setNotification',
        method: 'post',
        mimetype: 'text/json',
        encoding: 'utf-8',
        content: { notification: toggleValue },
        load: function(type, data, evt) {
                //
        }
    });
}