dojo.provide('xg.photo.photo.show');

/**
 * Behavior for the photo detail page.
 */
xg.photo.photo.show = {

    /**
     * Increments the view count for the specified photo.
     *
     * @param photoId  content ID of the Photo
     */
    incrementViewCount: function(photoId) {
        window.setTimeout(dojo.lang.hitch(this, function() {
            dojo.io.bind({
                url: '/index.php/'+xg.global.currentMozzle+'/photo/registershown',
                content: { id: photoId },
                method: 'post',
                encoding: 'utf-8',
                load: dojo.lang.hitch(this, function(type, data, event) {})
            });
        }), 5000);
    }
};
