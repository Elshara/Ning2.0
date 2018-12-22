<?php
/** Message template for an invitation
 *
 * @param $url string the target URL for the message
 * @param $body string The message from the sender
 * @param $fromProfile XN_Profile The person sending the invitation
 * TODO are these parameters below a copy and paste error?  They don't get used.  Are they passed in?
 * @param $title string The title of the shared object, if it's a share-with-friends message
 * @param $thumb string optional preview url of the shared object, if it's a share-with-friends message
 * @param $type string optional type of the object being shared, if it's a share-with-friends message.
 */
$imgSrc = XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($fromProfile,96,96), 'xn_auth','no');
$imgAlt = xg_username($fromProfile);
$imgHeightWidth='width="96" height="96"';
?>
<div class="xg_body">
    <p><big><strong><%= xg_html('COME_JOIN_ME_ON_X', xnhtmlentities(XG_UserHelper::getFullName($fromProfile)), xnhtmlentities($message['appName'])) %></strong></big></p>
    <table width="100%">
        <tr>
            <td>
                <?php
                if ($body) { ?>
                    <p><%= xnhtmlentities($body) %></p>
                <?php
                } ?>
                <p><%= xg_html('CLICK_HERE_TO_JOIN') %><br />
                    <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a></p>
                <p><%= xnhtmlentities(xg_username($fromProfile->screenName)) %></p>
            </td>
            <td class="picture"><img align="right" <%= $imgHeightWidth %> src="<%= xnhtmlentities($imgSrc) %>" alt="<%= xnhtmlentities($imgAlt) %>"></td>
        </tr>
    </table>
</div>
