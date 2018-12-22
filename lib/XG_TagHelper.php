<?php

/**
 * Useful functions for working with tags.
 */
class XG_TagHelper {

    /** Max number of characters allowed in Tags fields. */
    const MAX_TAGS_LENGTH = 2000;

    public static function orderTagNames($tags) {
        uksort($tags, array(new XG_TagSortWrapper($tags), "tagSortCmp"));
        return $tags;
    }

    /**
     * Return the given number of tag names
     * for the given content object.
     *
     * @param object  the content object, or its ID
     * @param numTags Maximum number of tags to return
     * @return array  The tag names, beginning with the most popular
     */
    public static function getTagNamesForObject($object, $numTags = 100) {
        return array_keys(self::orderTagNames(self::getTagsForObject($object, $numTags)));
    }

    /**
     * Returns the given number of tag names
     * for the given content object and tag contributor
     *
     * @param $id string  ID of the object
     * @param $screenName string  Username of the person
     * @return array  The tag names
     */
    public static function getTagNamesForObjectAndUser($object, $screenName, $numTags = 100) {
        $id = is_object($object) ? $object->id : $object;
        if (! $id || ! $screenName) { return array(); }
        return array_keys(self::orderTagNames(XN_Query::create('Tag_ValueCount')
                       ->filter('ownerName', 'eic', $screenName)
                       ->filter('contentId', '=', $id)
                       ->end($numTags)
                       ->execute()));
    }

    /**
     * Retrieves the tags assigned by a person to an object.
     *
     * @param $object XN_Content|W_Content|string  The object, or its ID
     * @param $screenName string  Username of the person
     * @return string  Comma-delimited list of tag names
     */
    public static function getTagStringForObjectAndUser($object, $screenName) {
        $id = is_object($object) ? $object->id : $object;
        return self::implode(XN_Tag::tagNamesFromTags(self::getTagsForObjectAndUser($id, $screenName)));
    }

    /**
     * Return the given number of Tag objects for the given content object, most popular first.
     *
	 * @param $object  W_Content|XN_Content|string 	the content object, or its ID
     * @param $numTags int							Maximum number of tags to return
     * @return array  tag name => popularity
     */
    public static function getTagsForObject($object, $numTags = 100) {
        $id = is_object($object) ? $object->id : $object;
        if (! $id) { return array(); }
        return XN_Query::create('Tag_ValueCount')
                       ->filter('contentId', '=', $id)
                       ->end($numTags)
                       ->execute();
    }

    /**
     * Return the given number of Tag objects for the given user, most popular first.
     *
     * @param   $screenName   string  Username of the person
     * @param   $numTags      int     Maximum number of tags to return
     * @return                array   tag name => popularity
     */
    public static function getTagsForUser($screenName, $numTags = 100) {
        return XN_Query::create('Tag_ValueCount')
                       ->filter('ownerName', 'eic', $screenName)
                       ->end($numTags)
                       ->execute();
    }

    /**
     * Retrieves tags assigned by a person to an object.
     *
     * @param $id string  ID of the object
     * @param $screenName string  Username of the person
     * @return array  The Tag objects
     */
    public static function getTagsForObjectAndUser($id, $screenName) {
        if (! $id) { return array(); }
        return XN_Query::create('Tag')
                       ->filter('ownerName', 'eic', $screenName)
                       ->filter('contentId', '=', $id)
                       ->execute();
    }

    /**
     * Return the tag names as a comma-delimited string, with double-quotes
     * for tag names with commas, spaces, or semicolons.
     *
     * @param $tagNames array  The tag names
     * @return string  A comma-delimited string of tag names
     * @see XN_Tag::parseTagString for the converse operation
     */
    public static function implode($tagNames) {
        $result = '';
        if ($tagNames) {
            foreach ($tagNames as $tagName) {
                if ($result) { $result = $result . ', '; }
                $result = $result . (preg_match('/[, ;]/u', $tagName) ? '"'.$tagName.'"' : $tagName);
            }
        }
        return $result;
    }

    /**
     * Updates object tags, sets the "topTags" attribute and saves an object.
     * Only changed tags are updated.
     *
     * @param   $object     W_Content|XN_Content    Source object.
     * @param   $tagString  string                  Tag string. Will be trimmed to the allowed length.
     * @return  void
     */
    public static function updateTagsAndSave($object, $tagString) {
    	$tagString = mb_substr($tagString, 0, XG_TagHelper::MAX_TAGS_LENGTH);
    	$numTopTags = 5;
    	$isLoggedIn = XN_Profile::current()->isLoggedIn();
		$existingTags = $object->id ? XN_Tag::tagNamesFromTags( self::getTagsForObjectAndUser($object->id, XN_Profile::current()->screenName) ) : array();
		$newTags = array_unique( XN_Tag::parseTagString($tagString) );
		$tagsToDelete = array_diff($existingTags, $newTags);
		$tagsToAdd = array_diff($newTags, $existingTags);

		if ($object->id && $isLoggedIn && $tagsToDelete) {
			try {
        		XN_Tag::deleteTags($object, self::implode($tagsToDelete) );
			} catch(Exception $e) {
				// Don't stop just because of a taqging error  [Jon Aquino 2006-07-03] [Andrey 2008-05-07]
			}
		}

		// For the optimization purposes we assume that if no tags were added/deleted, we don't have to update the topTags attr [Andrey 2008-05-06]
		if ($tagsToDelete || $tagsToAdd) {
			// Add the new tags to the current top tags
			$topTags = self::getTagsForObject($object, $numTopTags*2);
			foreach ($tagsToAdd as $tag) {
				$topTags[$tag]++;
			}
			arsort($topTags, SORT_NUMERIC);
			$object->my->topTags = self::implode( array_slice( array_keys($topTags), 0, $numTopTags ) );
		}

        $object->save();

		if ($isLoggedIn && $tagsToAdd) {
			try {
	    	    XN_Tag::addTags($object, self::implode($tagsToAdd));
			} catch(Exception $e) {
				// Don't stop just because of a taqging error  [Jon Aquino 2006-07-03] [Andrey 2008-05-07]
			}
		}
	}
}

/**
 * Class that wraps arrays of tags for sorting
 */
class XG_TagSortWrapper {
    private $_tags;
    public function XG_TagSortWrapper($tags) {
        $this->_tags = $tags;
    }
    // sort by tag count, then by tag name
    public function tagSortCmp($a, $b) {
        // descending order by tag count
        if ($this->_tags[$a] > $this->_tags[$b]) {
            return -1;
        } else if ($this->_tags[$a] < $this->_tags[$b]) {
            return 1;
        }
        // ascending order by tag name
        return strcmp($a, $b);
    }
}
