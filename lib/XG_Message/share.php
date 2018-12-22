<?php
/** Message template for an invitation
 *
 * @param $url string the target URL for the message
 * @param $body string The message from the sender
 * @param $fromProfile XN_Profile The person sending the invitation
 * @param $share boolean Is this a share-with-friends message (default is false)
 * @param $title string The title of the shared object, if it's a share-with-friends message
 * @param $thumb string optional preview url of the shared object, if it's a share-with-friends message
 * @param $type string optional type of the object being shared, if it's a share-with-friends message.
 */
// TODO: $share is always true; remove unneeded code. [Jon Aquino 2007-10-26]
// TODO Do this in the controller.  Or, much better, change the User text so it has no HTML and can be passed in unescaped.

$share = isset($share) ? $share : false;

if ($share && $thumb) {
    $imgSrc = $thumb;
    $imgAlt = xnhtmlentities($title);
    $imgHeightWidth='width="150"';
} else {
    $imgSrc = XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($fromProfile,96,96), 'xn_auth','no');
    $imgAlt = xnhtmlentities(xg_username($fromProfile));
    $imgHeightWidth='width="96" height="96"';
}

if ($share) {
    if (isset($type) && mb_strlen($type)) {
        $clickMessage = xg_html('TO_VIEW_THIS_X_VISIT', xnhtmlentities($type));
    } else {
        $clickMessage = xg_html('TO_VIEW_IT_VISIT');
    }
} else {
    $clickMessage = xg_html('CLICK_HERE_TO_JOIN_BANG');
}

?>
    <div class="xg_body">
        <h3><?php
        if ($share) {
            echo xg_html('X_WANTS_YOU_TO_CHECK_OUT_Y_ON_Z', xnhtmlentities(xg_username($fromProfile)), xnhtmlentities($title), xnhtmlentities($message['appName']));
        } else {
            echo xg_html('X_HAS_INVITED_YOU_TO_JOIN_Y', xnhtmlentities(xg_username($fromProfile)), xnhtmlentities($message['appName']));
        }
        ?></h3>
        <table width="100%">
            <tr>
                <td>
                    <?php
                    if ($body) { ?>
                        <p><%= nl2br(xnhtmlentities($body)) %></p>
                    <?php
                    } ?>
                    <p><%= $clickMessage %>
                        <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a></p>
                </td>
                <td class="picture"><img align="right" <%= $imgHeightWidth %> src="<%= xnhtmlentities($imgSrc) %>" alt="<%= $imgAlt %>"></td>
            </tr>
        </table>
        <p class="smallprint"><small>
            <?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
            <%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
            <a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
        </small></p>
    </div>
