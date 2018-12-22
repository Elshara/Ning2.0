<?php
/**
 * Pre-creates new shapes and new attributes. Generated by diffShapes.php
 * The purpose of pre-creating shapes and attributes on the top few thousand networks
 * is to prevent a spike in DDL activity immediately after a release.
 * Infrequently created shapes and attributes do not need to be pre-created.
 */

$shape = XN_Shape::load('User');
if ($shape) {
    $shape->setAttribute('my.internalFlags', array('type' => 'string'));
    $shape->setAttribute('my.xg_groups_groupCount', array('type' => 'number'));
    $shape->setAttribute('my.xg_photo_albumCount', array('type' => 'number'));
    $shape->save();
}

echo 'Done';
