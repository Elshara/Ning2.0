<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Message.php');
EventWidget::init();

class XG_MessageTest extends UnitTestCase {

    public function tearDown() {
        XG_Message::$storeInsteadOfSend = false;
        W_Cache::getWidget('main')->privateConfig['sendHtmlMessages'] = 'Y';
    }

    public function testRequestInvitation() {
        if (! $_GET['request-invitation']) { return; }
        $body = "hi!\n\nI would like an invitation.\n\nThanks.";
        $msg = XG_Message_Request_Invitation::create(array('body' => $body));
        $msg->send(XN_Profile::load('david'));
        $msg->send(array('Test User','test-user@ninginc.com'));
        $this->pass('Invitation Request Sent');
    }

    public function testEventActivity() {
        if (! $_GET['activity']) { return; }
        $opts = array('viewActivity' => 'To view the menu, click here:',
                      'activity' => 'There is a new chowder on your menu.',
                      'reason' => 'someone ran the messaging test',
                      'content' => XN_Query::create('Content')->filter('owner')->end(1)->uniqueResult());
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_ACTIVITY, $opts);
        $msg->send('ningdev');
        $this->pass('Activity Notification Sent');
    }

    public function testEventModerationNew() {
        if (! $_GET['moderation-new']) { return; }
        $opts = array('moderationUrl' => 'http://' . $_SERVER['HTTP_HOST'],
                      'reason' => 'someone ran the messaging test',
                      'content' => XN_Query::create('Content')->filter('owner')->end(1)->uniqueResult());
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_NEW, $opts);
        $msg->send('ningdev');
        $this->pass('New Moderation Notification Sent');
    }

    public function testEventModerationDecision() {
        if (! $_GET['moderation-decision']) { return; }
        $opts = array('reason' => 'someone ran the messaging test',
                      'content' => XN_Query::create('Content')->filter('owner')->end(1)->uniqueResult());
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_DECISION, $opts);
        $msg->send('ningdev');
        $this->pass('Moderation Decision Notification Sent');
    }

    public function testEventJoin() {
        if (! $_GET['join']) { return; }
        $opts = array('joiner' => 'NingDev');
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_JOIN, $opts);
        $msg->send('ningdev');
        $this->pass('Join Notification Sent');
    }

    public function testEventUserMessage() {
        if (! $_GET['user-message']) { return; }
        $opts = array('profile' => 'NingDev');
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_USER_MESSAGE, $opts);
        $msg->send('ningdev');
        $this->pass('User Message Notification Sent');
    }

    public function testEventFriendRequest() {
        if (! $_GET['friend-request']) { return; }
        $opts = array('profile' => 'NingDev');
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_FRIEND_REQUEST, $opts);
        $msg->send('ningdev');
        $this->pass('Friend Request Notification Sent');
    }

    public function testEventFriendAccepted() {
        if (! $_GET['friend-accepted']) { return; }
        $opts = array('profile' => 'NingDev');
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_FRIEND_ACCEPTED, $opts);
        $msg->send('ningdev');
        $this->pass('Friend Accepted Notification Sent');
    }

    public function testEventWelcome() {
        if (! $_GET['welcome']) { return; }
        $opts = array('profile' => 'NingDev');
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_WELCOME, $opts);
        $msg->send('ningdev');
        $this->pass('Welcome Notification Sent');
    }

    public function testEventModerateMember() {
        if (! $_GET['moderate-member']) { return; }
        $opts = array('joiner' => 'NingDev');
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_MODERATION_MEMBER, $opts);
        $msg->send('ningdev');
        $this->pass('Member Moderation Notification Sent');
    }

    public function testAcceptedMember() {
        if (! $_GET['accepted']) { return; }
        $opts = array('profile' => 'NingDev');
        $msg = XG_Message_Notification::create(XG_Message_Notification::EVENT_PENDING_ACCEPTED, $opts);
        $msg->send('ningdev');
        $this->pass('Accepted Pending Member Notification Sent');
    }

    public function testInvitation() {
        if (! $_GET['invitation']) { return; }
        $opts = array('subject' => 'Sample Invitation Title',
                      'body' => 'Sample Invitation Body',
                      'url' => 'http://' . $_SERVER['HTTP_HOST']);
        $msg = new XG_Message_Invitation($opts);
        $msg->send('ningdev','ningdev',false);
    }

    public function testGroupInvitation() {
        if (! $_GET['group-invitation']) { return; }
        $groups = XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Group')->execute();
        if (! $groups) {
            $this->skip('No groups available');
            return;
        }
        $opts = array('subject' => 'Sample Group Invitation Title',
                      'body' => 'Sample Group Invitation Body',
                      'url' => 'http://' . $_SERVER['HTTP_HOST']);
        $msg = new XG_Message_Group_Invitation($opts);
        $msg->send('ningdev','ningdev',$groups[0]);
    }

    public function testSiteReturnAddress() {
        require_once 'Mail/RFC822.php';
        $host = $_SERVER['HTTP_HOST'];
        $appName = XN_Application::load()->name;
        $currentHost = preg_replace('@^www\.@u', '', $host);
        $defaultAddress = imap_rfc822_write_address('noreply',$currentHost, $appName . " " . xg_text('NOTIFICATIONS'));
        $this->assertEqual(XG_Message::siteReturnAddress(), $defaultAddress);
        $parsed = Mail_RFC822::parseAddressList($defaultAddress);
        if (is_array($parsed)) {
            $this->assertEqual(count($parsed), 1);
            $this->assertEqual($parsed[0]->personal,
                    "$appName " . xg_text('NOTIFICATIONS'));
            $this->assertEqual($parsed[0]->mailbox,'noreply');
            $this->assertEqual($parsed[0]->host, $currentHost);
        }

        // Mapped Domain
        $_SERVER['HTTP_HOST'] = 'www.artichoke.com';
        $address = 'vegetable <noreply@artichoke.com>';
        $this->assertEqual(XG_Message::siteReturnAddress('vegetable'), $address);
        $parsed = Mail_RFC822::parseAddressList($address);
        if (is_array($parsed)) {
            $this->assertEqual(count($parsed), 1);
            $this->assertEqual($parsed[0]->personal, 'vegetable');
            $this->assertEqual($parsed[0]->mailbox,'noreply');
            $this->assertEqual($parsed[0]->host, 'artichoke.com');
        }

        // Special chars in name
        $_SERVER['HTTP_HOST'] = 'www.artichoke.com';
        $name = '"my site" is\\cool';
        $address = '"\\"my site\\" is\\\\cool" <noreply@artichoke.com>';
        $this->assertEqual(XG_Message::siteReturnAddress($name), $address);
        $parsed = Mail_RFC822::parseAddressList($address);
        if (is_array($parsed)) {
            $this->assertEqual(count($parsed), 1);
            $this->assertEqual($parsed[0]->personal, '"\\"my site\\" is\\\\cool"');
            $this->assertEqual($parsed[0]->mailbox,'noreply');
            $this->assertEqual($parsed[0]->host, 'artichoke.com');
        }

        // Put things back as they were
        $_SERVER['HTTP_HOST'] = $host;
    }

    public function testMissingHtmlEmailTemplates() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib/XG_Message', '*_text.php') as $filename) {
            $this->assertTrue(file_exists(str_replace('_text', '', $filename)), basename($filename));
        }
    }

    public function testMissingTextEmailTemplates() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib/XG_Message', '*.php') as $filename) {
            if (strpos($filename, '_text') !== false) { continue; }
            if (basename($filename) == '_header.php') { continue; }
            if (basename($filename) == '_footer.php') { continue; }
            $this->assertTrue(file_exists(str_replace('.php', '_text.php', $filename)), basename($filename));
        }
    }

    public function testXgMessageUsedBeforeIncluded() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $filename) {
            $contents = file_get_contents($filename);
            $contents = str_replace('@see XG_Message', '', $contents);
            $i = strpos($contents, 'XG_Message.php');
            $j = strpos($contents, 'XG_Message::');
            if ($i !== false && $j !== false) {
                $this->assertTrue($i <= $j, $i . ' vs. ' . $j . ' ' . $filename);
            }
        }
    }

    public function testHtmlEmailsNotHtml() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib/XG_Message', '*.php') as $filename) {
            if (strpos($filename, '_text') !== false) { continue; }
            if (basename($filename) == '_header.php') { continue; }
            if (basename($filename) == '_footer.php') { continue; }
            $this->assertTrue(strpos(file_get_contents($filename), '<div') !== false, basename($filename));
        }
    }

    /**
     * @todo According to Jon A this test is no longer necessary - investigate removing it
     *       prior to adding very many more exceptions
     */
    public function testReplaceScreenNameWithEmail() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib/XG_Message', '*.php') as $filename) {
            if (basename($filename) == 'user-message.php') { continue; }
            if (basename($filename) == 'user-message_text.php') { continue; }
            if (basename($filename) == 'osapp-notification.php') { continue; }
            if (basename($filename) == 'osapp-notification_text.php') { continue; }
            if (basename($filename) == '_header.php') { continue; }
            if (basename($filename) == '_footer.php') { continue; }
            $contents = file_get_contents($filename);
            $this->assertTrue(strpos($contents, "'to'") === false, basename($filename));
        }
    }

    public function testFormatEmailAddress() {
        $profile = new TestProfile();
        $profile->fullName = null;
        $profile->screenName = 'NingDev';
        $profile->email = 'NingDev@example.org';
        $this->assertEqual('NingDev@users', TestMessage::formatEmailAddressProper('NingDev', $profile));
        $profile->fullName = null;
        $profile->screenName = 'NingDev';
        $profile->email = '';
        $this->assertEqual('NingDev@users', TestMessage::formatEmailAddressProper('NingDev', $profile));
        $profile->fullName = 'John Smith';
        $profile->screenName = 'NingDev';
        $profile->email = 'NingDev@example.org';
        $this->assertEqual('John Smith <NingDev@users>', TestMessage::formatEmailAddressProper('NingDev', $profile));
        $profile->fullName = 'John "J" Smith';
        $profile->screenName = 'NingDev';
        $profile->email = 'NingDev@example.org';
        $this->assertEqual('"John \"J\" Smith" <NingDev@users>', TestMessage::formatEmailAddressProper('NingDev', $profile));
        $profile->fullName = 'John < Smith';
        $profile->screenName = 'NingDev';
        $profile->email = 'NingDev@example.org';
        $this->assertEqual('"John < Smith" <NingDev@users>', TestMessage::formatEmailAddressProper('NingDev', $profile));
        $profile->fullName = 'NingDev';
        $profile->screenName = 'NingDev';
        $profile->email = 'NingDev@example.org';
        $this->assertEqual('NingDev@users', TestMessage::formatEmailAddressProper('NingDev', $profile));
        // Chinese
        $profile->fullName = '你好';
        $profile->screenName = 'NingDev';
        $profile->email = 'NingDev@example.org';
        $this->assertEqual('=?UTF-7?Q?+T=32BZfQ-?= <NingDev@users>', TestMessage::formatEmailAddressProper('NingDev', $profile));
        $this->assertEqual(XN_Profile::current()->screenName . '@users', TestMessage::formatEmailAddressProper(XN_Profile::current()->screenName, null));
        $this->assertEqual(XN_Profile::current()->email, TestMessage::formatEmailAddressProper(XN_Profile::current()->email, null));
        $this->assertEqual('blah123456@users', TestMessage::formatEmailAddressProper('blah123456', null));
        $this->assertEqual('blah123456@example.org', TestMessage::formatEmailAddressProper('blah123456@example.org', null));
    }

    public function testNoExceptionsOrFatalErrors() {
        W_Cache::getWidget('admin')->includeFileOnce('/lib/helpers/Admin_MessageHelper.php');
        ob_start();
            $opts = array();
            foreach (Admin_MessageHelper::getAllTypes() as $type) {
                foreach (array('text', 'html', 'combined') as $format) {
                    $opts['format'] = $format;
                    foreach (array('sparse' => true, 'non_sparse' => false) as $sparse_type=>$sparse_value) {
                        $opts['sparse'] = $sparse_value;
                        foreach (array('custom_msg' => true, 'no_custom_msg' => false) as $custom_msg_n=>$custom_msg_v) {
                            $opts['custom_msg'] = $custom_msg_v;
                            Admin_MessageHelper::sendMessage($type, 'display', $opts);
                        }
                    }
                }
            }
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertNoPattern('@error|exception@ui', $output);
    }

}

class TestMessage extends XG_Message {
    public static function formatEmailAddressProper($screenNameOrEmail, $profile) {
        return parent::formatEmailAddressProper($screenNameOrEmail, $profile);
    }
}

class TestProfile {
    var $fullName;
    var $screenName;
    var $email;
}

XG_App::includeFileOnce('/test/test_footer.php');
