<?php
/** Message template for a notification that somebody has sent you a message
 *
 * @param $profile XN_Profile The profile of the message sender
 * @param $body string The message from the user
 */
echo "$body\n\n";
echo "---\n\n";
echo xg_html("MESSAGE_SENT_ON_BEHALF_BY_APP", xg_username($profile), $osAppTitle)."\n\n";
echo "---\n\n";
echo xg_text('TO_VIEW_CLICK_HERE_COLON'), ' ', W_Cache::getWidget('profiles')->buildUrl('message', 'listAlerts') . "\n\n";
