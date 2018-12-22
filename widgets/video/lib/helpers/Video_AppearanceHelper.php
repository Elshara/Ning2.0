<?php

/**
 * Utility functions for the Appearance Panel in the Ningbar.
 */

// TODO: Move most of these functions into Photos, as Videos no longer uses them [Jon Aquino 2007-01-16]

class Video_AppearanceHelper {
    /**
     * Returns the URL for the image to use for the logo in a Flash player, or
     * null if no logo is available. Other widgets (such as Photos) may call this function -
     * their widget-configuration.xml must contain public attributes named "logoImageUrl"
     * and "playerImageUrl", which this function will store values in.
     *
     * If the action calling this function is cached (using setCaching), it should include
     * Index_AppearanceHelper::APPEARANCE_CHANGED as one of its invalidation conditions.
     * Then when the site owner uploads a new header image, this function will be called to
     * update the player image.
     *
     * @param $name string unique identifier for the player image e.g. VideoPlayerImage (currently being used for a content-object type)
     * @param $maxHeight integer maximum height in pixels for the player image; if the original image
     *         is smaller, the player image will have the same size
     * @param string backgroundHex e.g. #cccccc - color of the background; affects transparent images only
     * @return string the URL, or null if there is no image
     */
    public static function getPlayerImageUrl($name, $maxHeight, $backgroundHex) {
        $mainWidget = W_Cache::getWidget('main');
        $currentWidget = W_Cache::current('W_Widget');
        if ($mainWidget->privateConfig['logoImageUrl'] == $currentWidget->privateConfig['logoImageUrl']) {
            return $currentWidget->privateConfig['playerImageUrl'] ? $currentWidget->privateConfig['playerImageUrl'] : null;
        }
        $currentWidget->privateConfig['logoImageUrl'] = $mainWidget->privateConfig['logoImageUrl'];
        // Update config immediately to minimize concurrency issues. [Jon Aquino 2006-12-21]
        $currentWidget->saveConfig();
        self::deletePlayerImage($name);
        if (! $mainWidget->privateConfig['logoImageUrl']) {
            $currentWidget->privateConfig['playerImageUrl'] = '';
            $currentWidget->saveConfig();
            return null;
        }
        $playerImage = self::createPlayerImage($name, $mainWidget->privateConfig['logoImageUrl'], $mainWidget->privateConfig['logoImageName'], $maxHeight, $backgroundHex);
        $currentWidget->privateConfig['playerImageUrl'] = $playerImage->fileUrl('data');
        $currentWidget->saveConfig();
        return $currentWidget->privateConfig['playerImageUrl'];
    }

    private static function deletePlayerImage($name) {
        $oldImages = XN_Query::create('Content')
                ->filter('owner')
                ->filter('type', '=', $name)
                ->execute();
        if (count($oldImages) > 0) {
            XN_Content::delete($oldImages);
        }
    }

    private static function createPlayerImage($name, $logoImageUrl, $originalFilename, $maxHeight, $backgroundHex) {
        $widget = W_Cache::current('W_Widget');
        $outputDirectory = $_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile';
        if (! file_exists($outputDirectory)) { @mkdir($outputDirectory, 0777, true); }
        $outputPath = tempnam($outputDirectory, 'player_image');
        $upperImage = self::createImage($logoImageUrl, $originalFilename);
        $backgroundRGB = self::hexToRGB($backgroundHex);
        $lowerImage = imageCreateTrueColor(imagesx($upperImage), imagesy($upperImage));
        imagefill($lowerImage, 0, 0, imagecolorallocate($lowerImage, $backgroundRGB[0], $backgroundRGB[1], $backgroundRGB[2]));
        imagecopy($lowerImage, $upperImage, 0, 0, 0, 0, imagesx($upperImage), imagesy($upperImage));
        if (imagesy($upperImage) < $maxHeight) {
            // Set imageinterlace to 0 as Flash does not support progressive JPEGs  [Jon Aquino 2006-08-10]
            imageinterlace($lowerImage, 0);
            imagejpeg($lowerImage, $outputPath);
        } else {
            $smallImage = imagecreatetruecolor(imagesx($upperImage) * $maxHeight / imagesy($upperImage), $maxHeight);
            imagecopyresampled($smallImage, $lowerImage, 0, 0, 0, 0, imagesx($smallImage), imagesy($smallImage), imagesx($lowerImage), imagesy($lowerImage));
            imageinterlace($smallImage, 0);
            imagejpeg($smallImage, $outputPath);
            imagedestroy($smallImage);
        }
        imagedestroy($lowerImage);
        imagedestroy($upperImage);
        $response = XN_REST::post( '/content?binary=true&type=' . $name, file_get_contents($outputPath), 'image/jpeg');
        $playerImage = XN_AtomHelper::loadFromAtomFeed( $response, 'XN_Content');
        unlink($outputPath);
        return $playerImage;
    }

    public static function createImage($url, $originalFilename) {
        if (preg_match('/png$/ui', $originalFilename)) { return imagecreatefrompng($url); }
        if (preg_match('/jpg$/ui', $originalFilename)) { return imagecreatefromjpeg($url); }
        if (preg_match('/jpeg$/ui', $originalFilename)) { return imagecreatefromjpeg($url); }
        if (preg_match('/gif$/ui', $originalFilename)) { return imagecreatefromgif($url); }
        $image = @imagecreatefrompng($url);
        if ($image) { return $image; }
        $image = @imagecreatefromjpeg($url);
        if ($image) { return $image; }
        $image = @imagecreatefromgif($url);
        if ($image) { return $image; }
        return imagecreate(1, 1);
    }

    private static function hexToRGB($hex) {
        if ($hex[0] === "#") { $hex = mb_substr($hex, 1); }
        // Code from cory@lavacube.com, "dechex", http://ca3.php.net/manual/en/function.dechex.php
        // [Jon Aquino 2005-11-02]
        $rgb = array();
        $rgb[0] = hexdec(mb_substr($hex, 0, 2));
        $rgb[1] = hexdec(mb_substr($hex, 2, 2));
        $rgb[2] = hexdec(mb_substr($hex, 4, 2));
        return $rgb;
    }

    public static function updateFilesIfNecessary() { }
    public static function bodyBackgroundWhite() { return true; }
}
