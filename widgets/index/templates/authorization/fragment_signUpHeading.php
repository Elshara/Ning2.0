<?php
/**
 * Top portion of a sign-up form.
 *
 * @param $title string  heading text
 * @param $target string  (optional) URL to go to after sign-up
 * @param $groupToJoin string - (optional) URL of the group to make the user a member of
 * @param $invitation XN_Invitation  (optional) invitation for the current user
 */
$signInLink = xg_html('IF_ALREADY_MEMBER_SIGN_IN', 'href="' . xnhtmlentities(XG_AuthorizationHelper::signInUrl($target, $groupToJoin)) . '"');
if (XG_App::membersAreModerated()) { $signInLink = xg_html('APPLY_FOR_MEMBERSHIP_SIGN_IN', 'href="' . xnhtmlentities(XG_AuthorizationHelper::signInUrl($target, $groupToJoin)) . '"'); }
if ($invitation && $profile = XG_Cache::profiles($invitation->inviter)) { ?>
    <h3><%= xg_html('USERNAME_INVITED_YOU_TO_JOIN_APPNAME', xnhtmlentities(XG_UserHelper::getFullName($profile)), xnhtmlentities(XN_Application::load()->name)) %></h3>
<?php
} else { ?>
    <h3><%= xnhtmlentities($title) %></h3>
<?php
} ?>
<p class="small"><%= $signInLink %></p>
