dojo.provide('xg.shared.CommentForm');

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.comment');
dojo.require('xg.shared.util');

/**
 * A form for submitting comments.
 */
xg.shared.CommentForm = {

    /** Whether to submit the form using Ajax. */
    ajax: null,

    /** Whether to add the new comment to the top of the comment list, rather than the bottom. */
    addAtTop: null,

    /** Whether an Ajax request is in progress */
    submitting: false,

    /**
     * Sets up the behavior of comments on the page.
     */
    initialize: function() {
        var form = dojo.byId('comment_form');
        this.ajax = form.getAttribute('_ajax') == 'true';
        this.addAtTop = form.getAttribute('_addAtTop') == 'true';
        this.submitting = false;
        var spinner = dojo.html.createNodesFromText('<img src="' + xg.shared.util.cdn('/xn_resources/widgets/index/gfx/spinner.gif') + '" alt="" style="width:16px; height:16px; margin-right:3px; display: none;" />')[0];
        dojo.dom.insertBefore(spinner, dojo.html.getElementsByClass('button', form)[0]);
        dojo.event.connect(form, 'onsubmit', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            if (this.submitting) { return; }
            if (! xg.index.util.FormHelper.runValidation(form, dojo.lang.hitch(this, this.validate))) { return; }
            if (! this.ajax) { return form.submit(); }
            this.submitting = true;
            dojo.style.show(spinner);
            dojo.dom.removeNode(dojo.html.getElementsByClass('notification', form, 'div')[0]);
            dojo.io.bind({
                url: form.action + '&xn_out=json',
                method: 'post',
                preventCache: true,
                encoding: 'utf-8',
                mimetype: 'text/javascript',
                content: { comment: form.comment.value },
                load: dojo.lang.hitch(this, function(type, data, event){
                    dojo.style.hide(spinner);
                    this.submitting = false;
                    this.onSuccess(data);
                })
            });
        }));
    },

    /**
     * Validates the form.
     */
    validate: function(form) {
        return xg.index.util.FormHelper.validateRequired({}, form, 'comment', xg.shared.nls.html('pleaseEnterAComment'));
    },

    /**
     * Called after the comment is successfully posted.
     *
     * @param data  object returned by the comment endpoint
     */
    onSuccess: function(data) {
        if (! data.html) { return; }
        if (data.userIsNowFollowing) {
            dojo.lang.forEach(dojo.widget.manager.getWidgetsByType('FollowLink'), function (followLink) {
                followLink.showFollowing();
            });
        }
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = data.html;
        var dl = dojo.dom.firstElement(tempDiv);
        var form = dojo.byId('comment_form');
        form.comment.value = '';
        if (data.approved === false) {
            var notification = dojo.html.getElementsByClass('notification', form, 'div')[0];
            if (! notification) {
                notification = dojo.html.createNodesFromText('<div class="notification" style="margin-bottom:1em"><p class="last-child">' + xg.shared.nls.html('yourCommentMustBeApproved') + '</p></div>')[0];
                dojo.dom.insertAtPosition(notification, form, 'first');
            }
            xg.index.util.FormHelper.scrollIntoView(form);
            return;
        }
        xg.shared.comment.addDl(dl, this.addAtTop);
    }
}

xg.shared.CommentForm.initialize();
