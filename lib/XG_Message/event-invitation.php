<?php
/**
 * An invitation to a event
 *
 * @param $url string the target URL for the message
 * @param $body string  the message from the sender
 * @param $fromProfile XN_Profile  the person sending the invitation
 * @param $event XN_Content  Event object
 */
$username = xg_username($fromProfile->screenName);
?>
<div class="xg_body">
    <table width="100%">
        <tr>
            <td>
                <h3><%= xg_html('USER_HAS_INVITED_YOU_TO_JOIN_EVENT', xnhtmlentities($username), xnhtmlentities($event->title), Events_TemplateHelper::startDate($event,true)) %></h3>
                <?php if ($body) { ?>
                    <p><big><%= nl2br(xnhtmlentities($body)) %></big></p>
                <?php } ?>
<?php
echo "<p>";
echo xg_html('EVENT_COLON'),' ',xnhtmlentities($event->title), "<br />";
echo xg_html('TIME_COLON'), ' ', Events_TemplateHelper::startDate($event), "<br />";
echo xg_html('LOCATION_COLON'), ' ', Events_TemplateHelper::location($event), "<br />";
echo xg_html('EVENT_TYPE_COLON'), ' ', Events_TemplateHelper::type($event), "<br />";
echo xg_html('ORGANIZED_BY_COLON'), ' ', Events_TemplateHelper::organizedBy($event), "<br />";
echo xg_html('DESCRIPTION_COLON'), "</p>";
echo "<p>", $event->description, "</p>\n";
?>
                <p><%= xg_html('CLICK_HERE_TO_RSVP_COLON') %><br />
                    <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a></p>
                <p><%= xnhtmlentities($username) %></p>
            </td>
            <td class="picture"><img align="right" width="96" height="96" src="<%= xnhtmlentities(XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($fromProfile,96,96), 'xn_auth','no')) %>" alt=""></td>
        </tr>
    </table>
    <p class="smallprint"><small>
        <?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
        <%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
        <a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
    </small></p>
</div>
