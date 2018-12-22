dojo.provide('xg.index.feature.add');
dojo.require('dojo.dnd.*');
dojo.require('xg.shared.util');
dojo.require('xg.shared.features');

/**
 * Behavior for the Add Features page.
 */
xg.index.feature.add = {

    /** <li> elements representing all Features */
    availableFeatureList: [],

    /** The DOM node being targeted by the drag-and-drop operation. */
    highlightedTarget: null,

    /** Whether an element was successfully dropped into a drop target. */
    successfulDrop: 0,

    /**
     * Makes note of the initial set of features and their properties, for later
     * updates to this list.
     */
    recordAvailableFeatures: function() {
        xg.index.feature.add.availableFeatureList = [];
        var ul = dojo.byId('xg_add_features_source');
        var lis = ul.getElementsByTagName('li');
        for(var x=0; x<lis.length; x++){
            dojo.style.hide(lis[x]);
            xg.index.feature.add.availableFeatureList.push(lis[x]);
        }
    },

    /**
     * Updates the list of available features on the left to indicate which are
     * now available to be dragged to the page.
     */
    updateFeatureList: function() {
        //  Count embeds in the layout
        var currentEmbeds = {};
        var columns = 3;
        for (var col = 1; col <= columns; col++) {
            dojo.lang.forEach(dojo.byId('xg_layout_column_' + col).getElementsByTagName('li'),
                    function(li) {
                        var name = li.getAttribute('xg_embed_key');
                        if (name) {
                            if (currentEmbeds[name]) {
                                currentEmbeds[name]++;
                            }
                            else {
                                currentEmbeds[name] = 1;
                            }
                        }
                    });
        }
        dojo.lang.forEach(dojo.byId('xg_layout_column_sidebar').getElementsByTagName('li'),
                function(li) {
                    var name = li.getAttribute('xg_embed_key');
                    if (name) {
                        if (currentEmbeds[name]) {
                            currentEmbeds[name]++;
                        }
                        else {
                            currentEmbeds[name] = 1;
                        }
                    }
                });

        //  Clear feature list
        var dl = dojo.byId('xg_add_features_source');
        dojo.dom.removeChildren(dl);

        // z-index set explicitly on both the containing relative block and the
        // contained absolute pop-up in a manner derived from:
        //      http://www.aplus.co.yu/lab/z-pos/index5.php
        // to suit the vagaries of Internet Explorer [bakert 2007-09-07]
        var nodeZindex = 1000;
        var popupZindex = 1001;
        //  Replace features not yet at their limit
        dojo.lang.forEach(xg.index.feature.add.availableFeatureList, function(li) {
            var name = li.getAttribute('xg_embed_key');
            var limit = li.getAttribute('xg_embed_limit');
            var width = li.getAttribute('xg_width_option');
            if (typeof(currentEmbeds[name]) == 'undefined' || currentEmbeds[name] < limit) {
                var node = li.cloneNode(true);
				var subWidgets = node.getElementsByTagName('*');
				for (var i = 0;i<subWidgets.length;i++) {
					if (subWidgets[i].getAttribute('_dojoType')) {
						subWidgets[i].setAttribute('dojoType', subWidgets[i].getAttribute('_dojoType'));
					}
				}
                new dojo.dnd.HtmlDragSource(node, width);
                dojo.dom.insertAtPosition(node, dl, 'last');
                node.style.zIndex = nodeZindex--;
                dojo.lang.forEach(dojo.html.getElementsByClass('context_help_popup', node), function(chp) {
                    chp.style.zIndex = popupZindex--;
                });
            }
        });

        // Now parse the widgets in the list to enable the tooltip help.
        xg.shared.util.parseWidgets(dl);
        //  Show the feature list if we haven't yet
        dojo.html.show(dl);
    },

    /**
     * Called when a feature has been dropped into one of the drop targets in the page layout.
     */
    onDrop: function(evt) {
        xg.index.feature.add.successfulDrop = 1;
        xg.index.feature.add.moveMovableBelowFixed();
        xg.index.feature.add.hideContextHelp();
        xg.index.feature.add.updateFeatureList();
    },

    /**
     * Called when a feature has been dropped into the trash area.
     */
    onDropTrash: function(evt) {
        xg.index.feature.add.successfulDrop = 1;
        var ul = dojo.byId('xg_add_features_trash');
        var lis = ul.getElementsByTagName('li');
        for(var x=0; x<lis.length; x++){
            ul.removeChild(lis[x]);
        }
        xg.index.feature.add.updateFeatureList();
    },

    /**
     * Closes any help bubbles that may be open.
     */
    hideContextHelp: function(evt) {
        var cols = ['xg_layout_column_1', 'xg_layout_column_2', 'xg_layout_column_3',
                'xg_layout_column_sidebar'];
        for (var x=0; x<cols.length; x++) {
            dojo.lang.forEach(dojo.html.getElementsByClass('context_help', dojo.byId(cols[x])), function(node) {
                dojo.style.setVisibility(node, false);
            });
        }
    },

    /**
     * Repositions list items that are out of order.
     */
    moveMovableBelowFixed: function(evt) {
        // If we ever have fixed items in any other column we will need to do this to them, too.
        var sidebar = dojo.byId('xg_layout_column_sidebar');
        while (! xg.index.feature.add.inCorrectOrder(sidebar.getElementsByTagName('li'))) {
            var items = sidebar.getElementsByTagName('li');
            var firstOutOfOrder = xg.index.feature.add.firstOutOfOrder(items);
            var nextElement = dojo.dom.nextElement(firstOutOfOrder);
            if (firstOutOfOrder && nextElement) {
                dojo.dom.removeNode(nextElement);
                dojo.dom.insertBefore(nextElement, firstOutOfOrder);
            }
        }
    },

    /**
     * Returns true if no items are out-of-order.
     *
     * @return  whether all items are in order
     */
    inCorrectOrder: function(items) {
        return(xg.index.feature.add.firstOutOfOrder(items) == null);
    },

    /**
     * Returns the first movable item that is out-of-order. Most movable items are considered
     * out-of-order if placed above a non-movable item.
     *
     * @return  the first list item that is out-of-order, or null if all items are in order
     */
    firstOutOfOrder: function(items) {
        for (var i = 0; i < items.length; i++) {
            if (dojo.html.hasClass(items[i], 'noedit')) { continue; }
            // ads are a special case and can be dragged anywhere except above the login box.
            if (xg.index.feature.add.isFeature(items[i], '_ads') && i != 0) { continue; }
            var fixedBelow = false;
            for (var j = i + 1; j < items.length; j++) {
                if (dojo.html.hasClass(items[j], 'noedit')) { fixedBelow = true; }
            }
            if (! fixedBelow) { return null; }
            return items[i];
        }
        return null;
    },

    /**
     * Returns whether the given element has the given featureName
     *
     * @param element  a DOM node
     * @param featureName  an xg_embed_key
     * @return  whether the featureName matches
     */
    isFeature: function(element, featureName) {
        return (element && (dojo.html.getAttribute(element, 'xg_feature_name') == featureName));
    },

    /**
     * Called when a drag has entered a drop-target zone.
     */
    aroundEnterTarget: function(invocation) {
        var retval = invocation.proceed();
        xg.index.feature.add.highlightedTarget = invocation.object.domNode;
        var sidebar = dojo.byId('xg_layout_column_sidebar');
        if (xg.index.feature.add.highlightedTarget == sidebar) {
            dojo.style.setVisibility(dojo.byId('xg_add_features_allpagesnote'), true);
        }
        if (retval) {
            dojo.html.addClass(invocation.object.domNode, 'drop');
        }
        else {
            dojo.html.addClass(invocation.object.domNode, 'nodrop');
        }
        return retval;
    },

    /**
     * Called when a drag has left a drop-target zone.
     */
    onLeaveTarget: function(evt) {
        dojo.html.removeClass(xg.index.feature.add.highlightedTarget, 'drop');
        dojo.html.removeClass(xg.index.feature.add.highlightedTarget, 'nodrop');
        dojo.style.setVisibility(dojo.byId('xg_add_features_allpagesnote'), false);
    },

    skip: function() {
        // TODO: This does not seem to be called anymore. Delete. [Jon Aquino 2008-04-14]
        var form = dojo.byId('xg_add_features_form');
        form.xg_feature_layout.value = '';
        form.submit();
    },

    /**
     * Submits the form to the given URL.
     *
     * @param url  the endpoint to post to
     * @param evt  event object
     */
    handleLaunchBarSubmit: function(url, evt) {
        dojo.event.browser.stopEvent(evt);
        var form = dojo.byId('xg_add_features_form');
        if (form.successTarget && url) {
            form.successTarget.value = url;
        }
        xg.index.feature.add.submitForm();
    },

    /**
     * Prepares the form for submission, then submits it.
     */
    submitForm: function() {
        //  Serialize the layout object into a field for submission
        var form = dojo.byId('xg_add_features_form');
        form.xg_feature_layout.value = xg.shared.features.jsonizeLayout();
        form.successfulDrop.value = xg.index.feature.add.successfulDrop;

        xg.index.feature.testAddHook();
        form.submit();
    },

    /**
     * Sets up the View All Features link. Call this after the first call to updateFeatureList.
     */
    initViewAllFeaturesLink: function() {
        var visibleSourceFeatureCount = parseInt(dojo.byId('xg_add_features_source').getAttribute('_initialVisibleSourceFeatureCount'), 10);
        var sourceFeatureCount = this.sourceFeatureNames().length;
        // Don't hide link if there's only one to hide [Jon Aquino 2008-04-14]
        if (sourceFeatureCount == visibleSourceFeatureCount + 1) { visibleSourceFeatureCount = sourceFeatureCount; }
        if (sourceFeatureCount > visibleSourceFeatureCount) {
            this.setVisibleSourceFeatureCount(visibleSourceFeatureCount);
            dojo.style.show(dojo.byId('view_all_features_link'));
            dojo.event.connect(dojo.byId('view_all_features_link'), 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                dojo.style.hide(dojo.byId('view_all_features_link'));
                this.showAllSourceFeatures();
            }));
        } else {
            this.showAllSourceFeatures();
        }
    },

    /**
     * Returns the names of the features listed on the left (both shown and hidden).
     *
     * @return an array of xg_embed_key values
     */
    sourceFeatureNames: function() {
        return dojo.lang.map(dojo.byId('xg_add_features_source').getElementsByTagName('li'), function(li) { return dojo.html.getAttribute(li, 'xg_embed_key'); });
    },

    /**
     * Sets the number of features shown on the left.
     *
     * @param visibleSourceFeatureCount  the maximum number of features to display
     */
    setVisibleSourceFeatureCount: function(visibleSourceFeatureCount) {
        var visibleSourceFeatureNames = this.sourceFeatureNames().slice(0, visibleSourceFeatureCount);
        dojo.lang.forEach(dojo.byId('xg_add_features_source').getElementsByTagName('li'), function(li) {
            dojo.style.setShowing(li, dojo.lang.inArray(visibleSourceFeatureNames, dojo.html.getAttribute(li, 'xg_embed_key')));
        });
        dojo.lang.forEach(this.availableFeatureList, function(li) {
            dojo.style.setShowing(li, dojo.lang.inArray(visibleSourceFeatureNames, dojo.html.getAttribute(li, 'xg_embed_key')));
        });
    },

    /**
     * Makes visible all features listed on the left.
     */
    showAllSourceFeatures: function() {
        dojo.lang.forEach(dojo.byId('xg_add_features_source').getElementsByTagName('li'), function(li) {
            dojo.style.show(li);
        });
        dojo.lang.forEach(this.availableFeatureList, function(li) {
            dojo.style.show(li);
        });
    }

};

/** Hook for Squish regression tests */
xg.index.feature.testAddHook = function() { };

xg.addOnRequire(function() {
    xg.index.feature.add.recordAvailableFeatures();
    xg.index.feature.add.updateFeatureList();
    xg.index.feature.add.initViewAllFeaturesLink();

    var widths = {};
    dojo.lang.forEach(xg.index.feature.add.availableFeatureList, function(li) {
        widths[li.getAttribute('xg_embed_key')] = li.getAttribute('xg_width_option');
    });
    for (var col = 1; col <= 3; col++) {
        dojo.lang.forEach(dojo.byId('xg_layout_column_' + col).getElementsByTagName('li'),
                function(li) {
            if (li.className.indexOf('noedit') != -1) { return; }
            if (widths[li.getAttribute('xg_embed_key')]) {
                new dojo.dnd.HtmlDragSource(li, widths[li.getAttribute('xg_embed_key')]);
            }
            else {
                //  Don't know the available widths - constrain to current width
                new dojo.dnd.HtmlDragSource(li, 'w' + (col == 2 ? '2' : '1'));
            }
        });
    }
    dojo.lang.forEach(dojo.byId('xg_layout_column_sidebar').getElementsByTagName('li'),
            function(li) {
        if (li.className.indexOf('noedit') != -1) { return; }
        if (widths[li.getAttribute('xg_embed_key')]) {
            new dojo.dnd.HtmlDragSource(li, widths[li.getAttribute('xg_embed_key')]);
        }
        else {
            //  Don't know the available widths - constrain to current width
            new dojo.dnd.HtmlDragSource(li, 'w1');
        }
    });

    var dt = new dojo.dnd.HtmlDropTarget(dojo.byId('xg_layout_column_1'), ['w1', 'w12']);
    dojo.event.connect(dt, 'onDrop', xg.index.feature.add, 'onDrop');
    //dojo.event.connect(dt, 'onDragOver', xg.index.feature.add, 'onEnterTarget');
    dojo.event.connect('around', dt, 'onDragOver', xg.index.feature.add, 'aroundEnterTarget');
    dojo.event.connect(dt, 'onDragOut', xg.index.feature.add, 'onLeaveTarget');

    dt = new dojo.dnd.HtmlDropTarget(dojo.byId('xg_layout_column_2'), ['w2', 'w12']);
    dojo.event.connect(dt, 'onDrop', xg.index.feature.add, 'onDrop');
    //dojo.event.connect(dt, 'onDragOver', xg.index.feature.add, 'onEnterTarget');
    dojo.event.connect('around', dt, 'onDragOver', xg.index.feature.add, 'aroundEnterTarget');
    dojo.event.connect(dt, 'onDragOut', xg.index.feature.add, 'onLeaveTarget');

    dt = new dojo.dnd.HtmlDropTarget(dojo.byId('xg_layout_column_3'), ['w1', 'w12']);
    dojo.event.connect(dt, 'onDrop', xg.index.feature.add, 'onDrop');
    //dojo.event.connect(dt, 'onDragOver', xg.index.feature.add, 'onEnterTarget');
    dojo.event.connect('around', dt, 'onDragOver', xg.index.feature.add, 'aroundEnterTarget');
    dojo.event.connect(dt, 'onDragOut', xg.index.feature.add, 'onLeaveTarget');

    dt = new dojo.dnd.HtmlDropTarget(dojo.byId('xg_layout_column_sidebar'), ['w1', 'w12']);
    dojo.event.connect(dt, 'onDrop', xg.index.feature.add, 'onDrop');
    //dojo.event.connect(dt, 'onDragOver', xg.index.feature.add, 'onEnterTarget');
    dojo.event.connect('around', dt, 'onDragOver', xg.index.feature.add, 'aroundEnterTarget');
    dojo.event.connect(dt, 'onDragOut', xg.index.feature.add, 'onLeaveTarget');

    dt = new dojo.dnd.HtmlDropTarget(dojo.byId('xg_add_features_trash'), '*');
    dojo.event.connect(dt, 'onDrop', xg.index.feature.add, 'onDropTrash');
    //dojo.event.connect(dt, 'onDragOver', xg.index.feature.add, 'onEnterTarget');
    dojo.event.connect('around', dt, 'onDragOver', xg.index.feature.add, 'aroundEnterTarget');
    dojo.event.connect(dt, 'onDragOut', xg.index.feature.add, 'onLeaveTarget');

    var form = dojo.byId('xg_add_features_form');
    dojo.event.connect(form, 'onsubmit', xg.index.feature.add, 'submitForm');

    // preload mouseover images
    dojo.lang.forEach(['column_drop', 'column_nodrop', 'trash_drop', 'allpagesnote'], function(name){
        var preloader = new Image();
        preloader.src = xg.shared.util.cdn('/xn_resources/widgets/index/gfx/features/' + name + '.gif');
    });
});
