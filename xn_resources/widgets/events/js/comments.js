dojo.provide('xg.events.comments');

dojo.require('dojo.lfx.*');

/**
 * Behavior for the comments section of the Events detail page.
 */

(function() {
    var rsvpLink = dojo.byId('xj_rsvp_link');
    dojo.event.connect(rsvpLink, 'onclick', function(event) {
        dojo.event.browser.stopEvent(event);
        dojo.byId('rsvpForm').parentNode.parentNode.scrollIntoView();
        dojo.lfx.html.highlight(dojo.byId('rsvpForm').parentNode, '#ffee7d', 1000).play();
        if(dojo.byId('changeStatus')){
            dojo.byId('changeStatus').onclick();
        }
    });
}());