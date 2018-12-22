<?php
/**
 * The footer of the Photos module.
 *
 * @param $embed XG_Embed containing the state of the Photos module
 * @param $viewAllUrl string  URL for the View All link
 * @param $addPhotosUrl string URL for Add Photos link or null to hide the link.
 */
XG_App::includeFileOnce('/lib/XG_FacebookHelper.php');
if (XG_FacebookHelper::isAppEnabled('photo') && $embed->get('photoType') == 'slideshow') { ?>
    <div class="xg_module_body xg_module_facebook">
        <p class="left">
            <small><img src="<%= qh(xg_cdn('/xn_resources/widgets/index/gfx/icon/facebook.gif')) %>"><a href="<%= qh(XG_FacebookHelper::getFacebookEmbedAppUrl('photo')) %>"><%= xg_html('FACEBOOK_ADD_TO_FACEBOOK') %></a></small>
        </p>
        <p class="right"><small><a href="<%= xnhtmlentities($viewAllUrl) %>"><%= xg_html('VIEW_ALL') %></a></small></p>
    </div>
<?php 
} else { ?>
    <div class="xg_module_foot">
        <ul>
            <?php if ($addPhotosUrl) { ?>
                <li class="left"><a href="<%= xnhtmlentities($addPhotosUrl) %>" class="desc add"><%= xg_html('ADD_PHOTOS') %></a></li>
            <?php } ?>
            <?php if ($viewAllUrl) { ?>
                <li class="right"><a href="<%= xnhtmlentities($viewAllUrl) %>"><%= xg_html('VIEW_ALL') %></a></li>
            <?php } ?>
        </ul>
    </div>
<?php 
} ?>

