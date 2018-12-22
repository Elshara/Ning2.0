dojo.provide('xg.shared.DisplayIfJavaScript');

/**
 * An element that is displayed if JavaScript is enabled. Also useful for temporarily hiding links
 * that won't work until the JavaScript is loaded.
 */
dojo.widget.defineWidget('xg.shared.DisplayIfJavaScript', dojo.widget.HtmlWidget, {

    /** Tells the Dojo widgetparser to parse descendant nodes */
    isContainer: true,

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        var node = this.getFragNodeRef(frag);
        dojo.style.show(node);
    }

});
