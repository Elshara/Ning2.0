<?php

/**
 * @see test/VideoTest.php
 */
class Video extends W_Model {

    // For speed, store numbers as strings where not needed for sorting and comparisons  [Jon Aquino 2006-07-24]
    // For speed, store ids instead of content references  [Jon Aquino 2006-07-24]

    /**
     * @var XN_Attribute::STRING optional
     * @rule length 0,200
     */
    public $title;

    /**
     * @var XN_Attribute::STRING optional
     * @rule length 0,4000
     */
    public $description;

    /**
     * System attribute marking whether to make the content available on the pivot and search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * ID of the VideoAttachment that stores the Flash video
     *
     * @var XN_Attribute::STRING optional
     */
    public $videoAttachment;

    /**
     * ID of the VideoAttachment that stores the video prior to being converted to Flash. Deleted after successful conversion.
     *
     * @var XN_Attribute::STRING optional
     */
    public $sourceVideoAttachment;

    /**
     * Not stored if the video is private, as URLs for private objects change.
     *
     * @var XN_Attribute::STRING optional
     */
    public $videoAttachmentUrl;

    /**
     * ID of the VideoPreviewFrame that shows a still from the beginning of the video
     *
     * @var XN_Attribute::STRING optional
     */
    public $previewFrame;

    /**
     * Not stored if the video is private, as URLs for private objects change.
     *
     * @var XN_Attribute::STRING optional
     */
    public $previewFrameUrl;

    /**
     * @var XN_Attribute::STRING optional
     */
    public $previewFrameWidth;

    /**
     * @var XN_Attribute::STRING optional
     */
    public $previewFrameHeight;
    
    /**
     * Duration of the video in milliseconds.
     *
     * @var XN_Attribute::NUMBER optional
     */
    public $duration;

    /**
     * Title + description. Used for Related Videos.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $searchText;

    /**
     * GMT dates and view counts. Example: 11FEB1977 5, 12FEB1977 10, 07MAR1977 15
     *
     * @var XN_Attribute::STRING optional
     */
    public $dailyViewCountsForLastMonth;

    /**
    * @var XN_Attribute::NUMBER
    * @rule range 0,*
    */
    public $popularityCount;

   /**
    * Null if the user supplied a file rather than embed code. Processed with Tidy to avoid injection attacks.
    *
    * @var XN_Attribute::STRING optional
    * @rule length 0,4000
    */
    public $embedCode;

    /**
    * Null if the user supplied embed code rather than a file.
    *
    * @var XN_Attribute::STRING optional
    * @rule choice 1,1
    * @feature indexing phrase
    */
    public $conversionStatus;
    public $conversionStatus_choices = array('in progress', 'complete', 'failed');

    /**
    * @var XN_Attribute::STRING
    * @rule choice 1,1
    * @feature indexing phrase
    */
    public $visibility;
    public $visibility_choices = array('all', 'friends', 'me');

   /**
    * @var XN_Attribute::STRING
    * @rule choice 1,1
    * @feature indexing phrase
    */
    public $approved;
    public $approved_choices = array('Y', 'N');

   /**
    * the id of the "video added" activity notification log item, used to know when
    * changing visibility of a video if the notification exists and if it needs property changes
    * @var XN_Attribute::STRING optional
    */
    public $newContentLogItem;


   /**
    * @var XN_Attribute::STRING optional
    */
    public $reasonForConversionFailure;

    /**
     * The address where this video was recorded.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,100
     * @feature indexing text
     */
    public $address;

    /**
     * The latitude where the video was recorded.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,100
     */
    public $lat;

    /**
     * The latitude where the video was recorded.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,100
     */
    public $lng;

    /**
     * A Named Location e.g. Palo Alto, or Canada
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,200
     * @feature indexing phrase
     */
    public $location;

    /**
     * Extra location info; for now, just the zoom level. Never an empty string; use null instead.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,100
     */
    public $locationInfo;

    /**
     * Needed for player
     *
     * @var XN_Attribute::STRING optional
     * @rule range 0,*
     */
    public $videoSizeInBytes;

    /**
    * @var XN_Attribute::NUMBER
    * @rule range 0,*
    */
    public $ratingCount;

    /**
    * @var XN_Attribute::NUMBER
    * @rule range 0,5
    */
    public $ratingAverage;

    /**
    * @var XN_Attribute::NUMBER
    * @rule range 0,*
    */
    public $viewCount;

    /**
    * @var XN_Attribute::DATE optional
    */
    public $lastViewedOn;

    /**
    * When the video was last rated, commented on, or favorited
    *
    * @var XN_Attribute::DATE optional
    */
    public $lastActivityOn;

    /**
     * Timestamped, comma-delimited list of screen names.
     *
     * @see ContentHelper for utility functions for working with timestamped, comma-delimited lists
     * @var XN_Attribute::STRING optional
     */
    public $recentFavoriters;

    /**
     * Timestamped, comma-delimited list of screen names.
     *
     * @see ContentHelper for utility functions for working with timestamped, comma-delimited lists
     * @var XN_Attribute::STRING optional
     */
    public $recentCommenters;

    /**
     * Timestamped, comma-delimited list of screen names.
     *
     * @see ContentHelper for utility functions for working with timestamped, comma-delimited lists
     * @var XN_Attribute::STRING optional
     */
    public $recentRaters;

    /**
    * @var XN_Attribute::NUMBER
    * @rule range 0,*
    */
    public $favoritedCount;

    /**
     * The top 5 tags of the video.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $topTags;

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     * @feature indexing phrase
     */
    public $mozzle;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go below here */



    public static function create() {
        $video = W_Content::create('Video');
        $video->my->mozzle = W_Cache::current('W_Widget')->dir;
        $video->my->visibility = 'all';
        $video->my->ratingCount = 0;
        $video->my->ratingAverage = 0;
        $video->my->viewCount = 0;
        $video->my->favoritedCount = 0;
        $video->my->popularityCount = 0;
        return $video;
    }



    public function setTitle($title) {
        $this->title = $title;
        $this->searchText = $this->title . ' ' . $this->description;
    }



    public function setDescription($description) {
        $this->description = Video_HtmlHelper::cleanText($description);
        $this->searchText = $this->title . ' ' . $this->description;
    }



    /**
     * @param $time Used for unit testing
     */
    public function incrementViewCount($time=NULL) {
        XG_App::includeFileOnce('/lib/XG_PageViewHelper.php');
        XG_PageViewHelper::incrementViewCount($this, false, $time);
    }



    public static function dateToString($time) {
        XG_App::includeFileOnce('/lib/XG_PageViewHelper.php');
        return XG_PageViewHelper::dateToString($time);
    }



    public function setDailyViewCountsForLastMonth($dailyViewCountsForLastMonth) {
        XG_App::includeFileOnce('/lib/XG_PageViewHelper.php');
        XG_PageViewHelper::setDailyViewCountsForLastMonth($this, $dailyViewCountsForLastMonth);
    }



    public function getDailyViewCountsForLastMonth() {
        XG_App::includeFileOnce('/lib/XG_PageViewHelper.php');
        return XG_PageViewHelper::getDailyViewCountsForLastMonth($this);
    }



    public function addFavoriter($screenName) {
        Video_ContentHelper::add($screenName, $this, 'recentFavoriters', NULL, 10);
        $this->lastActivityOn = date('c', time());
    }

    public function removeFavoriter($screenName) {
        Video_ContentHelper::remove($screenName, $this, 'recentFavoriters');
    }


    public function addCommenter($screenName) {
        Video_ContentHelper::add($screenName, $this, 'recentCommenters', NULL, 10, TRUE);
        $this->lastActivityOn = date('c', time());
    }

    public function addRater($screenName) {
        Video_ContentHelper::add($screenName, $this, 'recentRaters', NULL, 10);
        $this->lastActivityOn = date('c', time());
    }
    
    /**
     * Returns the formatted duration in hours:minutes:seconds for the video or false if there's no duration
     *
     * @param $video XN_Content|W_Content  The video object
     */    
    public static function getDuration($video) {
        if (!$video->my->duration || $video->my->duration == -1) {
            return false;
        } else {
            $duration = $video->my->duration / 1000;
            if ($duration > 3600) {
                $duration = gmdate("H:i:s", $duration);
                $duration = ltrim($duration,'0');
            } else {
                $duration = gmdate("i:s", $duration);
                $duration = str_replace("00:","0:",$duration);
            }
            return $duration;
        }
    }



/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go above here */
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}