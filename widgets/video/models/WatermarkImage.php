<?php

/**
 * Singleton content object containing the binary data for the image used for watermarks.
 */
class WatermarkImage extends W_Model {

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

    public static function updateWithPostValues() {
        self::delete();
        // The transcoder gives us an error if we pass it an object without a title [Jon Aquino 2006-11-09]
        $watermarkImage = XN_Content::create('WatermarkImage', 'WatermarkImage');
        $watermarkImage->my->mozzle = W_Cache::current('W_Widget')->dir;
        $watermarkImage->set('data', $_POST['header_imagefile'], XN_Attribute::UPLOADEDFILE);
        $watermarkImage->save();
    }

    private static function delete() {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'WatermarkImage');
        foreach ($query->execute() as $watermarkImage) {
            XN_Content::delete($watermarkImage);
        }
    }

    public static function load() {
        if (! self::$instanceQueried) {
            $query = XN_Query::create('Content');
            $query->filter('owner');
            $query->filter('type', '=', 'WatermarkImage');
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
