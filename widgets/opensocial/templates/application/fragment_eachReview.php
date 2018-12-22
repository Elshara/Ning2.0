<?php W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_SecurityHelper.php'); ?>
<dl class="comment vcard" id="<%= $review->id %>">
    <dt>
        <%= xg_html('REVIEW_BY_IMG_NAME_TIME', xg_avatar(XG_Cache::profiles($review->my->user), 48, 'photo'), xnhtmlentities(xg_username($review->my->user)), '<span class="xg_lightfont">' .  xnhtmlentities(xg_elapsed_time($review->updatedDate)) . '</span>') %>
        <?php if (OpenSocial_SecurityHelper::currentUserCanDeleteReview($review)) { ?>
            <a class="icon delete" href="#" _reviewId="<%= xnhtmlentities($review->id) %>">
            <%= xg_html('DELETE') %></a>
        <?php } ?>
    </dt>
    <dd><%= xg_rating_image($review->my->rating) %></dd>
    <dd>
        <%= xnhtmlentities($review->my->body) %>
    </dd>
</dl>
