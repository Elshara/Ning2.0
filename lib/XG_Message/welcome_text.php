<?php
/** Message template for a notification that you've signed up.
 *
 * @param $profile XN_Profile The profile of the happy new user
 */

echo xg_text('WELCOME_TO_X_THANK_YOU_FOR_JOINING', $message['appName']) . "\n\n";

echo xg_text('YOU_CAN_SIGN_IN_USING_EMAIL_HERE', $profile->email) . "\n";
echo xg_absolute_url('/?' . XG_App::SIGN_IN_IF_SIGNED_OUT . '=1') . "\n\n";

echo "\n" . $message['appName'];

if (mb_strlen($message['appDescription'])) {
    echo "\n\n\n" . $message['appName'] . "\n";
    echo $message['appDescription'] . "\n\n";
}
