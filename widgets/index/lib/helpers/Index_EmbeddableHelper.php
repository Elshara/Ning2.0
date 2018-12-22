<?php

/**
 * Useful functions for working with <embed> embeddables.
 */
class Index_EmbeddableHelper {

    /**
     * Regenerates the files used by the network badge.
     * Possibly expensive; call this function infrequently (e.g., when the app
     * version changes, when the app name or description changes, when
     * a member is added or removed, every hour, etc.)
     *
     * @param $appName string  (optional) the name of the app
     * @param $appDescription string  (optional) the description of the app
     */
    public static function generateResources($appName, $appDescription) {
        list($users, $numMembers) = self::getUsers();
        if (! XG_App::appIsPrivate()) { $avatarGridImageUrl = self::generateAvatarGridImage($users); }
        self::generateBadgeConfigXml($numMembers, $avatarGridImageUrl, $appName, $appDescription);
    }

    /**
     * Returns the 16 most active users.
     *
     * @return array  the User objects and the total member count
     */
    private static function getUsers() {
        $maxUserCount = 16;
        // Sort by xg_forum_activityCount, to approximate sorting by general activity [Jon Aquino 2007-06-11]
        // 2 queries, to work around NING-5231  [Jon Aquino 2007-06-11]
        $userData = User::find(array('my->xg_forum_activityCount' => array('<>', null, XN_Attribute::NUMBER)), 0, $maxUserCount, array('my->xg_forum_activityCount', XN_Attribute::NUMBER), 'desc');
        $users = $userData['users'];
        $numMembers = $userData['numUsers'];
        // Get users with null activity counts to (1) reach the $maxUserCount, if we haven't reached it already (BAZ-3365)
        // (2) get the total $numMembers (BAZ-3833) [Jon Aquino 2007-07-14]
        $userData = User::find(array('my->xg_forum_activityCount' => array('=', null, XN_Attribute::NUMBER)), 0, $maxUserCount, 'updatedDate');
        $users = array_slice(array_merge($users, $userData['users']), 0, $maxUserCount);
        $numMembers += $userData['numUsers'];
        return array($users, $numMembers);
    }

    /**
     * Regenerates the badge-config.xml file.
     *
     * @param $numMembers int  the number of network members
     * @param $avatarGridImageUrl string  URL to the avatar-grid image, or null if none exists
     * @param $appName string  (optional) the name of the app
     * @param $appDescription string  (optional) the description of the app
     */
    private static function generateBadgeConfigXml($numMembers, $avatarGridImageUrl, $appName, $appDescription) {
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        $avatarGridImageUrl = is_null($avatarGridImageUrl) ? '' : $avatarGridImageUrl;
        XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <config>
                <backgroundColor>' . xg_xmlentities(XG_EmbeddableHelper::getBackgroundColor()) . '</backgroundColor>
                <backgroundImageUrl>' . xg_xmlentities(XG_EmbeddableHelper::getBackgroundImageUrl()) . '</backgroundImageUrl>
                <networkName>' . xg_xmlentities(is_null($appName) ? XN_Application::load()->name : $appName) . '</networkName>
                <networkNameCss>' . xg_xmlentities('h1 { font-family: ' . XG_EmbeddableHelper::getNetworkNameFontFamily() . '; color: #' . XG_EmbeddableHelper::getNetworkNameColor() . '; }') . '</networkNameCss>
                <description>' . xg_xmlentities(is_null($appDescription) ? XG_MetatagHelper::appDescription() : $appDescription) . '</description>
                <logoUrl>' . xg_xmlentities(XG_EmbeddableHelper::getBadgeLogoUrl()) . '</logoUrl>
                <logoWidth>' . xg_xmlentities(XG_EmbeddableHelper::getBadgeLogoWidth()) . '</logoWidth>
                <logoHeight>' . xg_xmlentities(XG_EmbeddableHelper::getBadgeLogoHeight()) . '</logoHeight>
                <avatarGridImageUrl>' . xg_xmlentities($avatarGridImageUrl) . '</avatarGridImageUrl>
                <memberCountText>' . xg_xmlentities(xg_text('N_MEMBERS', xg_number($numMembers))) . '</memberCountText>
                <joinUsText>' . xg_xmlentities(xg_text('JOIN_US')) . '</joinUsText>
                <iAmMemberText>' . xg_xmlentities(xg_text('I_AM_MEMBER')) . '</iAmMemberText>
                <createdByText>' . xg_xmlentities(xg_text('CREATED_BY_COLON')) . '</createdByText>
                <networkCreatorName>' . xg_xmlentities(XG_UserHelper::getFullName(XG_Cache::Profiles(XN_Application::load()->ownerName))) . '</networkCreatorName>
            </config>';
        $directory = dirname(XG_EmbeddableHelper::getBadgeConfigXmlPath());
        if (! file_exists($directory)) { @mkdir($directory, 0777, true); }
        file_put_contents(XG_EmbeddableHelper::getBadgeConfigXmlPath(), $xml);
    }

    /**
     * Regenerates the avatar_grid.png file.
     *
     * @param $users array  the User objects to display in the grid of avatars
     * @return  the URL for the image
     */
    private static function generateAvatarGridImage($users) {
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        $gridExtent = 4;
        $avatarExtent = 48;
        $gridImage = imagecreatetruecolor(1 + $gridExtent + ($gridExtent*$avatarExtent), 1 + $gridExtent + ($gridExtent*$avatarExtent));
        imagealphablending($gridImage, false);
        imagesavealpha($gridImage, true);
        imagefill($gridImage, 0, 0, imagecolorallocatealpha($gridImage, 255, 255, 255, 127));
        $profiles = XG_Cache::profiles($users);
        for ($y = 0; $y < $gridExtent; $y++) {
                for ($x = 0; $x < $gridExtent; $x++) {
                if (count($profiles) == 0) { continue; }
                $avatarImage = self::createImage(XG_UserHelper::getThumbnailUrl(array_pop($profiles), $avatarExtent, $avatarExtent));
                if (! $avatarImage) { continue; }
                // Returned thumbnail may not be 48x48  (NING-5611)  [Jon Aquino 2007-06-08]
                $avatarX = max(0, (imagesx($avatarImage)/2) - ($avatarExtent/2));
                $avatarY = max(0, (imagesy($avatarImage)/2) - ($avatarExtent/2));
                imagecopy($gridImage, $avatarImage, 1 + $x + ($x*$avatarExtent), 1 + $y + ($y*$avatarExtent), $avatarX, $avatarY, $avatarExtent, $avatarExtent);
                imagedestroy($avatarImage);
                imagerectangle($gridImage, $x + ($x*$avatarExtent), $y + ($y*$avatarExtent), $x + ($x*$avatarExtent) + $avatarExtent + 1, $y + ($y*$avatarExtent) + $avatarExtent + 1, imagecolorallocatealpha($gridImage, 0, 0, 0, 63));
            }
        }

        $gridImageFilename = NF_APP_BASE . '/xn_resources/instances/main/embeddable/avatar_grid.png';
        @mkdir(dirname($gridImageFilename), 0750, true);
        imagepng($gridImage, $gridImageFilename);
        imagedestroy($gridImage);
        chmod($gridImageFilename, 0750);
        $gridImageUrl = 'http://static' . XN_AtomHelper::$DOMAIN_SUFFIX
                . '/' . XN_Application::load()->relativeUrl
                . mb_substr($gridImageFilename, mb_strlen(NF_APP_BASE . '/xn_resources'));
        $gridImageUrl = XG_EmbeddableHelper::addGenerationTimeParameter($gridImageUrl);
        return $gridImageUrl;
    }

    /**
     * Creates an image from the given URL
     *
     * @param $url string  URL of the image
     * @return the image identifier, or null if the image could not be created
     */
    private static function createImage($url) {
        $metadata = getimagesize($url);
        if (preg_match('/png/iu', $metadata['mime'])) { return imagecreatefrompng($url); }
        if (preg_match('/gif/iu', $metadata['mime'])) { return imagecreatefromgif($url); }
        if (preg_match('/jpg/iu', $metadata['mime'])) { return imagecreatefromjpeg($url); }
        if (preg_match('/jpeg/iu', $metadata['mime'])) { return imagecreatefromjpeg($url); }
        if (preg_match('/bmp/iu', $metadata['mime'])) {
            XG_App::includeFileOnce('/lib/XG_ImageHelper.php');
            $tempFilename = tempnam($_SERVER['DOCUMENT_ROOT'].'/xn_private/xn_volatile', 'temp_bmp_');
            file_put_contents($tempFilename, file_get_contents($url));
            $image = XG_ImageHelper::ImageCreateFromBMP($tempFilename);
            unlink($tempFilename);
            return $image;
        }
        return null;
    }

    /**
     * Creates a version of the specified image tiled to the specified dimensions,
     *   stores it in a new content object, and returns the file API URL for that
     *   object
     *
     * Or, if the image is already as big as the specified dimensions, just returns
     *   the supplied URL
     *
     * @param $url string URL of the image to be tiled
     * @param $width integer Width of the desired image
     * @param $height integer Height of the desired image
     * @param $type string Content type for new image object
     * @param string (optional) A prefix for the filename, default 'tileimg_'.
     *   The new image filename will be the prefix plus some random (guaranteed
     *   unique) characters.
     *
     * @return string URL of the new image
     */
    public static function getTiledImageUrl($url, $width, $height, $type,
            $namePrefix= 'tileimg_') {
        $imageInfo = getimagesize($url);
        if ($imageInfo['width'] >= $width && $imageInfo['height'] >= $height) {
            return $url;
        }

        //  Create a new empty image of the appropriate size - at least as big
        //    as the requested dimensions but no smaller than the tile image
        $tileImage = self::createImage($url);
        $newWidth = max(imagesx($tileImage), $width);
        $newHeight = max(imagesy($tileImage), $height);
        $targetImage = imagecreatetruecolor($newWidth, $newHeight);

        //  Preserve transparency
        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);

        //  Tile the tile image across the target image starting upper left
        imagesettile($targetImage, $tileImage);
        imagefill($targetImage, 0, 0, IMG_COLOR_TILED);
        imagedestroy($tileImage);

        //  Write the image to a new file in xn_resources (to be served by the CDN)
        $newFilename = tempnam(NF_APP_BASE . '/xn_resources/instances/main/embeddable/',
                $namePrefix);
        imagepng($targetImage, $newFilename);
        imagedestroy($targetImage);
        chmod($newFilename, 0750);
        $newImageUrl = 'http://static' . XN_AtomHelper::$DOMAIN_SUFFIX
                . '/' . XN_Application::load()->relativeUrl
                . mb_substr($newFilename, mb_strlen(NF_APP_BASE . '/xn_resources'));
        $newImageUrl = XG_HttpHelper::addParameter($newImageUrl,'width', $newWidth);
        $newImageUrl = XG_HttpHelper::addParameter($newImageUrl,'height', $newHeight);
        $newImageUrl = XG_HttpHelper::addParameter($newImageUrl,'xn_auth', 'no');
        $newImageUrl = XG_HttpHelper::addParameter($newImageUrl,'type', 'png');
        return urldecode($newImageUrl);
    }

}