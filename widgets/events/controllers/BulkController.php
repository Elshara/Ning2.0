<?php
/**
 * Approves or deletes large numbers of content objects, in chunks.
 *
 * @see "Bazel Code Structure: Bulk Operations"
 */
class Events_BulkController extends W_Controller {

    /**
     * Constructor.
     *
     * @param   $widget     W_BaseWidget    The Events widget
     */
    public function __construct(W_BaseWidget $widget) {
        parent::__construct($widget);
        EventWidget::init();
    }

    /**
     * Removes Event and EventAttendee objects created by the specified user.
     *
     * @param $limit integer  Maximum number of content objects to remove (approximate).
     * @param $screenName string  Username of the person whose content to remove.
     * @return array  'changed' => the number of content objects deleted,
     *     'remaining' => 1 or 0 depending on whether or not there are content objects remaining to delete
     */
    public function action_removeByUser($limit = null, $screenName = null) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (1070134197)'); }
        if (! XG_SecurityHelper::currentUserCanDeleteUser($screenName)) { xg_echo_and_throw('Not allowed (554055589)'); }
        $this->_widget->includeFileOnce('/lib/helpers/Events_BulkHelper.php');
        $result = Events_BulkHelper::removeEventAttendees($limit, $screenName);
        if ($result['remaining']) { return $result; }
        return Events_BulkHelper::removeEvents($limit, $screenName);
    }

}
