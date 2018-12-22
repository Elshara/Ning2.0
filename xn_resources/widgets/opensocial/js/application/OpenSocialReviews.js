dojo.provide('xg.opensocial.application.OpenSocialReviews');

/**
 * A module which submits the reviews for the app.
 */
dojo.widget.defineWidget('xg.opensocial.application.OpenSocialReviews',dojo.widget.HtmlWidget, {
    /** Endpoint for looking up user's reviews */
    _lookupReviewUrl: '',
    
    /** Endpoing for adding a review */
    _addReviewUrl: '',
    
    /** Endpoint for deleting a review */
    _deleteReviewUrl: '',

    /**
     * Initializes the widget.
     */
    fillInTemplate: function(args, frag) {
        var module = this.getFragNodeRef(frag);
        dojo.event.connect(dojo.byId('addReviewSubmitButton'), 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
            var content = this.getContent();
            var errors = this.validateContent(content);
	        if (errors) {
	            this.displayErrors(errors);
	            return;
	        }
	        dojo.io.bind({
	            url: this._lookupReviewUrl,
	            method: 'get',
	            preventCache: true,
	            mimetype: 'text/json',
	            encoding: 'utf-8',
	            load: dojo.lang.hitch(this, function(type, data, event){
	                        var form = dojo.byId('comment_form');
	                        var self = this;
					        if (data['numReviews'] == '1') {
					            xg.shared.util.confirm({
				                        title: xg.opensocial.nls.html('replaceReview'),
					                    bodyHtml: xg.opensocial.nls.html('replaceReviewQ'),
					                    okButtonText: xg.opensocial.nls.html('replaceReview'),
					                    onOk: function() { self.submitForm(self, content); } 
					                });
					        } else {
					            self.submitForm(self, content);
                            }
                        })
	        });
        }));
        dojo.lang.forEach(dojo.html.getElementsByClass('delete', module), 
			                dojo.lang.hitch(this, function(node) { dojo.event.connect(node, 'onclick', dojo.lang.hitch(this, function(event) {
                                var self = this;
                                xg.shared.util.confirm({
                                        title: xg.opensocial.nls.html('deleteReview'),
                                        bodyHtml: xg.opensocial.nls.html('deleteReviewQ'),
                                        okButtonText: xg.opensocial.nls.html('deleteReview'),
                                        onOk: function() { self.deleteReview(self, node); } 
                                    });
			                    }))
                        }));
    },
    getContent: function() {
        var content = {};
        var form = dojo.byId('comment_form');
        var inputs = form.getElementsByTagName('input');
        for (var i = 0; i<inputs.length; i++) {
            var inp = inputs[i];
            if (inp.type === 'hidden') {
                content[inp.name] = inp.value;
            }
        }
        var tas = form.getElementsByTagName('textarea');
        for (var i = 0; i<tas.length; i++) {
            var ta = tas[i];
            content[ta.name] = ta.value;
        }
        return content;
    },
    validateContent: function(content) {
        var errors = {};
        var body = content['body'];
        var rating = content['rating'];
        if (body === '') {
            errors['body'] = xg.opensocial.nls.html('mustSupplyReview');
        } else if (body.length > 4000) {
            errors['body'] = xg.opensocial.nls.html('reviewIsTooLong', body.length);
        }
        if (!rating || rating < 1 || rating > 5) {
            errors['rating'] = xg.opensocial.nls.html('mustSupplyRating');
        }
        return (!errors['body'] && !errors['rating']) ? null : errors;
    },
    displayErrors: function(errors) {
        var errorMsg = dojo.byId('errorMsg');
        var html = '';
        if (errors['rating']) html += '<li>'+errors['rating']+'</li>';
        if (errors['body']) html += '<li>'+errors['body']+'</li>';
        if (html) {
            html = '<dt>'+xg.opensocial.nls.html('thereHasBeenAnError')+'</dt><ul>'+html+'</ul>';
            errorMsg.innerHTML = html;
            errorMsg.style.display = '';
        }
    },
    updateSections: function(avgRatingHtml, headerHtml) {
        if (avgRatingHtml) {
            var node = dojo.byId('overallStarRating');
            if (node) { node.innerHTML = avgRatingHtml; }
        }
        if (headerHtml) {
            var node = dojo.byId('allReviewsHeader');
            if (node) { node.innerHTML = headerHtml; }
        }
    },
    clearErrors: function() {
        var errorMsg = dojo.byId('errorMsg');
        errorMsg.innerHtml = '';
        errorMsg.style.display = 'none';
    },
    // Closely tied-in with StarRater dojo widget
    clearForm: function() {
		var form = dojo.byId('comment_form');
		var ta = form.getElementsByTagName('textarea')[0];
		if (ta) ta.value = '';
		var starRater = dojo.widget.manager.getWidgetsByType('StarRater')[0];
		if (starRater) {
            starRater.clearRating();
		}
    },
    submitForm: function(self, content) {
        dojo.io.bind({
	            url: self._addReviewUrl,
	            method: 'post',
	            content: content,
	            preventCache: true,
	            mimetype: 'text/json',
	            encoding: 'utf-8',
	            load: function(type, data, event){ self.handleSubmission(self, data); }
	        });
    },
    handleSubmission: function(self, data) {
        var errors = data['errors'];
        if (errors) {
            self.displayErrors(errors);
            return;
        } else {
            var errorMsg = dojo.byId('errorMsg');
            var currUserReview = dojo.byId(data['reviewId']);
	        var overallStarRating = dojo.byId('overallStarRating');
	        var allReviewsHeader = dojo.byId('allReviewsHeader');
	        self.updateSections(data['avgRatingHtml'], data['headerHtml']);
            self.clearErrors();
            self.clearForm();
            if (currUserReview) {
                dojo.dom.removeNode(currUserReview);
            }
            var reviewHtml = data['reviewHtml'];
            if (reviewHtml) {
                var reviewNode = dojo.html.createNodesFromText(reviewHtml);
                var reviewsBlock = dojo.byId('reviews');
                dojo.dom.prependChild(reviewNode, reviewsBlock);
                var deleteLink = dojo.html.getElementsByClass('delete', reviewNode)[0];
                if (deleteLink) {
	                dojo.event.connect(deleteLink, 'onclick', function(event) {
	                        xg.shared.util.confirm({
	                                title: xg.opensocial.nls.html('deleteReview'),
	                                bodyHtml: xg.opensocial.nls.html('deleteReviewQ'),
	                                okButtonText: xg.opensocial.nls.html('deleteReview'),
	                                onOk: function() { self.deleteReview(self, deleteLink); }
	                            });
	                    });
                }
            }
        }
    },
    deleteReview: function(self,node) {
        dojo.io.bind({
            url: self._deleteReviewUrl,
            method: 'post',
            content: { reviewId: node.getAttribute('_reviewId') },
            preventCache: true,
            mimetype: 'text/json',
            encoding: 'utf-8',
            load: function(type, data, event){
                    self.updateSections(data['avgRatingHtml'], data['headerHtml']);
                    dojo.dom.removeNode(dojo.byId(node.getAttribute('_reviewId')));
		            self.clearErrors();
                 }
        });
    }
});
