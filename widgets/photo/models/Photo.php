<?php

/**
 * Represents a photo.
 */
class Photo extends W_Model {
    /**
     * The title of the photo.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,200
     * @feature indexing text
     */
    public $title;

    /**
     * The description of the photo.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,4000
     * @feature indexing text
     */
    public $description;

    /**
     * System attribute marking whether photo is available on the pivot and search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * The mime type of the photo.
     *
     * @var XN_Attribute::STRING optional
     */
    public $mimeType;

    /**
     * GMT dates and view counts. Example: 11FEB1977 5, 12FEB1977 10, 07MAR1977 15.
     *
     * @var XN_Attribute::STRING optional
     */
    public $dailyViewCountsForLastMonth;

    /**
     * The popularity value for the photo.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,*
     */
    public $popularityCount;

    /**
     * The visibility of the photo.
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $visibility;
    public $visibility_choices = array('all', 'friends', 'me');

    /**
     * Whether the photo was already approved by the app owner.
     *
     * @var XN_Attribute::STRING
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $approved;
    public $approved_choices = array('Y', 'N');

    /**
     * the id of the "photo added" activity notification log item, used to know when
     * changing visibility of a photo if the notification exists and if it needs property changes
     * @var XN_Attribute::STRING optional
     */
     public $newContentLogItem;

    /**
     * The address where this photo was taken.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,100
     * @feature indexing text
     */
    public $address;

    /**
     * The latitude where the photo was taken.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,100
     */
    public $lat;

    /**
     * The longitude where the photo was taken.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,100
     */
    public $lng;

    /**
     * Extra location info; for now, just the zoom level. Never an empty string - use null instead.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,100
     */
    public $locationInfo;

    /**
     * Name of the location of the photo, e.g., Hawaii
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,100
     * @feature indexing phrase
     */
    public $location;

    /** Max length for the location field */
    const MAX_LOCATION_LENGTH = 100;

    /**
     * How many times this photo has been rated.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,*
     */
    public $ratingCount;

    /**
     * The average rating.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,5
     */
    public $ratingAverage;

    /**
     * The number of detail views of the photo.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,*
     */
    public $viewCount;

    /**
     * When was the photo viewed (on the detail page) the last time.
     *
     * @var XN_Attribute::DATE optional
     */
    public $lastViewedOn;

    /**
     * When the photo was last rated, commented on, or favorited
     *
     * @var XN_Attribute::DATE optional
     */
    public $lastActivityOn;

    /**
     * The number of users that have favorited this photo (for efficiency).
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,*
     */
    public $favoritedCount;

    /**
     * The top 5 tags of the photo.
     *
     * @var XN_Attribute::STRING optional
     * @feature indexing text
     */
    public $topTags;

    /**
     * The rotation of the photos in degrees.
     *
     * @var XN_Attribute::NUMBER optional
     */
    public $rotation;

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

    /**
     * Sets the title of the photo.
     *
     * @param title The new title
     */
    public function setTitle($title) {
        $this->title = mb_substr(mb_ereg_replace("[[:space:]]*(.*)[[:space:]]*", "\\1", $title), 0, 200);
    }

    /**
     * Sets the description of the photo.
     *
     * @param description The new description
     */
    public function setDescription($description) {
        $this->description = mb_substr(html_entity_decode(Photo_HtmlHelper::cleanText($description), ENT_QUOTES, 'UTF-8'), 0, 4000);
    }

    /**
     * Sets the visibility.
     *
     * @param visibility The new visibility
     */
    public function setVisibility($visibility) {
        $this->visibility = $visibility;
        // Make the photo and its supporting objects private to ensure they
        // don't appear in the pivot and search results.  [Jon Aquino 2006-07-31]
        $this->updatePrivacy();
    }

    private function updatePrivacy() {
        $this->isPrivate = (XG_App::contentIsPrivate()) ||
                           ($this->visibility != 'all') ||
                           Photo_PhotoHelper::isAwaitingApproval($this);
    }

    public function setApproved($approved) {
        $this->approved = $approved;
        $this->updatePrivacy();
        // Update the approval-link cache
        W_Controller::invalidateCache(XG_Cache::key('moderation', XN_Application::load(), W_Cache::current('W_Widget')));
    }

    /**
     * Returns a friendly description of the visibility.
     *
     * @return The visibility description
     */
    public function getVisibilityDescription() {
        return Photo_PhotoHelper::getVisibilityDescription($this->visibility);
    }

    /**
     * Increments the view count of this photo.
     *
     * @param $time Used for unit testing
     */
    public function incrementViewCount($time = null) {
        XG_App::includeFileOnce('/lib/XG_PageViewHelper.php');
        XG_PageViewHelper::incrementViewCount($this, false, $time);
    }

    /**
     * Helper function that converts a date object to a string.
     *
     * @param dateObj The date to convert
     * @return The string representation
     */
    protected static function dateToString($dateObj) {
        XG_App::includeFileOnce('/lib/XG_PageViewHelper.php');
        return XG_PageViewHelper::dateToString($dateObj);
    }

    /**
     * Sets the view counts per day for the last month.
     *
     * @param dailyViewCountsForLastMonth An array of date string => view count
     */
    public function setDailyViewCountsForLastMonth($dailyViewCountsForLastMonth) {
        XG_App::includeFileOnce('/lib/XG_PageViewHelper.php');
        XG_PageViewHelper::setDailyViewCountsForLastMonth($this, $dailyViewCountsForLastMonth);
    }

    /**
     * Returns the view counts per day for the last month.
     *
     * @return An array of date string => view count
     */
    public function getDailyViewCountsForLastMonth() {
        XG_App::includeFileOnce('/lib/XG_PageViewHelper.php');
        return XG_PageViewHelper::getDailyViewCountsForLastMonth($this);
    }

    /**
     * Registers for this photo that it has been favorited.
     */
    public function addFavorite() {
        $this->favoritedCount = $this->favoritedCount + 1;
        $this->lastActivityOn = date('c', time());
    }

    /**
     * Registers for this photo that it has been defavorited.
     */
    public function removeFavorite() {
        if($this->favoritedCount > 0) {
            $this->favoritedCount = $this->favoritedCount - 1;
        }
    }

    /**
     * Adds or updates a rating for this photo.
     *
     * @param oldRating The old rating by the user if any
     * @param newRating The new rating
     */
    public function addRating($oldRating, $newRating) {
        $oldTotal = $this->ratingAverage * $this->ratingCount;
        if ($oldRating) {
            $newTotal          = $oldTotal - $oldRating + $newRating;
        } else {
            $newTotal          = $oldTotal + $newRating;
            $this->ratingCount = $this->ratingCount + 1;
        }
        $this->ratingAverage  = $newTotal / $this->ratingCount;
        $this->lastActivityOn = date('c', time());
    }

    /**
     * Rotates the photo once to the left (90 degrees CCW).
     */
    public function rotateLeft() {
        $curRotation = 0;
        if ($this->rotation) {
            $curRotation = (int)$this->rotation;
        }
        $this->rotation = ($curRotation >= 90 ? $curRotation - 90 : $curRotation + 270);
    }

    /**
     * Rotates the photo once to the right (90 degrees CW).
     */
    public function rotateRight() {
        $curRotation = 0;
        if ($this->rotation) {
            $curRotation = (int)$this->rotation;
        }
        $this->rotation = ($curRotation >= 270 ? $curRotation - 270 : $curRotation + 90);
    }

/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go above here */
/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}
