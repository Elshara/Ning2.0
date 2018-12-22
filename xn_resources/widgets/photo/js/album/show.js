dojo.provide('xg.photo.album.show');

function incrementViewCount(albumId) {
    window.setTimeout(dojo.lang.hitch(this, function() {
        dojo.io.bind({
            url     : '/index.php/'+xg.global.currentMozzle+'/album/registershown',
            content : { id: albumId },
            method  : 'post',
            encoding: 'utf-8',
            load    : dojo.lang.hitch(this, function(type, data, event) {})
        });
    }), 3000);
}