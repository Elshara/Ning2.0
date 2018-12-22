dojo.provide('xg.shared.comment');

dojo.require('dojo.lfx.*');

/**
 * Behavior for a list of comments.
 *
 * @see XG_CommentHelper
 */
xg.shared.comment = {

    /** The total number of comments */
    numComments: null,

    /** The <div> for the comments */
    commentContainer: null,

    /**
     * Adds behavior to all comments on the page.
     */
    initialize: function() {
        var commentDiv;
        dojo.lang.forEach(xg.$$('div'), function(div) {
            if (div.id == 'comments') { commentDiv = div; }
        });
        this.commentContainer = commentDiv;
        this.numComments = parseInt(this.commentContainer.getAttribute('_numComments'), 10);
        dojo.lang.forEach(dojo.html.getElementsByClass('comment', this.commentContainer, 'dl'), dojo.lang.hitch(this, this.initializeDl));
    },

    /**
     * Adds behavior to the <dl> element for a comment.
     *
     * @param dl  the <dl> element to set up
     */
    initializeDl: function(dl) {
        dojo.lang.forEach(dojo.html.getElementsByClass('delete_link', dl, 'a'), dojo.lang.hitch(this, function(deleteLink) {
            var deleting = false;
            dojo.event.connect(deleteLink, 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                if (deleting) { return; }
                deleting = true;
                dojo.io.bind({
                    url: deleteLink.getAttribute('_url'),
                    method: 'post',
                    mimetype: 'text/javascript',
                    preventCache: true,
                    encoding: 'utf-8',
                    content: { 'id' : dl.getAttribute('_id') },
                    load: dojo.lang.hitch(this, function(type, data, event) {
                        if (! data.success) { return; }
                        dojo.lfx.html.fadeOut(dl, 500, null, dojo.lang.hitch(this, function() {
                            dl.parentNode.removeChild(dl);
                        })).play();
                        this.changeCommentCount(-1);
                    })
                });
            }));
        }));
        dojo.lang.forEach(dojo.html.getElementsByClass('approve_link', dl, 'a'), dojo.lang.hitch(this, function(approveLink) {
            var approving = false;
            dojo.event.connect(approveLink, 'onclick', dojo.lang.hitch(this, function(event) {
                dojo.event.browser.stopEvent(event);
                if (approving) { return; }
                approving = true;
                dojo.io.bind({
                    url: approveLink.getAttribute('_url'),
                    method: 'post',
                    mimetype: 'text/javascript',
                    preventCache: true,
                    encoding: 'utf-8',
                    content: { 'id' : dl.getAttribute('_id') },
                    load: dojo.lang.hitch(this, function(type, data, event) {
                        if (! data.success) { return; }
                        var approvalDiv = dojo.dom.getAncestorsByTag(approveLink, 'div', true);
                        dojo.lfx.html.fadeOut(approvalDiv, 500, null, dojo.lang.hitch(this, function() {
                            approvalDiv.parentNode.removeChild(approvalDiv);
                        })).play();
                    })
                });
            }));
        }));
    },

    /**
     * Adds the <dl> element to the DOM
     *
     * @param dl  the <dl> element representing a new comment.
     * @param addAtTop  whether to add the new comment to the top of the comment list, rather than the bottom.
     */
    addDl: function(dl, addAtTop) {
        dojo.style.setOpacity(dl, 0);
        dojo.html.show(this.commentContainer);
        if (addAtTop) {
            dojo.dom.insertAfter(dl, this.commentContainer.getElementsByTagName('p')[0]);
        } else {
            this.commentContainer.appendChild(dl);
        }
        this.initializeDl(dl);
        this.changeCommentCount(1);
        dojo.lfx.fadeIn(dl, 500, dojo.lfx.easeIn).play();
        dojo.style.show(dojo.html.getElementsByClass('xg_module_foot', this.commentContainer.parentNode, 'div')[0]);
    },

    /**
     * Increments or decrements the displayed comment count.
     *
     * @param delta  +1 or -1
     */
    changeCommentCount: function(delta) {
        this.numComments += delta;
        dojo.dom.firstElement(this.commentContainer).innerHTML = xg.shared.nls.html('nComments', this.numComments);
        var heading = dojo.byId('comments');
        if (typeof(heading.getAttribute('numComments')) != typeof(null)) {
            heading.setAttribute('numComments', this.numComments);
            heading.innerHTML = this.numComments > 0 ? xg.shared.nls.html('commentWallNComments', this.numComments) : xg.shared.nls.html('commentWall');
        }
    }

}

xg.shared.comment.initialize();
