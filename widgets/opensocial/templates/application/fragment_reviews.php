<div class="xg_module module_reviews" _addReviewUrl="<%= $this->addReviewUrl %>"
    _deleteReviewUrl="<%= $this->deleteReviewUrl %>"
    _lookupReviewUrl="<%= $this->lookupReviewUrl %>" dojoType="OpenSocialReviews">
    <div class="xg_module_head">
        <h2 id="allReviewsHeader"><%= $this->reviewsHeader($this->numReviews) %></h2>
	</div>
    <div class="xg_module_body" id="reviews">
        <?php
        foreach ($this->reviews as $review) {
            $this->renderPartial('fragment_eachReview', 'application', array('review' => $review));
        }
        // Don't let anonymous users see the add review form.
        if (XN_Profile::current()->isLoggedIn()) {
            $this->renderPartial('fragment_addReview', 'application');
        } else {?>
            <div class="comment-join">
                <h3><%= xg_html('YOU_NEED_TO_BE_MEMBER_REVIEW', xnhtmlentities(XN_Application::load()->name)) %></h3>
                <p><%= xg_html('SIGN_UP_OR_SIGN_IN', 'href="' . xnhtmlentities(XG_HttpHelper::signUpUrl()) . '"', 'href="' . xnhtmlentities(XG_HttpHelper::signInUrl()) . '"') %></p>
            </div>
        <?php }
        if (count($this->reviews) < $this->numReviews && $this->allReviewsUrl) { ?>
            <p class="right"><a href="<%= $this->allReviewsUrl %>"><%= xg_html('VIEW_ALL_REVIEWS') %></a></p>
        <?php } ?>
        <?php
            XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
            XG_PaginationHelper::outputPagination($this->numReviews, $this->pageSize);
         ?>
    </div>
</div><!--/.xg_module-->