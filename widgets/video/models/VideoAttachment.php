<?php

/**
 * Content object containing the binary data for the video.
 */
class VideoAttachment extends W_Model {

    /**
     * Filename
     *
     * @var XN_Attribute::STRING
     */
    public $title;

    /**
     * System attribute marking whether to make the content available on the pivot and search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * ID of the associated Video object. May be temporarily optional during
     * construction, but should be set ASAP.
     *
     * @var XN_Attribute::STRING optional
     */
    public $video;

    /**
    * Whether the VideoAttachment is the original video or Flash-converted
    *
    * @var XN_Attribute::STRING
    * @rule choice 1,1
    */
    public $isSource;
    public $isSource_choices = array('Y', 'N');

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


    public static function create($filename, $video, $isSource, $postVariableName) {
        $videoAttachment = W_Content::create('VideoAttachment');
        $videoAttachment->my->mozzle = W_Cache::current('W_Widget')->dir;
        $videoAttachment->title = $filename;
        $videoAttachment->my->video = $video->id;
        $videoAttachment->isPrivate = $video->isPrivate;
        $videoAttachment->my->isSource = $isSource ? 'Y' : 'N';
        $videoAttachment->set('data', $_POST[$postVariableName], XN_Attribute::UPLOADEDFILE);
        return $videoAttachment;
    }


/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go above here */
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}
