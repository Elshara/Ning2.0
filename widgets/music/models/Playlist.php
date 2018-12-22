<?php

/**
 * Represents an playlist.
 */
class Playlist extends W_Model {
    /**
     * The title of the playlist.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,200
     */
    public $title;

    /**
     * The description of the playlist.
     *
     * @var XN_Attribute::STRING optional
     * @rule length 0,4000
     */
    public $description;

    /**
     * System attribute marking whether playlist is available on the pivot and search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

    /**
     *  If Network Admnistrators are allowed to reorder/edit the playlist or not
     *  used by the main playlist of the network, that can have multiple editors
     *
     * @var XN_Attribute::STRING optional
     */
    public $allowAdminEditing;

    /**
    * The url of the image to be used as the playlist cover photo
    *
    * @see <a href="http://xspf.org/xspf-v1.html#rfc.section.4.1.1.2.7">image element on XSPF</a> 
    * @var XN_Attribute::STRING optional
    */
    public $artworkUrl;

    /**
    * ID of the ImageAttachment that shows the playlist cover image
    *
    * @var XN_Attribute::STRING optional
    */
    public $artworkAtachment;

    /**
     * Comma separated list of the ids of the tracks in the playlist.
     *
     * @var XN_Attribute::STRING optional
     */
    public $tracks;

    /**
     * The number of tracks in the playlist.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,*
     */
    public $trackCount;

    /**
     * The number of detail views of the playlist.
     *
     * @var XN_Attribute::NUMBER
     * @rule range 0,*
     */
    public $viewCount;

    /**
     * The date of the oldest track in the playlist.
     *
     * @var XN_Attribute::DATE optional
     */
    public $startDate;

    /**
     * The date of the newest track in the playlist
     *
     * @var XN_Attribute::DATE optional
     */
    public $endDate;

    /**
     * Which mozzle created this object?
     *
     * @var XN_Attribute::STRING
     */
    public $mozzle;

/** xn-ignore-start 24779211156660397 **/
/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go below here */

public static function create() {
    $playlist = W_Content::create('Playlist');
    $playlist->my->mozzle = W_Cache::current('W_Widget')->dir;
    $playlist->my->trackCount = 0;
    $playlist->my->viewCount = 0;
    $playlist->isPrivate = XG_App::contentIsPrivate();
    return $playlist;
}

/**
 * Returns the playlist object with the given ID
 * Throws an exception if the playlist does not exist.
 * 
 */
public static function load($id) {
    $playlist = XG_Cache::content($id);
    if (! $playlist) { throw new Exception('Playlist not found: ' . $id . ' (344160036234337)'); }
    if ($playlist->type != 'Playlist') { throw new Exception('Not a Playlist: ' . $playlist->type . ' (9724168634042679)'); }
    return $playlist;
}

/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go above here */
/** xn-ignore-end 24779211156660397 **/

}
