<?php

/**
 * Represents an album.
 */
class Album extends W_Model {
    /**
     * The title of the album.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,200
     * @feature indexing text
     */
    public $title;

    /**
     * The description of the album.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,4000
     * @feature indexing text
     */
    public $description;

    /**
     * System attribute marking whether album is available on the pivot and search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     * Albums are hidden until one of their photos becomes approved and visible to others.
     * null implies 'N'.
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @since 1.7
     * @feature indexing phrase
     */
    public $hidden;
    public $hidden_choices = array('Y', 'N');

    /**
     * "Y" indicates that this group should be excluded from Ningbar and widget
     * search results. This is true of albums that are hidden
     *
     * @var XN_Attribute::STRING optional
     * @rule choice 1,1
     * @feature indexing phrase
     */
    public $excludeFromPublicSearch;
    public $excludeFromPublicSearch_choices = array('Y', 'N');

    /**
     * The id of the cover photo.
     *
     * @var XN_Attribute::STRING optional
     */
    public $coverPhotoId;
    
    /**
     * the id of the "album added" activity notification log item, used to know when
     * changing visibility of a album if the notification exists and if it needs property changes
     * @var XN_Attribute::STRING optional
     */
     public $newContentLogItem;

    /**
     * Comma separated list of the ids (with timestamps) of the photos in the album.
     *
     * @var XN_Attribute::STRING optional
     */
    public $photos;

    /**
     * The number of photos in the album.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,*
     */
    public $photoCount;

    /**
     * The number of detail views of the album.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,*
     */
    public $viewCount;

    /**
     * The date of the oldest photo in the album.
     *
     * @var XN_Attribute::DATE optional
     */
    public $startDate;

    /**
     * The date of the newest photo in the album
     *
     * @var XN_Attribute::DATE optional
     */
    public $endDate;

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
     * Sets the title of the album.
     *
     * @param title The new title
     */
    public function setTitle($title) {
        $this->title = mb_substr(html_entity_decode(Photo_HtmlHelper::cleanText(mb_ereg_replace("[[:space:]]*(.*)[[:space:]]*", "\\1", $title))), 0, 200);
    }

    /**
     * Sets the description of the album.
     *
     * @param description The new description
     */
    public function setDescription($description) {
        $this->description = mb_substr(html_entity_decode(Photo_HtmlHelper::cleanText($description)), 0, 4000);
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}


