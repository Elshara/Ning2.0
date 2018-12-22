<?php
/** $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  Email opt-out landing page
 *
 **/

class Index_OptoutController extends W_Controller {
    //
    protected function _init() { # void
        $this->code = $_GET['code'];
		$expireDays = 7;
        if (!$this->info = BlockedContactList::parseOptoutCode($this->code, $expireDays * 86400)) {
            $this->error = xg_html('SORRY_THIS_UNSUBSCRIBE_LINK', $expireDays);
            $this->render('message');
            return false;
        }
        if (BlockedContactList::isAlias($this->info['recipient'])) {
			$this->redirectTo(W_Cache::getWidget('profiles')->buildUrl('profile','emailSettings'));
			return false;
		}
        XG_Cache::profiles($this->info['sender'], $this->info['recipient']);

        $this->senderProfile = XG_Cache::profiles($this->info['sender']);
        $this->senderName = $this->senderProfile ? xg_username($this->senderProfile) : '';

        $this->recipientProfile = XG_Cache::profiles($this->info['recipient']);
        $this->recipientEmail = $this->info['recipient'];
        $this->recipientUser = $this->recipientProfile ? User::load($this->recipientProfile->screenName) : NULL;
        $this->recipientName = $this->recipientProfile ? xg_username($this->recipientProfile) : '';

        $this->recipientText = $this->recipientProfile->email
            ? $this->recipientProfile->email
            : ( preg_match('/@users$/u',$this->recipientEmail)
                ? $this->recipientName
                : $this->recipientEmail );
        return true;
    }

    //
    public function action_overridePrivacy () { # void
        return true;
    }

    /**
     *  Opt-out entry point
     *
     *  @param      $code	string	Out-out code
     */
    public function action_index() {
        if (!$this->_init()) { return; }
    }

    /**
     *  Block email sender
     *
     *  @param      $code	string	Out-out code
     */
    public function action_blockSender() {
        if (!$this->_init()) { return; }
        BlockedContactList::blockSender($this->info['recipient'], array($this->info['sender'], $this->senderProfile->email));
        $this->message = xg_html('YOU_HAVE_SUCCESSFULLY_BLOCKED', qh($this->senderName), qh($this->recipientText));
        $this->render('message');
    }

    /**
     *  Block all emails
     *
     *  @param      $code	string	Out-out code
     */
    public function action_blockAll() {
        if (!$this->_init()) { return; }
        BlockedContactList::blockAllEmails($this->info['recipient']);
        $this->message = xg_html('YOU_HAVE_SUCCESSFULLY_UNSUBSCRIBED', qh(XN_Application::load()->name), qh($this->recipientText));
        $this->render('message');
    }
}
?>
