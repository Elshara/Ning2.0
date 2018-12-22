<?php
class Profiles_MembersController extends XG_BrowserAwareController {
    //
    public function action_index() { # void
		$this->forwardTo('list','friend');
    }
    public function action_index_iphone() { # void
		$this->forwardTo('list','friend', array('nav' => 'members'));
    }
}
?>
