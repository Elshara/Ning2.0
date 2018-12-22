<?php

/**
 * Content object containing the binary data for the album artwork.
 */
class ImageAttachment extends W_Model {

    /**
     * ID of the associated content object.
     *
     * @var XN_Attribute::STRING optional
     */
    public $contentId;

    /**
     * System attribute marking whether to make the content available on the pivot and search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Which mozzle created this object? May be temporarily optional during
     * construction, but should be set ASAP.
     *
     * @var XN_Attribute::STRING optional
     */
    public $mozzle;


    /** xn-ignore-start 8559732172503315 **/
/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go below here */


    public static function create($content, $postVariableName) {
        $image = W_Content::create('ImageAttachment');
        $image->my->mozzle = W_Cache::current('W_Widget')->dir;
        $image->my->contentId = $content->id;
        $image->isPrivate = $content->isPrivate;
        $image->set('data', $_POST[$postVariableName], XN_Attribute::UPLOADEDFILE);
        return $image;
    }
    

    public static function createFromUrl($url, $mimeType) {
        $response = XN_REST::post('/content?binary=true&type=ImageAttachment', self::file_get_contents($url), $mimeType);
        $image = XN_AtomHelper::loadFromAtomFeed($response, 'XN_Content');
        $image->my->mozzle = W_Cache::current('W_Widget')->dir;
        return $image;
    }

    private static function file_get_contents($url) {
        for ($i = 0; $i < 3; $i++) {
            $contents = file_get_contents($url);
            if ($contents) { return $contents; }
            error_log('file_get_contents failed: ' . $url);
        }
        //@TODO return a generic image placeholder if the fetch fails
        //return file_get_contents('images/placeholders/210_generic.gif');
        return null;
    }

/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go above here */
/** xn-ignore-end 8559732172503315 **/

}