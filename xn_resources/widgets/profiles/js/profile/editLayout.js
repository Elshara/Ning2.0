dojo.provide('xg.profiles.profile.editLayout');

dojo.require('xg.shared.features');

(function() {

    /* TODO: We could put this in the page's HTML in order to do the minimum on page load [Thomas David Baker 2008-05-23]  */
    $('.movable > div.xg_module_head').addClass('draggable');
    $('.movable > div.xg_module_head h2').addClass('draggable');

    //on hover drag handles for modules
    $('.movable > div.xg_module_head').hover(function() {
        $(this).siblings('.xg_handle').show();
    }, function() {
        $(this).siblings('.xg_handle').hide();
    });
    $('.no_drag > div.xg_module_head').hover(function() {
        $(this).siblings('.xg_handle').show();
    }, function() {
        $(this).siblings('.xg_handle').hide();
    });

    /**
     * If the moved module contains content that must be resized on move, call
     * the attached updateEmbed method via AJAX.  The updateEmbed method must
     * set ui.item.css('visibility', '') in the data ready handler since this is
     * an asynchronous call.  If the module contents does not need to be resized
     * we just restore visibility to the module div.
     *
     * @param e     jQuery.event Object   The drop event
     * @param ui    jQuery.ui Object      The ui object which makes the callback
     */
    var updateModuleEmbedAndShow = function(e, ui) {
        //TODO: yuck, is there a way to call updateEmbed without grabbing the dojo widget? [ywh 2008-05-23]
        var widget = dojo.widget.manager.getWidgetByNode(ui.item[0]);

        if (widget && ('updateEmbed' in widget) && (typeof(widget.updateEmbed) == 'function')) {
            //TODO: short circuit if embed was not moved outside its original container [ywh 2008-05-23]
            // for embed modules, we delay showing the div until the ajax update completes
            widget.updateEmbed(ui);
        } else {
            // show the div straight away in all other cases
            ui.item.css('visibility', '');
        }
    }

    var dragHelper = function(e, ui) {
        /* TODO: We could pregenerate these in the page's HTML and just 'show' them at this point. [Thomas David Baker 208-05-23] */
        var title = $(ui).find('h2:first').text();
        var no_cross_container = ($(ui).hasClass('no_cross_container') ? no_cross_container = 'no_cross_container' : '');
        return $(document.createElement('div')).addClass('xg_module').addClass(no_cross_container).html('<div class="relative sortable-inner">' + title + '</div>');
    };

    var saveLayout = function(e, ui) {
        var layout = xg.shared.features.jsonizeLayout();
        if (layout != xg.shared.features.currentLayout) {
            xg.shared.features.currentLayout = layout;
            dojo.io.bind({
                url: "/profiles/profile/saveLayout",
                preventCache: true,
                method: "POST",
                mimetype: "text/json",
                encoding: "utf-8",
                content: { newLayout: layout, userName: $('#xg_layout').attr('userName') },
                load: saveLayoutResponse,
                error: saveLayoutError
            });
        }
    };

    var saveLayoutResponse = function(type, data, event, opts) {
        if (data && data.result && data.result == 'success') {
            $('#xg_layout').attr('iteration', data.iteration);
        } else {
            saveLayoutError(type, data, event);
        }
    };

    var dialog = dojo.html.createNodesFromText(dojo.string.trim('\
        <div class="xg_floating_module"> \
            <div class="xg_floating_container xg_module"> \
                <div class="xg_module_head"> \
                    <h2>' + xg.profiles.nls.html('networkError') + '</h2> \
                </div> \
                <div class="xg_module_body"> \
                    <p>' + xg.profiles.nls.html('wereSorry') + '</p> \
                </div> \
            </div> \
        </div>'))[0];

    
    var saveLayoutError = function(type, data, event) {
        xg.shared.util.showOverlay();
        document.body.appendChild(dialog);
        dojo.html.show(dialog);
    }
    
    var els = ['div#xg_layout_column_1', 'div#xg_layout_column_2'];
    var $els = $(els.toString());

    $els.sortable({
        items: '> div.sortable',
        handle: '.draggable',
        cursorAt: { top: 62, left: 90 },
        scroll: true,
        revert: false,
        moduleMargin: '5',
        helper: dragHelper,
        distance: '0',
        appendTo: '#xg_body',
        placeholder: 'placeholder',

        // we are disabling hiding/showing of the div in ui,sortable
        // because we want to control it (BAZ-7706) [ywh 2008-05-23]
        hideElement: false,

        dragOnEmpty: true,
        connectWith: els,
        start: function(e,ui) {
            // hide the div in all cases on drag
            ui.item.css('visibility', 'hidden');
        },
        stop: function(e,ui) {
            saveLayout();
            updateModuleEmbedAndShow(e, ui);
            $('div.xg_handle').hide();
        }
    });
})();
