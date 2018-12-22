<?php

/**
 * Useful functions for working with privacy.
 */
class XG_PrivacyHelper {

    /** These types have special rules and should be excluded from a general move from private to public. */
    public static $exclude = array('Comment', 'Invitation', 'TopicCommenterLink', 'PageCommenterLink', 'User');

    /**
     * Generates a basic privacy switching query with no type.  Add a type filter
     * to this to get a set of objects to change privacy on.
     *
     * Note that the query returned does not have a type filter yet. The caller is
     * responsible for adding a type filter; otherwise the query will be very expensive
     * (joining all tables).
     *
     * @param   $limit     integer      Maximum number of results from query.
     * @param   $toPrivate     boolean  true if switching to private, false if switching to public.
     * @param   $mozzle      string     Name of mozzle to limit query to.
     * @return  XN_Query                Query object ready for customization to a specific type's rules.
     */
    public static function basicQuery($limit, $toPrivate, $mozzle) {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->begin(0);
        $query->end($limit);
        $query->filter('isPrivate', '=' ,(! $toPrivate));
        $query->filter('my->mozzle', 'eic', $mozzle);
        // All group-related items have their own privacy settings.  We don't deal with them here.
        $query->filter('groupId', '=', null);
        return $query;
    }

    /**
     * Set the privacy of up to $limit objects belonging to the $mozzle mozzle,
     * excluding those with types found in $excludedTypes.  Return the number of objects changed.
     *
     * @param   $limit integer          Maximum number of objects to change.
     * @param   $toPrivate boolean      Value to set isPrivate attribute to.
     * @param   $mozzle  string         Name of mozzle to limit changes to.
     * @param   $excludedTypes   array  array of strings of type names to exclude from this process.
     * @return  integer                 Number of objects changed.
     */
    public static function setContentPrivacy($limit, $toPrivate, $mozzle, $excludedTypes=array()) {
        $allTypes = self::getAllTypes($mozzle);
        if ($toPrivate) {
            $standardTypes = $allTypes;
        } else {
            $standardTypes = array_diff($allTypes, $excludedTypes);
            $standardTypes = array_diff($standardTypes, self::$exclude);
        }
        return self::setPrivacyByTypeAndMozzle($limit, $toPrivate, $mozzle, $standardTypes);
    }

    /**
     * Gets an array of all the types found in the network with one or more objects.
     *
     * @param   $mozzle  string Name of the mozzle to get types for.
     * @return  array           Names of types found in the mozzle.
     */
    private static function getAllTypes() {
        $query = XN_Query::create('Content_Count');
        $query->filter('owner');
        $query->rollup('type');
        $types = array();
        foreach ($query->execute() as $type => $number) {
            if ($number > 0) { $types[] = $type; }
        }
        return $types;
    }

    /**
     * Set the isPrivate attribute of a maximum of $limit objects of types
     * in $types to the value of the $toPrivate parameter within the $mozzle mozzle.
     *
     * @param $limit   integer      Maximum number of objects to change.
     * @param $toPrivate   boolean  Value to set isPrivate attribute to.
     * @param $mozzle    string     Apply only to objects owned by this mozzle.
     * @param $types     array      Array of strings of types of objects to change.
     */
    private static function setPrivacyByTypeAndMozzle($limit, $toPrivate, $mozzle, $types) {
        $changed = 0;

        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->begin(0);
        $query->end($limit);
        $query->filter('isPrivate', '=', (! $toPrivate));
        $query->filter('my->mozzle', 'eic', $mozzle);
        $filters = array();
        foreach ($types as $type) {
            $filters[] = XN_Filter('type', 'eic', $type);
        }
        $query->filter(call_user_func_array(array('XN_Filter','any'), $filters));

        $objects = $query->execute();
        foreach ($objects as $object) {
            $object->isPrivate = $toPrivate;
            $object->save();
            $changed++;
        }
        return $changed;
    }

    /**
     * Set up to $limit objects of the specified type to the specified privacy setting.
     *
     * @param   $limit integer          Maximum number of items to switch privacy level before returning.
     * @param   $toPrivate boolean      true if switching to private, false if switching to public.
     * @param   $mozzle  string         Name of mozzle to perform this operation in.
     * @param   $type  string           Type of object to change.
     * @param   $attr  string           Attribute to check for specified value before changing.
     * @param   $value     var          Value attribute must match to be changed.
     * @return  array                   IDs of the objects switched (so sub-objects can be dealt with).
     */
    public static function setPrivacyAndGetIds($limit, $toPrivate, $mozzle, $type, $attr, $value) {
        XG_App::includeFileOnce('/lib/XG_PrivacyHelper.php');
        $query = XG_PrivacyHelper::basicQuery($limit, $toPrivate, $mozzle);
        $query->filter('type', 'eic', $type);
        $query->filter($attr, '=', $value);
        $objects = $query->execute();

        $changedObjects = array();
        foreach ($objects as $object) {
            $object->isPrivate = $toPrivate;
            $object->save();
            $changedObjects[] = $object->id;
        }
        return $changedObjects;
    }

    /**
     * Set privacy settings of all subobjects of the specified types which hold references to the specified master
     * objects.
     *
     * @param   $toPrivate boolean      true if switching to private, false if switching to public.
     * @param   $mozzle  string         Name of mozzle to restrict changes to.
     * @param   $types   array          Types to set the privacy of.
     * @param   $attr  string           Attribute to check for the IDs of the master objects.
     * @param   $changedObjects   array Array of IDs of master objects that have been changed.
     * @return  array                   Number of subobjects changed.
     */
    public static function setRelatedObjectsPrivacy($toPrivate, $mozzle, $types, $attr, $changedObjects) {
        XG_App::includeFileOnce('/lib/XG_PrivacyHelper.php');
        if (count($changedObjects) === 0) { return 0; }

        // Cannot use XG_PrivacyHelper::basicQuery here because we want to do them all at once not limit to a certain number.
        $query = XN_Query::create('Content')->filter('owner');
        $query->filter('isPrivate', '=', (! $toPrivate));
        $query->filter('my->mozzle', 'eic', $mozzle);
        foreach ($types as $type) {
            $filters[] = XN_Filter('type', 'eic', $type);
        }
        $query->filter(call_user_func_array(array('XN_Filter','any'), $filters));

        $filters = array();
        foreach ($changedObjects as $changedObjectID) {
            $filters[] = XN_Filter($attr, '=', $changedObjectID);
            // Try again with a string ID since on Bullwinkle, the attribute types seem to vary
            $filters[] = XN_Filter($attr, '=', (string) $changedObjectID);
        }
        $query->filter(call_user_func_array(array('XN_Filter','any'), $filters));

        $changed = 0;
        foreach ($query->execute() as $object) {
            $object->isPrivate = $toPrivate;
            $object->save();
            $changed++;
        }
        return $changed;
    }
}
