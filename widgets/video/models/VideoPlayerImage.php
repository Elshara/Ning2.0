<?php

/**
 * Singleton content object containing the binary data for the image used for the video player.
 */
class VideoPlayerImage extends W_Model {

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     */
    public $mozzle;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go below here */

    /**
     * Sets the video player image to the one being uploaded.
     *
     * @param $postVariableName string  The name of the file field containing the new player image.
     * @return string  The URL of the player image.
     */
    public static function updateWithPostValues($postVariableName) {
        self::delete();
        $videoPlayerImage = XN_Content::create('VideoPlayerImage');
        $videoPlayerImage->my->mozzle = W_Cache::current('W_Widget')->dir;
        $videoPlayerImage->set('data', $_POST[$postVariableName], XN_Attribute::UPLOADEDFILE);
        $videoPlayerImage->save();
        return $videoPlayerImage->fileUrl('data');
    }

    private static function delete() {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'VideoPlayerImage');
        foreach ($query->execute() as $videoPlayerImage) {
            XN_Content::delete($videoPlayerImage);
        }
    }

    public static function load() {
        if (! self::$instanceQueried) {
            $query = XN_Query::create('Content');
            $query->filter('owner');
            $query->filter('type', '=', 'VideoPlayerImage');
            $query->order('createdDate', 'desc', XN_Attribute::DATE);
            $results = $query->execute();
            self::$instance = $results[0];
            self::$instanceQueried = true;
        }
        return self::$instance;
    }

    private static $instance = null;
    private static $instanceQueried = false;

/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go above here */
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}
