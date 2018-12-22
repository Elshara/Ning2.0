<?php

/**
 * Represents a playable track (audio).
 */
class Track extends W_Model {
    
    /**
    * The title of the track item, usually "artist - track title" so it looks nice on atom feeds.
    *
    * @var XN_Attribute::STRING optional
    */
    public $title;
    
    /**
    * The title of the track.
    *
    * @var XN_Attribute::STRING optional
    * @see <a href="http://xspf.org/xspf-v1.html#rfc.section.4.1.1.2.14.1.1.1.3">title element on XSPF</a> 
    */
    public $trackTitle;
    
    /**
    * The name of the artist or band
    *
    * @var XN_Attribute::STRING optional
    * @see <a href="http://xspf.org/xspf-v1.html#rfc.section.4.1.1.2.14.1.1.1.4">creator element on XSPF</a> 
    * @see <a href="http://musicbrainz.org/doc/ArtistName">ArtistName on MusicBrainz Terminology</a> 
    */
    public $artist;
    
    /**
    * The Url of the track details page
    *
    * @var XN_Attribute::STRING optional
    * @see <a href="http://xspf.org/xspf-v1.html#rfc.section.4.1.1.2.14.1.1.1.6">info element on XSPF</a> 
    */
    public $infoUrl;
    
    /**
    * The name of the album or collection from which the track comes
    *
    * @var XN_Attribute::STRING optional
    * @see <a href="http://xspf.org/xspf-v1.html#rfc.section.4.1.1.2.14.1.1.1.8">album element on XSPF</a> 
    * @see <a href="http://musicbrainz.org/doc/ReleaseTitle">ReleaseTitle on MusicBrainz Terminology</a> 
    */
    public $album;
    
    /**
    * The url of the main artwork/album cover
    *
    * @see <a href="http://xspf.org/xspf-v1.html#rfc.section.4.1.1.2.14.1.1.1.7">image element on XSPF</a> 
    * @var XN_Attribute::STRING optional
    */
    public $artworkUrl;
    
    /**
    * The url of the main AudioAttachment
    *
    * @var XN_Attribute::STRING optional
    * @see <a href="http://xspf.org/xspf-v1.html#rfc.section.4.1.1.2.14.1.1.1.1">location element on XSPF</a> 
    */
    public $audioUrl;
    
    /**
    * The description of the track.
    *
    * @var XN_Attribute::STRING optional
    * @see <a href="http://xspf.org/xspf-v1.html#rfc.section.4.1.1.2.14.1.1.1.5">annotation element on XSPF</a> 
    */
    public $description;
    
    /**
    * The duration of the main AudioAttachment in miliseconds.
    *
    * @var XN_Attribute::NUMBER optional
    * @see <a href="http://www.xspf.org/xspf-v1.html#rfc.section.4.1.1.2.14.1.1.1.10">duration element on XSPF</a> 
    */
    public $duration;

    /**
    * The length of the main AudioAttachment file in bytes.
    *
    * @var XN_Attribute::NUMBER optional
    */
    public $length;

    /**
    * The genre of the track.
    *
    * @var XN_Attribute::STRING optional
    */
    public $genre;
    
    /**
    * The release year of the track
    *
    * @see <a href="http://musicbrainz.org/doc/ReleaseEventStyle">ReleaseDate on MusicBrainz</a>
    * @var XN_Attribute::STRING optional
    */
    public $year;
    
    /**
    * The "record label" related with that track release
    * @see <a href="http://musicbrainz.org/doc/LabelName">LabelName on MusicBrainz</a>
    * @see <a href="http://musicbrainz.org/doc/Label">Label on MusicBrainz</a>
    *
    * @var XN_Attribute::STRING optional
    */
    public $label;
    
    /**
    * The url of the artist website
    *
    * @var XN_Attribute::STRING optional
    */
    public $artistUrl;
    
    /**
    * The url of the website that is providing the track (used by external mp3 entries)
    *
    * @var XN_Attribute::STRING optional
    */
    public $trackHostUrl;
    
    /**
    * The url of the label website
    *
    * @var XN_Attribute::STRING optional
    */
    public $labelUrl;
    
    /**
    * The url of the license used by the track
    *
    * @var XN_Attribute::STRING optional
    */
    public $licenseUrl;
    
    /**
    * The name of the license used by the track
    *
    * @var XN_Attribute::STRING optional
    */
    public $licenseName;
    
    /**
    * If the track contains explicit material
    *
    * @see <a href="http://www.apple.com/itunes/store/podcaststechspecs.html#_Toc526931684">explicit tag on iTunes RSS</a>
    * @var XN_Attribute::STRING optional
    */
    public $explicit;
    public $explicit_choices = array('yes', 'no', 'clean');
    
    /**
    * If the download link is displayed by the Bazel music player or not
    *
    * @var XN_Attribute::STRING optional
    */
    public $enableDownloadLink;
    
    /**
    * If the "Add to: My Page, Main Page" link is displayed by the Bazel music player or not
    *
    * @var XN_Attribute::STRING optional
    */
    public $enableProfileUsage;
    
    /**
    * The artist name used for sorting (ex. "Beatles, The" for "The Beatles")
    *
    * @see <a href="http://musicbrainz.org/doc/ArtistSortName">Artist Sort Name on MusicBrainz</a>
    * @see <a href="http://musicbrainz.org/doc/SortNameStyle">Style Guidelines for sorting artist names on MusicBrainz</a>
    * @var XN_Attribute::STRING optional
    */
    public $sortArtist;
    
    /*
    * Array with key=>value pairs extracted from the id3 tag of a track
    */
    public $id3Frames;
    
    /**
    * ID of the main ImageAttachment that shows the album cover
    *
    * @var XN_Attribute::STRING optional
    * @see <a href="http://xspf.org/xspf-v1.html#rfc.section.4.1.1.2.14.1.1.1.8">album element on XSPF</a> 
    */
    public $artworkAtachment;
    
    /**
    * The ID of the main AudioAttachment content object (the one that stores the mp3 format file).
    *
    * @var XN_Attribute::STRING optional
    */
    public $audioAttachment;

    /**
    * @var XN_Attribute::STRING
    * @rule choice 1,1
    */
    public $visibility;
    public $visibility_choices = array('all', 'friends', 'me');
    
    /**
    * @var XN_Attribute::STRING
    * @rule choice 1,1
    */
    public $approved;
    public $approved_choices = array('Y', 'N');
    
    /**
    * @var XN_Attribute::NUMBER
    * @rule range 0,*
    */
    public $playCount;
    
    /**
    * @var XN_Attribute::DATE optional
    */
    public $lastPlayedOn;
    
    /**
    * The filename of the uploaded file
    *
    * @var XN_Attribute::STRING optional
    */
    public $filename;
    
    /**
    * Which mozzle created this object?
    *
    * @var XN_Attribute::STRING
    */
    public $mozzle;
    
    /**
    * System attribute marking whether to make the content available on the pivot and search results.
    *
    * @var XN_Attribute::STRING
    */
    public $isPrivate;
    
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
    public $favoritedCount;
    
    /**
    * @var XN_Attribute::NUMBER
    * @rule range 0,*
    */
    public $popularityCount;
    

    
/** xn-ignore-start 8646226236959499 **/
/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go below here */

public static function create() {
    $track = W_Content::create('Track');
    $track->my->mozzle = W_Cache::current('W_Widget')->dir;
    $track->my->visibility = 'all';
    $track->my->approved = 'Y';
    $track->my->ratingCount = 0;
    $track->my->ratingAverage = 0;
    $track->my->playCount = 0;
    $track->my->favoritedCount = 0;
    $track->my->popularityCount = 0;
    $track->my->enableDownloadLink = 'on';
    $track->my->enableProfileUsage = 'on';
    $track->isPrivate = XG_App::contentIsPrivate();
    return $track;
}

/**
 * Returns the track object with the given ID
 * Throws an exception if the track does not exist.
 * 
 */
public static function load($id) {
    $track = XG_Cache::content($id);
    if (! $track) { throw new Exception('Track not found: ' . $id . ' (009203953862738423)'); }
    if ($track->type != 'Track') { throw new Exception('Not a Track: ' . $track->type . ' (6185523435906997)'); }
    return $track;
}

/** You can put any additional property definitions
  * anywhere you want but other code (other variables,
  * methods, etc.) should go above here */
/** xn-ignore-end 8646226236959499 **/
}
