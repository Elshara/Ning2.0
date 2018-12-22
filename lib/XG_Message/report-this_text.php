<?php
/**
 * Message template for a "report this" user report message
 *
 * @param $category string The report category (Adult, etc.)
 * @param $body string The message from the user
 * @param $url string The URL on which the issue occurred
 */
if (XN_Profile::current()->isLoggedIn()) {
    $profileUrl = xg_absolute_url(User::profileUrl(XN_Profile::current()->screenName));
	$name = xg_username(XN_Profile::current());
} else {
	$name = xg_text('SOMEBODY');
}

echo xg_text('X_REPORTED_THE_FOLLOWING_ISSUE_ON_Y', $name, $message['appName']) , "\n\n";

echo xg_text('URL_REPORTED_COLON') . $url . "\n\n";

echo xg_text('ISSUE_COLON') . $category . "\n\n";

echo xg_text('ADDITIONAL_INFORMATION_COLON') . $body . "\n\n";

if (isset($profileUrl)) {
	echo xg_text('SEND_X_A_MESSAGE_HERE', $name) . "\n";
	echo $profileUrl . "\n\n";
}

echo xg_text('FOR_ADDITIONAL_QUESTIONS_OR_TO_CONTACT_NING_PLEASE_CLICK_HERE') . "\n";
echo "http://help.ning.com\n";
