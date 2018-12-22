<?php

/**
 * Content object containing the binary data for the group's icon.
 */
class GroupIcon extends W_Model {

    /**
     * System attribute marking whether to hide the content from the pivot and search results.
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

/** xn-ignore-start e40ef5265dd4213fdcbfeb0735e1b8b0 **/
// Everything other than instance variables goes below here

    /**
     * Creates a new GroupIcon
     *
     * @param $postVariableName string  The name of the POST variable storing the image data
     * @return GroupIcon  The saved GroupIcon
     */
    public static function create($postVariableName) {
        $groupIcon = W_Content::create('GroupIcon');
        $groupIcon->my->mozzle = 'groups';
        $groupIcon->isPrivate = XG_App::appIsPrivate();
        $groupIcon->set('data', $_POST[$postVariableName], XN_Attribute::UPLOADEDFILE);
        $groupIcon->save();
        return $groupIcon;
    }

/** xn-ignore-end e40ef5265dd4213fdcbfeb0735e1b8b0 **/

}
