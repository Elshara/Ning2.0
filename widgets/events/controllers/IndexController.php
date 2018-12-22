<?php
XG_App::includeFileOnce('/lib/XG_Layout.php');
/**
* Primary dispatcher for the Events widget.
*/
class Events_IndexController extends W_Controller {
	protected $ok;
    /**
     * Displays the Events main page
     *
     * Expected GET variables:
     *     page - page number (optional)
     */
    public function action_index() {
        $this->forwardTo('listUpcoming','event');
    }
    /**
	 *  /xn/detail handler
     */
	public function action_detail($object) { # void
		$this->forwardTo('show','event',array($object));
    }

    public function action_asyncJob() { # void
		// Backward compatibility. Can be removed after 3.3 release
		W_Cache::getWidget('main')->dispatch('index','asyncJob');
    }
}
?>