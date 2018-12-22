<?php
/**
 * Message template for notifying someone that their pending membership has
 * been approved
 *
 * @param $profile XN_Profile The profile for the user that's trying to join
 */
?>
<%= xg_text('CONGRATULATIONS_BANG_YOUR_X_MEMBERSHIP_HAS_BEEN_APPROVED', $message['appName']); %>


<?php
echo xg_text('YOU_CAN_NOW_SIGN_IN_USING_YOUR_EMAIL_ADDRESS_HERE'); ?>

http://<%= $_SERVER['HTTP_HOST'] %><%= User::profileUrl($profile->screenName); %>

<?php
echo "\n" . $message['appName'];
?>