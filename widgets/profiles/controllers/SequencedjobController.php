<?php

XG_App::includeFileOnce('/lib/XG_SequencedjobController.php');

/**
 * Dispatches requests for sequenced jobs. A sequenced job is an XN_Job that
 * will create a new XN_Job if it cannot complete its work. This results in a
 * sequence (or "chain") of jobs.
 *
 * @see XG_SequencedjobController
 */
class Profiles_SequencedjobController extends XG_SequencedjobController {

    /**
     * A sequenced job that builds the BlogArchive.
     */
    public function action_buildBlogArchive() {
        BlogArchive::build($this->start, $this->start += 50);
        $this->setContinueJob(BlogArchive::instance()->my->buildStatus != BlogArchive::COMPLETE);
    }

}
