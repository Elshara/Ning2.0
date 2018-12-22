<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_MessageHelper.php');

Mock::generate('TestRest');

class Profiles_MessageHelperTest extends UnitTestCase {
    private $_skip = false;

    public function setUp() {
        TestRest::setInstance(null);
        $this->messageHelper = new TestMessageHelper();
        Profiles_MessageHelper::invalidateInboxUnreadMessageCountCache(XN_Profile::current()->screenName);
        Profiles_MessageHelper::invalidateAlertsUnreadMessageCountCache(XN_Profile::current()->screenName);
        
        // psuedo skip() - not implemented in our current version of SimpleTest
        // TODO: remove this once upgraded to a more recent version of ST
        $this->_skip = !class_exists('XN_MessageFolder');
    }

    private function doTestMoveMessages($response) {
        $rest = new ExceptionMockDecorator(new MockTestRest());
        TestRest::setInstance($rest);
        $rest->expectAt(0, 'doRequest', array('GET', 'http://' . XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX . '/xn/rest/internal/messaging/?action=move&folder=Inbox&message_id%5B0%5D=8686&message_id%5B1%5D=8682&xn_out=xml', null, null, null));
        $rest->expectAt(1, 'doRequest', array('DELETE', 'http://' . XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX . '/xn/rest/1.0/cache(id=\'inbox-unread-message-count-' . XN_Profile::current()->screenName . '\')', null, null, null));
        $rest->setReturnValue('doRequest', $response);
        return Profiles_MessageHelper::moveMessages(array(8686, 8682), 'Inbox');
    }

    public function testMoveMessages1() {
        if ($this->_skip) {
            return;
        }
        try {
            $this->doTestMoveMessages("<?xml version='1.0' encoding='UTF-8'?><errors><element><folder>Invalid folder 'INBOX2'</folder></element></errors>");
            $this->fail();
        } catch (Exception $e) {
            $this->assertEqual("Invalid folder 'INBOX2'", $e->getMessage());
        }

    }

    public function testMoveMessages2() {
        if ($this->_skip) {
            return;
        }
        $this->doTestMoveMessages('<success>Message Moved!</success>');
    }

    public function testMoveMessages3() {
        if ($this->_skip) {
            return;
        }
        $rest = new ExceptionMockDecorator(new MockTestRest());
        TestRest::setInstance($rest);
        $rest->expectNever('doRequest', array('DELETE', 'http://' . XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX . '/xn/rest/1.0/cache(id=\'inbox-unread-message-count-'.XN_Profile::current()->screenName.'\')', null, null, null));
        Profiles_MessageHelper::moveMessages(array(), 'Inbox');
    }

    public function testFormatMessageForDisplay() {
        if ($this->_skip) {
            return;
        }
        $testMessage = "a
bc
def

> Date: " . date("r", time()+5) . "
> From: sender1
> To: r1, r2, r3
>
> 1:a
> 1:bc
> 1:def
>
> > Date: " . date("r") . "
> > From: sender2
> > To: r4, r5, r6
> >
> > 2:a
> > 2:bc
> > 2:def";

        $testCases = array(
                            array('startLevel' => 0,
                                  'minLevel' => 0,
                                  'maxLevel' => 0,
                                  'expect' => "a<br />\nbc<br />\ndef<br />\n<br />\n"),
                            array('startLevel' => 1,
                                  'minLevel' => 0,
                                  'maxLevel' => 0,
                                  'expect' => "<blockquote id=\"xj_quote_attach\">a<br />\nbc<br />\ndef<br />\n<br />\n</blockquote>"),
                            array('startLevel' => 2,
                                  'minLevel' => 0,
                                  'maxLevel' => 0,
                                  'expect' => "<blockquote><blockquote id=\"xj_quote_attach\">a<br />\nbc<br />\ndef<br />\n<br />\n</blockquote></blockquote>"),
                            array('startLevel' => 0,
                                  'minLevel' => 0,
                                  'maxLevel' => 1,
                                  'expect' => "a<br />\nbc<br />\ndef<br />\n<br />\n<blockquote id=\"xj_quote_attach\"><span class=\"xg_lightfont\">From</span> sender1 <span class=\"xg_lightfont\">to</span> r1, r2 <span class=\"xg_lightfont\">and</span> r3<br />\n<span class=\"xg_lightfont\">Sent just now</span><br />\n<br />\n1:a<br />\n1:bc<br />\n1:def<br />\n<br />\n</blockquote>"),
                            array('startLevel' => 1,
                                  'minLevel' => 0,
                                  'maxLevel' => 1,
                                  'expect' => "<blockquote>a<br />\nbc<br />\ndef<br />\n<br />\n<blockquote id=\"xj_quote_attach\"><span class=\"xg_lightfont\">From</span> sender1 <span class=\"xg_lightfont\">to</span> r1, r2 <span class=\"xg_lightfont\">and</span> r3<br />\n<span class=\"xg_lightfont\">Sent just now</span><br />\n<br />\n1:a<br />\n1:bc<br />\n1:def<br />\n<br />\n</blockquote></blockquote>"),
                            array('startLevel' => 0,
                                  'minLevel' => 1,
                                  'maxLevel' => 1,
                                  'expect' => "<blockquote id=\"xj_quote_attach\"><span class=\"xg_lightfont\">From</span> sender1 <span class=\"xg_lightfont\">to</span> r1, r2 <span class=\"xg_lightfont\">and</span> r3<br />\n<span class=\"xg_lightfont\">Sent just now</span><br />\n<br />\n1:a<br />\n1:bc<br />\n1:def<br />\n<br />\n</blockquote>"),
                            array('startLevel' => 0,
                                  'minLevel' => 0,
                                  'maxLevel' => 2,
                                  'expect' => "a<br />\nbc<br />\ndef<br />\n<br />\n<blockquote><span class=\"xg_lightfont\">From</span> sender1 <span class=\"xg_lightfont\">to</span> r1, r2 <span class=\"xg_lightfont\">and</span> r3<br />\n<span class=\"xg_lightfont\">Sent just now</span><br />\n<br />\n1:a<br />\n1:bc<br />\n1:def<br />\n<br />\n<blockquote id=\"xj_quote_attach\"><span class=\"xg_lightfont\">From</span> sender2 <span class=\"xg_lightfont\">to</span> r4, r5 <span class=\"xg_lightfont\">and</span> r6<br />\n<span class=\"xg_lightfont\">Sent just now</span><br />\n<br />\n2:a<br />\n2:bc<br />\n2:def<br />\n</blockquote></blockquote>"),
                            array('startLevel' => 0,
                                  'minLevel' => 0,
                                  'maxLevel' => null,
                                  'expect' => "a<br />\nbc<br />\ndef<br />\n<br />\n<blockquote><span class=\"xg_lightfont\">From</span> sender1 <span class=\"xg_lightfont\">to</span> r1, r2 <span class=\"xg_lightfont\">and</span> r3<br />\n<span class=\"xg_lightfont\">Sent just now</span><br />\n<br />\n1:a<br />\n1:bc<br />\n1:def<br />\n<br />\n<blockquote><span class=\"xg_lightfont\">From</span> sender2 <span class=\"xg_lightfont\">to</span> r4, r5 <span class=\"xg_lightfont\">and</span> r6<br />\n<span class=\"xg_lightfont\">Sent just now</span><br />\n<br />\n2:a<br />\n2:bc<br />\n2:def<br />\n</blockquote></blockquote>"),
                          );

        foreach ($testCases as $testCase) {
            $this->assertEqual(Profiles_MessageHelper::formatMessageForDisplay($testMessage, TRUE, $testCase['startLevel'], $testCase['minLevel'], $testCase['maxLevel']), $testCase['expect']);
        }
    }


    public function testInboxUnreadMessageCountCacheId() {
        $this->assertEqual('inbox-unread-message-count-foo', $this->messageHelper->inboxUnreadMessageCountCacheId('foo'));
    }

    public function testAlertsUnreadMessageCountCacheId() {
        $this->assertEqual('alerts-unread-message-count-foo', $this->messageHelper->alertsUnreadMessageCountCacheId('foo'));
    }

    public function testGetInboxUnreadMessageCount1() {
        if ($this->_skip) {
            return;
        }
        $this->doTestGetInboxUnreadMessageCount(TRUE);
        $this->doTestGetInboxUnreadMessageCount(FALSE);
        Profiles_MessageHelper::invalidateInboxUnreadMessageCountCache(XN_Profile::current()->screenName);
        $this->doTestGetInboxUnreadMessageCount(TRUE);
    }

    public function testGetInboxUnreadMessageCount2() {
        if ($this->_skip) {
            return;
        }
        XN_Cache::put(TestMessageHelper::inboxUnreadMessageCountCacheId(XN_Profile::current()->screenName), serialize(array('expires' => time() - 5, 'payload' => 123)));
        $this->doTestGetInboxUnreadMessageCount(TRUE);
    }

    public function testGetInboxUnreadMessageCount3() {
        if ($this->_skip) {
            return;
        }
        XN_Cache::put(TestMessageHelper::inboxUnreadMessageCountCacheId(XN_Profile::current()->screenName), serialize(array('expires' => time() + 5, 'payload' => 123)));
        $this->doTestGetInboxUnreadMessageCount(FALSE, 123);
    }

    // TODO: This unit test needs to be fixed to deal with BAZ-10499 static count changes [ywh 2008-10-08]
    public function doTestGetInboxUnreadMessageCount($expectedPut, $expectedCount = NULL) {
        $oldData = XN_Cache::get('inbox-unread-message-count-' . XN_Profile::current()->screenName);
        $count = $this->messageHelper->getInboxUnreadMessageCount(true);
        $newData = XN_Cache::get('inbox-unread-message-count-' . XN_Profile::current()->screenName);
        $put = $oldData !== $newData;
        $this->assertTrue(is_numeric($count));
        $this->assertEqual($expectedPut, $put);
        if (! is_null($expectedCount)) { $this->assertEqual($expectedCount, $count); }
    }


    public function testGetAlertsUnreadMessageCount1() {
        if ($this->_skip) {
            return;
        }
        $this->assertTestGetAlertsUnreadMessageCount(TRUE);
        $this->assertTestGetAlertsUnreadMessageCount(FALSE);
        Profiles_MessageHelper::invalidateAlertsUnreadMessageCountCache(XN_Profile::current()->screenName);
        $this->assertTestGetAlertsUnreadMessageCount(TRUE);
    }

    public function testGetAlertsUnreadMessageCount2() {
        if ($this->_skip) {
            return;
        }
        XN_Cache::put(TestMessageHelper::alertsUnreadMessageCountCacheId(XN_Profile::current()->screenName), serialize(array('expires' => time() - 5, 'count' => 123)));
        $this->assertTestGetAlertsUnreadMessageCount(TRUE);
    }

    public function testGetAlertsUnreadMessageCount3() {
        if ($this->_skip) {
            return;
        }
        XN_Cache::put(
            TestMessageHelper::alertsUnreadMessageCountCacheId(XN_Profile::current()->screenName),
            serialize(array('expires' => time() + 5, 'payload' => 123))
        );
        $this->assertTestGetAlertsUnreadMessageCount(FALSE, 123);
    }

    public function assertTestGetAlertsUnreadMessageCount($expectedPut, $expectedCount = NULL) {
        $oldData = XN_Cache::get('alerts-unread-message-count-' . XN_Profile::current()->screenName);
        $count = $this->messageHelper->getAlertsUnreadMessageCount();
        $newData = XN_Cache::get('alerts-unread-message-count-' . XN_Profile::current()->screenName);

        $this->assertTrue(is_numeric($count), "simple guard assertion - should never fail");

        $expectation = $expectedPut ?
            new NotIdenticalExpectation($oldData) :
            new IdenticalExpectation($oldData);
        $this->assert($expectation, $newData);

        if (! is_null($expectedCount)) { $this->assertEqual($expectedCount, $count); }
    }
    
    public function testParseRecipientList() {
        $this->assertEqual(array('jon@foo.com', 'jon@example.com'), TestMessageHelper::parseRecipientList('jon@foo.com; jon@example.com'));
        $this->assertEqual(array('jon@foo.com', 'jon@example.com'), TestMessageHelper::parseRecipientList('jon@foo.com ; ;; jon@example.com'));
        $this->assertEqual(array('jon@foo.com', 'jon@example.com'), TestMessageHelper::parseRecipientList('jon@foo.com, jon@example.com'));
        $this->assertEqual(array('jon@foo.com', 'jon@example.com'), TestMessageHelper::parseRecipientList('jon@foo.com , ,, jon@example.com'));
        $this->assertEqual(array('jon@foo.com', 'jon@example.com'), TestMessageHelper::parseRecipientList('jon@foo.com , ,; jon@example.com'));
        $this->assertEqual(array('jon@foo.com'), TestMessageHelper::parseRecipientList('jon@foo.com,'));
        $this->assertEqual(array('jon@foo.com', 'jon@example.com'), TestMessageHelper::parseRecipientList('jon@foo.com; jon@example.com,'));
    }

    public function testOtherParties() {
        $this->assertEqual(array(100 => 'jon0@example.com', 101 => 'jon1@example.com', 102 => 'jon2@example.com', 103 => 'jon3@example.com'), Profiles_MessageHelper::otherParties(array(
                $this->createMessage(100, array('jon0@example.com'), XN_Profile::current()->screenName),
                $this->createMessage(101, array('jon1@example.com'), XN_Profile::current()->email),
                $this->createMessage(102, array(XN_Profile::current()->screenName), 'jon2@example.com'),
                $this->createMessage(103, array(XN_Profile::current()->email), 'jon3@example.com'))));
    }

    private function createMessage($id, $recipients, $sender) {
        $message = new StdClass();
        $message->id = $id;
        $message->recipients = $recipients;
        $message->sender = $sender;
        return $message;
    }

}

class TestMessageHelper extends Profiles_MessageHelper {

    public static function inboxUnreadMessageCountCacheId($screenName) {
        return parent::inboxUnreadMessageCountCacheId($screenName);
    }

    public static function alertsUnreadMessageCountCacheId($screenName) {
        return parent::alertsUnreadMessageCountCacheId($screenName);
    }

    public static function parseRecipientList($list) {
        return parent::parseRecipientList($list);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
