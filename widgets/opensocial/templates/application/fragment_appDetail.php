<?php
/**
 * Fragment for showing logo, title, description and possibly Add link and some stats for an application.
 * For use in list views like the Application Directory.
 *
 * @param   $showAddLink boolean   true if show Add to My Page links or false otherwise
 */
$loaderId = "app-" . xnhtmlentities(md5($app['appUrl']));
$reviewsUrl = $this->_buildUrl('application', 'about', array('appUrl' => $app['appUrl'])) . "#reviews"; ?>
<li class="xg_lightborder<%= $lastChild ? ' last-child' : '' %>">
    <div class="ib">
        <?php if ($app['prefs']['thumbnail']) { ?>
            <a href="<%= xnhtmlentities($this->_buildUrl('application', 'show', array('appUrl' => $app['appUrl'], 'owner' => $this->screenName))) %>">
                <img width="120" height="60" src="<%= xnhtmlentities($app['prefs']['thumbnail']) %>" alt="<%= xnhtmlentities($app['prefs']['title']) %>"/>
            </a>
        <?php } ?>
    </div>
    <div class="tb">
        <?php if ($app['numReviews']) { ?>
            <div class="item_ratings">
                <a href="<%= xnhtmlentities($reviewsUrl) %>"><%= xg_rating_image($app['avgRating']) %></a>
                <a href="<%= xnhtmlentities($reviewsUrl) %>"><%= xg_html('N_REVIEWS', xnhtmlentities($app['numReviews'])) %></a>
            </div>
        <?php } ?>
        <h3><a href="<%= xnhtmlentities(W_Cache::getWidget('opensocial')->buildUrl('application', 'show', array('appUrl' => $app['appUrl'], 'owner' => $this->screenName))) %>"><%= xg_excerpt(xnhtmlentities($app['prefs']['title']), 40) %></a><%= ($app['recommended'] ? ' <img title="'. xg_html('RECOMMENDED') . '" src="' . xnhtmlentities(xg_cdn(W_Cache::getWidget('opensocial')->buildResourceUrl('gfx/icon/recommended.gif'))) . '" width="16" height="16" alt="' . xg_html('RECOMMENDED')  . '"/>' : "") %></h3>
        <div class="description"><%= xg_excerpt(qh($app['prefs']['description']), 200) %></div>
        <div class="details" id="<%= $loaderId %>">
            <?php W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_LinkHelper.php');
            if (OpenSocial_LinkHelper::showAppDetailAddLink($app['appUrl'], XN_Profile::current()->screenName, $showAddLink)) {
                $this->renderPartial('fragment_addAppLink', '_shared', array('appUrl' => $app['appUrl'], 'loaderId' => $loaderId, 'cssClass' => 'button'));
            }
            if ($showAddLink && OpenSocial_GadgetHelper::isApplicationInstalled($app['appUrl'])) { ?>
                <div><strong><%= xg_html('YOUVE_ADDED_THIS_APPLICATION') %></strong></div>
            <?php }
            if ($showAddLink) {
                $membersUrl = $this->_buildUrl('application', 'people', array('show' => 'members', 'appUrl' => $app['appUrl']));
                $friendsUrl = $this->_buildUrl('application', 'people', array('show' => 'friends', 'appUrl' => $app['appUrl']));
                if ($app['numMembers'] && $app['numFriends']) { ?>
                    <small class="block"><%= xg_html('N_MEMBERS_AND_N_FRIENDS_ADDED_THIS_APPLICATION', xnhtmlentities($app['numMembers']), 'href="' . xnhtmlentities($membersUrl) . '"', 'href="' . xnhtmlentities($friendsUrl) . '"', xnhtmlentities($app['numFriends'])) %></small>
                <?php } else if ($app['numMembers']) { ?>
                    <small class="block"><%= xg_html('N_MEMBERS_ADDED_THIS_APPLICATION', xnhtmlentities($app['numMembers']), 'href="' . xnhtmlentities($membersUrl) . '"') %></small>
                <?php } 
            } else {
                $moreHref = 'href="' . qh($this->_buildUrl('application', 'about', array('appUrl' => $app['appUrl']))) . '"'; ?>
                    <div><%= xg_html('ABOUT_THIS_APPLICATION_LINK', $moreHref)  %></div>
            <?php } ?>
        </div>
    </div>
</li>
