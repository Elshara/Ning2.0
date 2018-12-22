<?php
/**
 * Message template for a generic user feedback message
 *
 * @param $body string The message from the user
 * @param $heading string The message heading
 */
$helpUrl = 'http://help.ning.com';
$profileUrl = xg_absolute_url(User::profileUrl(XN_Profile::current()->screenName));

echo $heading . "\n\n";
echo $body . "\n\n";

echo xg_text('SEND_X_A_MESSAGE_HERE', $feedbackSenderName) . "\n$profileUrl\n\n";
echo xg_text('FOR_ADDITIONAL_QUESTIONS_OR_TO_CONTACT_NING_PLEASE_CLICK_HERE') . "\n" . $helpUrl;
