dojo.provide('xg.index.embed.sidebarUserBox');

xg.index.embed.sidebarUserBox = {

    activateAnnouncementLinks: function(div) {
        var anchors = div.getElementsByTagName('a');
        dojo.lang.forEach(anchors, function(a) {
            dojo.event.connect(a, 'onclick',
                    xg.index.embed.sidebarUserBox, 'acknowledgeAnnouncement');
        });
    },

    acknowledgeAnnouncement: function() {
        var div = dojo.byId('xg_announcement_div');
        var annId = div.getAttribute('_annId');
        var ackUrl = div.getAttribute('_ackUrl');
        dojo.io.bind({
            url: ackUrl,
            method: 'post',
            encoding: 'utf-8',
            content: { id: annId },
            preventCache: true,
            mimetype: 'text/javascript'
        });
    }
};

xg.addOnRequire(function() {
    var div = dojo.byId('xg_announcement_div');
    if (div) {
        xg.index.embed.sidebarUserBox.activateAnnouncementLinks(div)
    }
});
