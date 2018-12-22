dojo.provide('xg.opensocial.embed.moduleBodyAndFooter');
dojo.require('xg.opensocial.embed.requests'); // !used

/**
 * Code to interact with the opensocial container javascript
 */
xg.opensocial.embed.moduleBodyAndFooter = {

    /**
     * Retrive the colors we are recommending the gadget use to match the page
     */
    getSkinningColors: function() {
        var bgColorArray = dojo.style.getBackgroundColor(xg.$$('.xg_opensocial_body')[0]);
        return {bgColor: 'rgb(' + bgColorArray.join(',') + ')' ,
                fontColor: dojo.style.getComputedStyle(xg.$$('.xg_osskin_text')[0], 'color'),
                anchorColor: dojo.style.getComputedStyle(xg.$$('.xg_osskin_link')[0], 'color')};
    },

    /**
     * Creates and calls the container javascript to render a gadget.
     *
     * @param config JS Object containing:
     *   specUrl: gadget URL
     *   domain: current domain
     *   secureToken: token for communicating page -> osoc core -> playground, securely
     *   baseUrl: base path where iframe renderer (ifr) is located
     *   renderUrl: url of the server and port, no path
     *   index: unique string used to interact with a specific gadget on a page with more than one
     */
    renderGadgets: function(config) {
        var skinningColors = xg.opensocial.embed.moduleBodyAndFooter.getSkinningColors();
        var chromeIds = [];
        var gadget = gadgets.container.createGadget({'specUrl': config.url,
                                                   'domain': config.domain,
                                                   'secureToken': config.secureToken,
                                                   'viewParams': config.viewParams,
                                                   'ownerId': config.ownerId,
                                                   'bgColor': skinningColors.bgColor,
                                                   'fontColor': skinningColors.fontColor,
                                                   'anchorColor': skinningColors.anchorColor,
                                                   'width' : '100%',
                                                   'iframeUrl': config.iframeUrl});
        gadget.setServerBase(config.baseUrl);
        gadget.setServerUrl(config.renderUrl);
        gadgets.container.addGadget(gadget);
        chromeIds[gadget.id] = '_opensocial-chrome-' + config.index;
        gadgets.container.setView(config.view);
        gadgets.container.layoutManager.setGadgetChromeIds(chromeIds);
        gadgets.container.renderGadgets();
    }
};
