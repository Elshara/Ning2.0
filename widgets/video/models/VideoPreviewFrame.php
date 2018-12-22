<?php

/**
 * Content object containing the binary data for the video's preview frame.
 */
class VideoPreviewFrame extends W_Model {

    /**
     * ID of the associated Video object. May be temporarily optional during
     * construction, but should be set ASAP.
     *
     * @var XN_Attribute::STRING optional
     */
    public $video;

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


    /** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go below here */


    public static function create($video, $postVariableName) {
        $previewFrame = W_Content::create('VideoPreviewFrame');
        $previewFrame->my->mozzle = W_Cache::current('W_Widget')->dir;
        $previewFrame->my->video = $video->id;
        $previewFrame->isPrivate = $video->isPrivate;
        $previewFrame->set('data', $_POST[$postVariableName], XN_Attribute::UPLOADEDFILE);
        return $previewFrame;
    }

    public static function createFromUrl($url, $mimeType) {
        $response = XN_REST::post('/content?binary=true&type=VideoPreviewFrame', self::file_get_contents($url), $mimeType);
        $previewFrame = XN_AtomHelper::loadFromAtomFeed($response, 'XN_Content');
        $previewFrame->my->mozzle = W_Cache::current('W_Widget')->dir;
        return $previewFrame;
    }

    private static function file_get_contents($url) {
        for ($i = 0; $i < 3; $i++) {
            $contents = file_get_contents($url);
            if (preg_match('/test_thumbnail_failure_probability=([0-9.]+)/u', $_POST['title'], $matches)) {
                if (mt_rand(0, 100) < $matches[1] * 100) { $contents = false; }
            }
            if ($contents) { return $contents; }
            Video_LogHelper::log('file_get_contents failed: ' . $url);
        }
        return file_get_contents('images/placeholders/210_generic.gif');
    }

/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go above here */
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}