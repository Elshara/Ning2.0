<?php
/**
 * Message template for notifying an administrator that someone has joined
 * and needs to be moderated
 *
 * @param $joiner XN_Profile The profile for the user that's trying to join
 */
echo xg_text('YOU_HAVE_A_NEW_MEMBER_TO_APPROVE_ON_X', $message['appName']) . "\n\n";

echo xg_text('NAME_COLON') . " " . xg_username($joiner) . "\n";
echo xg_text('EMAIL_COLON') . " " . $joiner->email . "\n";
echo "\n";

echo xg_text('TO_APPROVE_XS_PROFILE_VISIT', xg_username($joiner)) . "\n";
echo W_Cache::getWidget('profiles')->buildUrl('profile','showPending',array('id' => $joiner->screenName)) . "\n\n";

echo "--\n";
echo xg_text('TO_CONTROL_WHICH_EMAILS_YOUR_RECEIVE_ON_X_GO_TO', $message['appName']) . "\n";
echo $message['unsubscribeUrl'];
?>