<?php
/** Message template for an invitation (text only version)
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
$share = isset($share) ? $share : false;

if ($share) {
    if (isset($type) && mb_strlen($type)) {
        $clickMessage = xg_text('TO_VIEW_THIS_X_VISIT', $type);
    } else {
        $clickMessage = xg_text('TO_VIEW_IT_VISIT');
    }
} else {
    $clickMessage = xg_text('CLICK_HERE_TO_JOIN_COLON');
}

//  Build message body
if ($share && (! $fromProfile || ! $fromProfile->isLoggedIn())) {
    // For the signed-out Share This page [Jon Aquino 2007-10-26]
    echo xg_text('CHECK_OUT_TITLE_ON_APPNAME', $title, $message['appName']);
} elseif ($share) {
    echo xg_text('X_WANTS_YOU_TO_CHECK_OUT_Y_ON_Z', xg_username($fromProfile), $title, $message['appName']);
} else {
    echo xg_text('X_HAS_INVITED_YOU_TO_JOIN_Y', xg_username($fromProfile), $message['appName']);
}

echo "\n\n";
if ($body) { echo "$body\n\n"; }
echo "$clickMessage\n";
echo "$url\n\n";

if ($message['unsubscribeUrl']) {
	echo "--\n";
	echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
	echo $message['unsubscribeUrl'];
} else {
	// no unsubscribeUrl means it's called from SharingController to render a message for signed out user.
}
