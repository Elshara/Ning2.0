<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
Mock::generate('TestRest');
Mock::generate('FriendRequestMessage', 'MockFriendRequestMessage', array('getMessagesProper', 'createMessage'));
Mock::generate('stdClass', 'MockXN_Content', array('save'));

class FriendRequestMessageTest extends UnitTestCase {

    public function setUp() {
        TestRest::setInstance(null);
        TestFriendRequestMessage::setInstance(null);
    }

    public function testGetMessagesProper() {
        $rest = new ExceptionMockDecorator(new MockTestRest());
        TestRest::setInstance($rest);
        $rest->expectAt(0, 'doRequest', array('GET', 'http://' . XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX . '/xn/atom/1.0/content(type%20%3D%20%27FriendRequestMessage%27%26my.recipient%20in%20%5B%27a%27%2C%27b%27%5D%26my.sender%20in%20%5B%27c%27%5D)?from=0&to=100&order=published@D', null, null, null));
        $rest->setReturnValue('doRequest',
'<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
    <title type="text">Content feed for devbazjon14b</title>
    <id>http%3A%2F%2Fdevbazjon14b.xna.ningops.net%2Fxn%2Fatom%2F1.0%2Fcontent%28id%3D668013%3APhoto%3A13825%29</id>
    <xn:size>0</xn:size>
    <updated>2007-11-07T20:19:51.229Z</updated>
</feed>');
        $this->assertEqual(0, count(TestFriendRequestMessage::getMessagesProper(array('a', 'b'), array('c'))));
    }

    public function testGetMessagesFrom() {
        $instance = new MockFriendRequestMessage();
        TestFriendRequestMessage::setInstance($instance);
        $instance->expectAt(0, 'getMessagesProper', array(array(XN_Profile::current()->screenName), array('a', 'b')));
        $instance->setReturnValue('getMessagesProper', array(
                self::createMessage(XN_Profile::current()->screenName, 'a', 'A'),
                self::createMessage(XN_Profile::current()->screenName, 'b', 'B')));
        $this->assertEqual(array('a' => 'A', 'b' => 'B'), TestFriendRequestMessage::getMessagesFrom(array('a', 'b')));
    }

    public function testGetMessagesTo() {
        $instance = new MockFriendRequestMessage();
        TestFriendRequestMessage::setInstance($instance);
        $instance->expectAt(0, 'getMessagesProper', array(array('a', 'b'), array(XN_Profile::current()->screenName)));
        $instance->setReturnValue('getMessagesProper', array(
                self::createMessage('a', XN_Profile::current()->screenName, 'A'),
                self::createMessage('b', XN_Profile::current()->screenName, 'B')));
        $this->assertEqual(array('a' => 'A', 'b' => 'B'), TestFriendRequestMessage::getMessagesTo(array('a', 'b')));
    }

    private function createMessage($recipient, $sender, $description) {
        $message = XN_Content::create('X');
        $message->my->recipient = $recipient;
        $message->my->sender = $sender;
        $message->description = $description;
        return $message;
    }

    private function doTestSetMessage($expectedCreateMessageCount, $message) {
        $instance = new MockFriendRequestMessage();
        TestFriendRequestMessage::setInstance($instance);
        $instance->expectAt(0, 'getMessagesProper', array(array('a'), array('b')));
        $instance->setReturnValue('getMessagesProper', array());
        $messageObject = new MockXN_Content();
        $messageObject->my = new stdClass();
        $instance->setReturnValue('createMessage', $messageObject);
        $instance->expectCallCount('createMessage', $expectedCreateMessageCount);
        TestFriendRequestMessage::setMessage('a', 'b', $message);
        return $messageObject;
    }

    public function testSetMessage1() {
        $message = $this->doTestSetMessage(1, ' hello ');
        $this->assertEqual('a', $message->my->recipient);
        $this->assertEqual('b', $message->my->sender);
        $this->assertTrue($message->isPrivate);
        $this->assertEqual(' hello ', $message->description);
    }

    public function testSetMessage2() {
        $this->doTestSetMessage(1, ' ');
    }

    public function testSetMessage3() {
        $this->doTestSetMessage(0, '');
    }

    public function testSetMessage4() {
        $message = $this->doTestSetMessage(1, str_repeat('X', 1+FriendRequestMessage::MAX_MESSAGE_LENGTH));
        $this->assertEqual(str_repeat('X', 1+FriendRequestMessage::MAX_MESSAGE_LENGTH), $message->description);
    }

}

class TestFriendRequestMessage extends FriendRequestMessage {

    public function getMessagesProper($recipients, $senders) {
        return parent::getMessagesProper($recipients, $senders);
    }

    public function setInstance($instance) {
        parent::$instance = $instance;
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
