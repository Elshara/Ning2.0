<?php
/** This partial template displays a thumbnail for a particular person.
  *
  * @param $profile XN_Profile The profile object of the person to display
  * @param $size integer The size of the thumbnail url
  * @param $showScreenName string  Whether to display the username. Defaults to true.
  * @param $presenceIconOnly boolean  Whether the presence indicator, if shown, should show only the icon and not the text
  * @param $showSendMessageLink boolean  Whether to try to show the Send Message link (depending on whether
  *         the current user is allowed to see it)
  * @param $friendStatusForSendMessageLink string  The relationship (contact, friend, pending, requested, groupie,
  *         blocked, or not-friend), or null if it is not known (or has not been queried, for performance)
  */

$size = isset($size) ? $size : 48;
$screenNameLink = '<p><a class="name" href="' . xnhtmlentities(User::quickProfileUrl($profile->screenName)) . '">' . xnhtmlentities(xg_username($profile)) . '</a></p>';
if ($size == 96) {
    $profileUrl = xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($profile->screenName));
    $avatar = '<a class="img xg_lightborder" style="background: url(' . xnhtmlentities(XG_UserHelper::getThumbnailUrl($profile,$size,$size)) . ')" title="' . xnhtmlentities(xg_username($profile->screenName)). '" href="' . $profileUrl . '" ></a>';
}
if (!isset($showScreenName)) $showScreenName = true; ?>
<li>
        <%= $avatar ? $avatar : xg_avatar($profile, $size) %><%= $showScreenName ? $screenNameLink : '' %>
        <?php
        XG_App::includeFileOnce('/lib/XG_PresenceHelper.php');
        if ($showScreenName && XG_PresenceHelper::canShowPresenceIndicator($profile)) {
            if ($presenceIconOnly) { ?>
                <img title="<%= xg_html('MEMBER_IS_ONLINE') %>" alt="<%= xg_html('ONLINE') %>" src="<%= xnhtmlentities(xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/online-user.gif'))) %>"/>
            <?php }
            else { ?>
                <p class="online"><%= xg_html('ONLINE') %></p>
        <?php }
        } ?>
</li>

<?php if ($args['endRow']) { ?>
    </ul><ul class="clist">
<?php } ?>
