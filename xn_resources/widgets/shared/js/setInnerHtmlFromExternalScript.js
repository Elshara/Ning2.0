/**
 * Sets the node's innerHTML. If you use this to insert Flash objects into the document,
 * you can avoid the "Click to activate" message in IE.
 *
 * Load this file using a <script> tag rather than dojo.require or ning.loader.require.
 */
window.setInnerHtmlFromExternalScript = function(node, innerHTML) {
    node.innerHTML = innerHTML;
}
// In the future, if we create a setOuterHtmlFromExternalScript function,
// note that IE has some quirks in its handling of outerHTML and innerHTML for <object>s.
// See Már Örlygsson, "The Elegant, Unobtrusive Javascript Workaround for 'Click to activate and use this control'",
// http://mar.anomy.net/entry/2006/11/24/02.12.18/  [Jon Aquino 2007-05-31]
