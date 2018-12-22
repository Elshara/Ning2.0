dojo.provide('xg.profiles.embed.chatterwall');

dojo.require('xg.index.util.FormHelper');

// TODO: Eliminate code duplicated in xg.profiles.blog.show [Jon Aquino 2008-01-11]
xg.profiles.embed.chatterwall = {
    submitButton: null,
    enableSubmit: function() {
        if (xg.profiles.embed.chatterwall.submitButton) {
            xg.profiles.embed.chatterwall.submitButton.disabled = false;
        }
    },
    disableSubmit: function() {
        if (xg.profiles.embed.chatterwall.submitButton) {
            xg.profiles.embed.chatterwall.submitButton.disabled = true;
        }
    },

    validate: function(form) {
        var errors = {};
        errors = xg.index.util.FormHelper.validateRequired(errors, form, 'comment', xg.profiles.nls.html('pleaseEnterChatter'));
        if (dojo.lang.isEmpty(errors)) {
            xg.profiles.embed.chatterwall.disableSubmit();
        }
        return errors;
    },

    showEmptyMessage: function() {
        var emptyP = dojo.byId('xg_profiles_chatterwall_empty');
        if (emptyP) dojo.html.show(emptyP);
    },
    hideEmptyMessage: function() {
        var emptyP = dojo.byId('xg_profiles_chatterwall_empty');
        if (emptyP) dojo.html.hide(emptyP);
    },

    displayFromHtml: function(html, position) {
        xg.profiles.embed.chatterwall.hideEmptyMessage();
        var chatterList = dojo.byId('xg_profiles_chatterwall_list');
        var newChatterDl = null;
        if (chatterList) {
            var nodes = dojo.html.createNodesFromText(html);
            var form = dojo.byId('xg_profiles_chatterwall_post');
            // If the <dl/> that contains the chatter has the 'comment-new' class,
            // then it's not approved yet. So instead of displaying it, show a
            // fadeaway message over the textbox in the form saying that the chatter
            // needs to be approved before it can be viewed.
           var isModerated = null;
            for (var k in nodes) {
                if ((isModerated === null) && (nodes[k].tagName == 'DL')) {
                    isModerated = dojo.html.hasClass(nodes[k], 'comment-new');
                    break;
                }
            }
            if (isModerated && form) {
                var dialog = dojo.html.getElementsByClass('notification', form, 'div')[0];
                if (! dialog) {
                    dialog = dojo.html.createNodesFromText('<div class="notification" style="margin-bottom:1em"><p class="last-child">' + xg.profiles.nls.html('memberHasChosenToModerate', dojo.byId('xg_profiles_chatterwall_ownerName').value) + '</p></div>')[0];
                    dojo.dom.insertAtPosition(dialog, form, 'first');
                }
                xg.index.util.FormHelper.scrollIntoView(form);
                return;
            } else {
                //  If not moderated, update the comment count now
                xg.profiles.embed.chatterwall.changeCommentCount(+1);
            }

            // If we're not on the page of comments where the new comment
            // would appear, then redirect to that page. For chatters, that's the
            // first page of chatters (BAZ-984)
            // If the form contains a "showCommentUrl" element whose length is
            // nonzero, then redirect to that URL
            if (form.showCommentUrl && form.showCommentUrl.value && form.showCommentUrl.value.length) {
                window.location.replace(form.showCommentUrl.value);
                return;
            }

            if (position == 'first') {
                for (var i = (nodes.length - 1); i >= 0; i--) {
                    dojo.dom.insertAtPosition(nodes[i], chatterList, 'first');
                    if ((newChatterDl === null) && (nodes[i].tagName == 'DL')) {
                        newChatterDl = nodes[i];
                    }
                }
            } else if (position  == 'last') {
                for (var j in nodes) {
                    var addedNode = chatterList.appendChild(nodes[j]);
                    if ((newChatterDl === null) && (addedNode.tagName == 'DL')) {
                        newChatterDl = addedNode;
                    }
                }
            }

            // Activate any 'approve' or 'delete' links that may be in the new chatter
            if (newChatterDl) {
                dojo.lang.forEach(['remove','approve'], function(suffix) {
                    xg.profiles.embed.chatterwall.bindToClassLinks('chatter-' + suffix, xg.profiles.embed.chatterwall[suffix], newChatterDl);
                }, true);
            }
        }
    },

    create: function(data) {
        // TODO: Display a spinner during the Ajax form submission [Jon Aquino 2008-01-11]
        // Show and activate the new chatter
        xg.profiles.embed.chatterwall.displayFromHtml(data.html, 'first');
        // Clear the chatter submission text box
        dojo.byId('xg_profiles_chatterwall_post_comment').value = '';
        // Re-enable the button
        xg.profiles.embed.chatterwall.enableSubmit();
    },

    approve: function(linkNode, chatterId) {
        dojo.io.bind({ 'url': '/index.php/' + xg.global.currentMozzle + '/comment/approve?xn_out=json',
                       'method': 'post',
                       'mimetype': 'text/javascript',
                       'content': { 'id' : chatterId },
                       'load': function(type, data, evt) {
                           // Remove the notification class from the containing dl
                           var containingDl = dojo.dom.getFirstAncestorByTag(linkNode, 'dl');
                           if (containingDl) {
                               dojo.html.removeClass(containingDl, 'comment-new');
                           }
                           // remove the approve link from the page
                           linkNode.parentNode.removeChild(linkNode);
                           // If there's a spacer node (for the vertical bar separating the approve and delete links),
                           // remove that too.
                           var spacer = dojo.byId('chatter-spacer-' + chatterId);
                           if (spacer) { spacer.parentNode.removeChild(spacer); }
                       }
        });
    },

    /* "delete" is a reserved word */
    remove: function(linkNode, chatterId) {
        dojo.io.bind({ 'url': '/index.php/' + xg.global.currentMozzle + '/comment/delete?xn_out=json',
                       'method': 'post',
                       'mimetype': 'text/javascript',
                       'content': { 'id' : chatterId },
                       'load': function(type, data, evt) {
                           // Remove the containing <dl/>
                           var containingDl = dojo.dom.getFirstAncestorByTag(linkNode, 'dl');
                           if (containingDl) {
                               containingDl.parentNode.removeChild(containingDl);
                           }
                           xg.profiles.embed.chatterwall.changeCommentCount(-1);
                           // Get another chatter to stuff at the bottom of the list
                           var chatterList = dojo.byId('xg_profiles_chatterwall_list');
                           if (chatterList) {
                               var timestamp = null; /* Start with the most recent */
                               // Find the bottom-most chatter timestamp in the list
                               var timestampSpans = dojo.html.getElementsByClass('chatter-timestamp', chatterList, 'span');
                               if (timestampSpans.length) {
                                   var lastTimestampSpan = timestampSpans.pop();
                                   var m = lastTimestampSpan.id.match(/^chatter-timestamp-(\d+)$/);
                                   if (m) { timestamp = m[1]; }
                               }
                               // Find the first chatter that appears before the specified time
                               var attachedTo = dojo.byId('xg_profiles_chatterwall_attachedTo');
                               var attachedToType = dojo.byId('xg_profiles_chatterwall_attachedToType');
                               if (attachedTo && attachedToType) {
                                   dojo.io.bind({
                                       'url': '/index.php/' + xg.global.currentMozzle + '/comment/previous?attachedTo='+encodeURIComponent(attachedTo.value)+'&attachedToType='+encodeURIComponent(attachedToType.value)+'&when=' + timestamp + '&xn_out=htmljson',
                                       'method' : 'get',
                                       'mimetype' : 'text/javascript',
                                       'load': function (type, data, evt) {
                                           if (data && data.html && data.html.length) {
                                               xg.profiles.embed.chatterwall.displayFromHtml(data.html, 'last');
                                           }
                                           // No chatter was retrieved, so put up the 'no chatters yet' message
                                           // if there are no other chatters displayed
                                           else {
                                               if (timestamp === null) {
                                                   xg.profiles.embed.chatterwall.showEmptyMessage();
                                               }
                                           }
                                       }
                                   });
                               }
                           }
                       }
        });
    },

    bindToClassLinks: function(klass, callback, parent) {
        dojo.lang.forEach(dojo.html.getElementsByClass(klass, parent), function(el) {
            var pattern = new RegExp('^' + klass + '-(.+)$');
            var m = el.id.match(pattern);
            if (m) { // m[1] is the content object ID embedded in the element ID
                dojo.event.connect(el, 'onclick', function(evt) { dojo.event.browser.stopEvent(evt); callback(el, m[1]); });
            }
        }, true);
    },

    /**
     * Changes the number of comments displayed in the chatterwall title.
     *
     * @param integer delta  the positive or negative change
     */
    changeCommentCount: function(delta) {
        var heading = dojo.byId('chatter_box_heading');
        var numComments = parseInt(heading.getAttribute('numComments'), 10) + delta;
        heading.setAttribute('numComments', numComments);
        heading.innerHTML = numComments > 0 ? xg.profiles.nls.html('commentWallNComments', numComments) : xg.profiles.nls.html('commentWall');
    }

};

xg.addOnRequire(function() {
    var form = dojo.byId('xg_profiles_chatterwall_post');
    if (form) {
        // append json output to the url so that if the page loaded or javascript is enabled the submission can be made ajaxy-style
        form.setAttribute('action', form.getAttribute('action')+'?xn_out=htmljson');

        xg.index.util.FormHelper.validateAndSave(form,
                                             xg.profiles.embed.chatterwall.validate,
                                             xg.profiles.embed.chatterwall.create);

       // Save a reference to the submit button so we can easily enable and disable it
       xg.profiles.embed.chatterwall.submitButton = dojo.html.getElementsByClass('button',form,'input')[0];
    }
    dojo.lang.forEach(['remove','approve'], function(suffix) {
        xg.profiles.embed.chatterwall.bindToClassLinks('chatter-' + suffix, xg.profiles.embed.chatterwall[suffix]);
    }, true);
    // determine if the add-comment needs to function as a toggle
    var addComment = dojo.byId('add-comment');
    if (addComment && dojo.html.hasClass(addComment, 'toggle')) {
        dojo.event.connect(addComment, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            dojo.html.toggleShowing(form);
            var span = addComment.getElementsByTagName('span')[0];
            var open = false;
            if (span.getAttribute('state') == 'closed') {
                span.setAttribute('state', 'open');
                open = true;
                addComment.className = 'toggle';
            } else {
                span.setAttribute('state', 'closed');
                addComment.className = 'toggle last-child';
            }
            span.innerHTML = open ? '&#9660;' : (dojo.render.html.ie ? '&#9658;' : '&#9654;');
        }));
    }
});