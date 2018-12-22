<?php
/** Message template for a notification that you've signed up.
 *
 * @param $profile XN_Profile The profile of the happy new user
 */
?>
<div class="xg_body">
    <table width="100%">
        <tr>
            <h3><%= xg_html('WELCOME_TO_X_THANK_YOU_FOR_JOINING', xnhtmlentities($message['appName'])) %></h3>
            <td>
                <p>
                    <?php $url = xg_absolute_url('/?' . XG_App::SIGN_IN_IF_SIGNED_OUT . '=1') ?>
                    <%= xg_html('YOU_CAN_SIGN_IN_USING_EMAIL_HERE') %><br />
                    <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a>
                </p>
                <p><%= xnhtmlentities($message['appName']) %></p>

                <?php
                if (mb_strlen($message['appDescription'])) { ?>
                    <p><%= xnhtmlentities($message['appName']) %></p>
                    <p><%= nl2br(xnhtmlentities($message['appDescription'])) %></p>
                <?php
                } ?>
            </td>
            <td><img align="right" width="96" height="96" src="<%= xnhtmlentities(XG_HttpHelper::addParameter(XG_UserHelper::getThumbnailUrl($profile,96,96),'xn_auth','no')) %>" alt="<%= xnhtmlentities(xg_username($profile)) %>" /></td>
        </tr>
    </table>
</div>
