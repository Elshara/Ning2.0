<?php

XG_App::includeFileOnce('/lib/XG_SequencedjobController.php');

/**
 * Dispatches requests for sequenced jobs. A sequenced job is an XN_Job that
 * will create a new XN_Job if it cannot complete its work. This results in a
 * sequence (or "chain") of jobs.
 *
 * @see XG_SequencedjobController
 */
class Groups_SequencedjobController extends XG_SequencedjobController {

    /**
     * A sequenced job that updates groups with null activity scores, setting them to 0.
     * Also sets lastActivityOn to the Group's updatedDate.
     */
    public function action_initializeActivityAttributes() {
        $this->initializeActivityAttributes(XN_Query::create('Content'));
    }

    /**
     * A sequenced job that updates groups with null activity scores, setting them to 0.
     * Also sets lastActivityOn to the Group's updatedDate.
     */
    protected function initializeActivityAttributes($query) {
        $query->filter('owner');
        $query->filter('type', '=', 'Group');
        $query->filter('my.activityScore', '=', null);
        $query->end(20);
        $groups = $query->execute();
        foreach ($groups as $group) {
            $group->my->activityScore = 0;
            $group->my->set('lastActivityOn', $group->updatedDate, XN_Attribute::DATE);
            $group->save();
        }
        $this->setContinueJob(count($groups) > 0);
    }

    /**
     * A sequenced job that searches for all private groups and spins off a new job
     * to check for wrongly public comments for that group
     */
    public function action_privatizeAllWronglyPublicComments() {
        $this->privatizeAllWronglyPublicComments(XN_Query::create('Content'));
    }

    /**
     * A sequenced job that searches for all private groups and spins off a new job
     * to check for wrongly public comments for that group
     */
    protected function privatizeAllWronglyPublicComments($query) {
        $query->filter('owner')
            ->filter('type', '=', 'Group')
            ->filter('my.groupPrivacy', 'eic', 'private')
            ->end(20);
        $groups = $query->execute();
        foreach ($groups as $group) {
            error_log('* creating async job');
            XG_JobHelper::start('groups', 'privatizeWronglyPublicComments', array('groupId' => $group->id));
        }
        $this->setContinueJob(count($categories) > 0);
    }

    /**
     * A sequenced job that searches for all private group comments that are public
     * and mark them private.
     */
    public function action_privatizeWronglyPublicComments() {
        $groupId = $_POST['groupId'];
        $this->privatizeWronglyPublicComments(XN_Query::create('Content'), $groupId);
    }

    /**
     * A sequenced job that searches for all private group comments that are public
     * and mark them private.
     */
    protected function privatizeWronglyPublicComments($query, $groupId) {
       
        // safety check
        $groupQuery = XN_Query::create('Content')
            ->filter('owner')
            ->filter('type', '=', 'Group')
            ->filter('id', '=', $groupId)
            ->filter('my.groupPrivacy', 'eic', 'private');
        $group = $groupQuery->execute();
        if (count($group) == 0) {
            $this->setContinueJob(false);
            return;
        }

        $query->filter('owner')
            ->filter('type', '=', 'Comment')
            ->filter('my.attachedTo', 'eic', $groupId)
            ->filter('my.excludeFromPublicSearch', 'neic', 'Y')
            ->end(20);
        $comments = $query->execute();
        foreach ($comments as $comment) {
            $comment->my->excludeFromPublicSearch = 'Y';
            $comment->save();
        }
        $this->setContinueJob(count($comments) > 0);
    }

}
