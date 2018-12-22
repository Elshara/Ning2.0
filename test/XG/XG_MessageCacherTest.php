<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Message.php');

class XG_MessageCacherTest extends UnitTestCase {

    public function testSetMessage() {
        $messageCacher = new XG_MessageCacher();
        $data = array();
        $messageCacher->setMessage('jon@jon.com', 'david@david.com', 'Hello!', $data);
        $this->assertEqual('Hello!', $messageCacher->getMessage('jon@jon.com', 'david@david.com', $data));
        $this->assertNull($messageCacher->getMessage('jon@jon.com', 'fred@david.com', $data));
        $this->assertEqual('Hello!', $messageCacher->getMessage('fred@jon.com', 'david@david.com', $data));
    }

}

XG_App::includeFileOnce('/test/test_footer.php');
