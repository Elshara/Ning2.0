<?php
/**
 * Message template for a generic message
 *
 * @param $body string
 * @param $url string optional URL to include in message
 * @param $anchor string optional Link text for the URL
 *
 * The message can also use the message properties set in $message
 */
// TODO: Why do we say Click Here here? [Jon Aquino 2008-03-28]
$anchor = isset($anchor) ? $anchor : xg_text('CLICK_HERE');

echo ($body ? "$body\n\n" : "");

if (isset($url)) {
    echo "$anchor\n";
    echo "$url\n\n";
}

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
