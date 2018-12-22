<?php

XG_App::includeFileOnce('/lib/XG_SequencedjobController.php');

/**
 * Dispatches requests for sequenced jobs. A sequenced job is an XN_Job that
 * will create a new XN_Job if it cannot complete its work. This results in a
 * sequence (or "chain") of jobs.
 *
 * @see XG_SequencedjobController
 */
class Photo_SequencedjobController extends XG_SequencedjobController {

    /**
     * A sequenced job that updates all albums to have a cover photo.
     */
    public function action_addAlbumCovers() {
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_ContentHelper.php');
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_PhotoHelper.php');
        $this->addAlbumCovers(XN_Query::create('Content'), new Photo_ContentHelper(), new Photo_PhotoHelper());
    }

    /**
     * A sequenced job that updates all albums to have a cover photo.
     */
    protected function addAlbumCovers($query, $contentHelper, $photoHelper) {
        $query->filter('owner');
        $query->filter('type', '=', 'Album');
        $query->begin($this->start ? $this->start : 0);
        $query->end($this->start += 20);
        $albums = $query->execute();
        foreach ($albums as $album) {
            if ($album->my->coverPhotoId) { continue; }
            $photoIds = $contentHelper->ids($album, 'photos');
            $coverPhotoCandidates = $photoHelper->getSpecificPhotosProper(XN_Query::create('Content')->filter('my.approved', '=', 'Y')->filter('my.visibility', '=', 'all'), $photoIds, null, 0, 1);
            if ($coverPhotoCandidates['photos']) {
                $album->my->coverPhotoId = reset($coverPhotoCandidates['photos'])->id;
                $album->save();
            }
        }
        $this->setContinueJob(count($albums) > 0);
    }

}
