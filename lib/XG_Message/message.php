<?php
/**
 * Message template for a generic message
 *
 * @param $subject string
 * @param $body string
 * @param $url string optional URL to include in message
 * @param $anchor string optional Link text for the URL
 *
 * The message can also use the message properties set in $message
 */
//TODO $reason is not longer in use ... we just send the standard TO CONTROL message.
// We should rip out everything to do with $reason everywhere else.

//TODO "Click Here" ???
$anchor = isset($anchor) ? $anchor : xg_html('CLICK_HERE');
?>
    <div class="xg_body">
        <table width="100%">
            <tr>
                <td>
                    <?php if ($body) { ?>
                        <p><big><%= nl2br(xnhtmlentities($body)) %></big></p>
                    <?php } ?>
                    <?php if (isset($url)) { ?>
                        <p><%= xnhtmlentities($anchor) %>:<br />
                            <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($url) %></a></p>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <p class="smallprint"><small>
            <?php $unsubUrl = xnhtmlentities($message['unsubscribeUrl']); ?>
            <%= xg_html('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', xnhtmlentities($message['appName'])) %><br />
            <a href="<%= $unsubUrl %>"><%= $unsubUrl %></a>
        </small></p>
    </div>
