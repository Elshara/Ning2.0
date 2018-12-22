<?php

/**
 * Content object containing the binary data for the audio.
 */
class AudioAttachment extends W_Model {

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
    public $audio;

    /**
    * Whether the AudioAttachment is the original video or MP3-converted
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

    /**
     * The mime type of the photo.
     *
     * @var XN_Attribute::STRING optional
     */
    public $mimeType;

    

/** xn-ignore-start **/
/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go below here */


    public static function create($filename, $track, $isSource, $postVariableName, $mimeType) {
        $audioAttachment = W_Content::create('AudioAttachment');
        $audioAttachment->my->mozzle = W_Cache::current('W_Widget')->dir;
        $audioAttachment->title = $filename;
        $audioAttachment->my->audio = $track->id;
        $audioAttachment->isPrivate = $track->isPrivate;
        $audioAttachment->my->isSource = $isSource ? 'Y' : 'N';
        $audioAttachment->my->mimeType = $mimeType;
        $audioAttachment->set('data', $_POST[$postVariableName], XN_Attribute::UPLOADEDFILE);
        return $audioAttachment;
    }


/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go above here */
/** xn-ignore-end **/

}
