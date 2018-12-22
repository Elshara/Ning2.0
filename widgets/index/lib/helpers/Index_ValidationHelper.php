<?php


class Index_ValidationHelper {

    /**
     *  Returns a user object if the argument specifies a valid Ning user, an
     *    email address if the argument is a valid email address, or NULL if
     *    the argument satisfies neither condition
     *
     *  @userId string Username or email address
     */
    public static function validateUserId($userId) {
        $user = null;
        if (isset($userId) && mb_strlen($userId)) {
            try {
                $user = XN_Profile::load($userId);
            } catch (Exception $e) {
                if (Index_ValidationHelper::is_valid_email_address($userId)) {
                    $user = $userId;
                }
            }
        }
        return $user;
    }

    public static function is_valid_email_address($email){
        XG_App::includeFileOnce('/lib/XG_ValidationHelper.php');
        return XG_ValidationHelper::isValidEmailAddress($email);
    }

}

