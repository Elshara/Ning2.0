dojo.provide('xg.index.admin.manage');

dojo.require('xg.shared.util');

xg.index.admin.manage = {
    onlineHtml: '<img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/manage/16/online.gif') + '" alt="' + xg.index.nls.html('thisSiteIsOnline') + '" /> '
            + xg.index.nls.html('onlineSiteCanBeViewed')
            + '<small>(<a href="javascript:void(0)">' + xg.index.nls.html('takeOffline') + '</a>)</small>',
    offlineHtml: '<img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/manage/16/offline.gif') + '" alt="' + xg.index.nls.html('thisSiteIsOffline') + '" /> '
            + xg.index.nls.html('offlineOnlyYouCanView')
            + '<small>(<a href="javascript:void(0)">' + xg.index.nls.html('takeOnline') + '</a>)</small>',
    briefOnlineHtml: xg.index.nls.html('online')
                    + ' <small>(<a href="javascript:void(0)">' + xg.index.nls.html('takeOffline') + '</a>)</small>',
    briefOfflineHtml: xg.index.nls.html('offline')
                    + ' <small>(<a href="javascript:void(0)">' + xg.index.nls.html('takeOnline') + '</a>)</small>',

    displayOnline: function(evt) {
        var statusContainer = dojo.byId('xg_manage_online_status_container');
        var statusLi = dojo.byId('xg_manage_online_status');
        if (statusContainer) {
            dojo.html.hide(statusContainer);  
            statusLi.innerHTML = this.onlineHtml;
        } else {
            statusLi.innerHTML = this.briefOnlineHtml;
        }
        var anchor = statusLi.getElementsByTagName("a")[0];
        dojo.event.connect(anchor, "onclick", this, "takeOffline");
        if (statusContainer) {
            dojo.html.show(statusContainer);    
        } else {
            statusLi.className = 'network-online network-status';
        }
    },

    takeOnline: function(evt) {
        dojo.event.browser.stopEvent(evt);
        this.displayOnline();
        dojo.io.bind({
            url: "/xn/rest/1.0/application:"+ning.CurrentApp.id+"?xn_method=PUT",
            preventCache: true,
            method: "POST",
            mimetype: "text/json",
            encoding: "utf-8",
            content:  {
                application_online: true
            }
        });
    },

    displayOffline: function(evt) {
        var statusContainer = dojo.byId('xg_manage_online_status_container');
        var statusLi = dojo.byId('xg_manage_online_status');
        if (statusContainer) {
            dojo.html.hide(statusContainer);
            statusLi.innerHTML = this.offlineHtml;
        } else {
            statusLi.innerHTML = this.briefOfflineHtml;
        }
        var anchor = statusLi.getElementsByTagName("a")[0];
        dojo.event.connect(anchor, "onclick", this, "takeOnline");
        if (statusContainer) {
            dojo.html.show(statusContainer);    
        } else {
            statusLi.className = 'network-offline network-status';
        }
    },

    takeOffline: function(evt) {
        dojo.event.browser.stopEvent(evt);
        this.displayOffline();
        dojo.io.bind({
            url: "/xn/rest/1.0/application:"+ning.CurrentApp.id+"?xn_method=PUT",
            preventCache: true,
            method: "POST",
            mimetype: "text/json",
            encoding: "utf-8",
            content:  {
                application_online: false
            }
        });
    }
};

xg.addOnRequire(function() {
    if (dojo.byId('xg_manage_online_status')) {
        if (ning.CurrentApp.online) {
            xg.index.admin.manage.displayOnline();
        }
        else {
            xg.index.admin.manage.displayOffline();
        }
    } 
});
