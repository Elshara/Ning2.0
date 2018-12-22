<?php

XG_App::includeFileOnce('/lib/XG_SequencedjobController.php');

/**
 * Dispatches requests for sequenced jobs. A sequenced job is an XN_Job that
 * will create a new XN_Job if it cannot complete its work. This results in a
 * sequence (or "chain") of jobs.
 *
 * @see XG_SequencedjobController
 */
class Forum_SequencedjobController extends XG_SequencedjobController {

    /**
     * A sequenced job that updates groups with null activity scores, setting them to 0.
     * Also sets lastActivityOn to the Group's updatedDate.
     */
    public function action_initializeCategoryCountAndActivity() {
        $this->initializeCategoryCountAndActivity(XN_Query::create('Content'));
    }

    /**
     * A sequenced job that updates forum categories' activity count and latest activity
     */
    protected function initializeCategoryCountAndActivity($query) {
        $query->filter('owner');
        $query->filter('type', '=', 'Category');
        $query->filter('my.discussionCount', '=', null);
        $query->end(20);
        $categories = $query->execute();
        foreach ($categories as $category) {
            Category::updateDiscussionCountAndActivity($category, null, true);
        }
        $this->setContinueJob(count($categories) > 0);
    }

}