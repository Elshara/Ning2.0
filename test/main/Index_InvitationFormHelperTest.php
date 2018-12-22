<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_InvitationFormHelper.php');
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
Mock::generate('TestRest');

class Index_InvitationFormHelperTest extends UnitTestCase {

    public function tearDown() {
        TestRest::setInstance(null);
    }

    public function testExtractEmailAddresses() {
        $this->assertEqual('1@a.com,2@a.com,3@a.com,4@a.com,5@a.com,6@a.com', implode(',', TestInvitationFormHelper::extractEmailAddresses(
                "1@a.com,,,2@a.com , 3@a.com;4@a.com\t5@a.com\n\n\n6@a.com")));
        $this->assertEqual('1@a.com', implode(',', TestInvitationFormHelper::extractEmailAddresses("1@a.com")));
        $this->assertEqual('1@a.com', implode(',', TestInvitationFormHelper::extractEmailAddresses("1@a.com,")));
        $this->assertEqual('1@a.com', implode(',', TestInvitationFormHelper::extractEmailAddresses("   1@a.com   ")));
        $this->assertEqual(0, count(TestInvitationFormHelper::extractEmailAddresses("      ")));
    }

    public function testGetEmailDomainsProper() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode(array()), $json->encode(TestInvitationFormHelper::getEmailDomainsProper(
                array())));
        $this->assertEqual($json->encode(array('hotmail.com' => 'hotmail.com', 'hotmail.co.uk' => 'hotmail.co.uk', 'msn.com' => 'msn.com')), $json->encode(TestInvitationFormHelper::getEmailDomainsProper(
                array('hotmail' => 'x'))));
        $this->assertEqual($json->encode(array('yahoo.com' => 'yahoo.com', 'yahoo.co.uk' => 'yahoo.co.uk', 'yahoo.ca' => 'yahoo.ca')), $json->encode(TestInvitationFormHelper::getEmailDomainsProper(
                array('yahoo' => 'x'))));
        $this->assertEqual($json->encode(array('aol.com' => 'aol.com')), $json->encode(TestInvitationFormHelper::getEmailDomainsProper(
                array('aol' => 'x'))));
        $this->assertEqual($json->encode(array('gmail.com' => 'gmail.com')), $json->encode(TestInvitationFormHelper::getEmailDomainsProper(
                array('gmail' => 'x'))));
    }

    public function testImportedContactsToContactList() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode(array()), $json->encode(TestInvitationFormHelper::importedContactsToContactList(
                array())));
        $this->assertEqual($json->encode(array(array('name' => 'Jon', 'emailAddress' => 'jon@example.org'), array('name' => null, 'emailAddress' => 'david@example.org'))), $json->encode(TestInvitationFormHelper::importedContactsToContactList(
                array(TestImportedContact::create('Foo', XN_Profile::current()->email), TestImportedContact::create('Jon', 'jon@example.org'), TestImportedContact::create(null, 'david@example.org')))));
    }

    public function testGenerateName() {
        $this->assertEqual('foo', Index_InvitationFormHelper::generateName('foo@example.org'));
        $this->assertEqual('hello', Index_InvitationFormHelper::generateName('hello'));
    }

    public function testAllImportedContacts() {
        $services = XN_ContactImportService::listServices();
        $mockRest = new ExceptionMockDecorator(new MockTestRest());
        TestRest::setInstance($mockRest);
        $mockRest->setReturnValueAt(0, 'doRequest', '<?xml version=\'1.0\' encoding=\'UTF-8\'?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
    <title type="text">Ning Import Response</title>
    <id>http://app.ning.com/xn/rest/1.0/contact/import(id=ABC-123-FOO)</id>
    <updated>2007-10-19T21:22:32.407Z</updated>
    <xn:size>10000</xn:size>
    <entry>
        <name>Jon Aquino</name>
        <email>JonathanAquino@im.secondlife.com</email>
    </entry>
    <entry>
        <email>tester@gmail.com</email>
    </entry>
</feed>');
        $result = $services['csv']->import("E-mail\nfoo@example.org\n");
        $mockRest->setReturnValueAt(1, 'doRequest', new XN_Exception(
'<?xml version=\'1.0\' encoding=\'utf-8\'?>
<errors>
  <error code="contact-import:auth:4">Login failed</error>
</errors>', 500));
        $this->assertEqual(array('contact-import:auth:4' => 'Login failed', 'xn:status' => 500), TestInvitationFormHelper::allImportedContacts($result));
    }

    public function testAllImportedContacts2() {
        TestRest::setInstance(new FakeRest());
        $data = "E-mail\n";
        for ($i = 0; $i < 7500; $i++) {
            $data .= "foo$i@example.org\n";
        }
        $services = XN_ContactImportService::listServices();
        $result = $services['csv']->import($data);
        FakeRest::$requestCount = 0;
        $this->assertEqual(7500, count(TestInvitationFormHelper::allImportedContacts($result)));
        $this->assertEqual(1, FakeRest::$requestCount);
        FakeRest::$requestCount = 0;
        $this->assertEqual(7500, count(TestInvitationFormHelper::allImportedContacts($result)));
        $this->assertEqual(0, FakeRest::$requestCount);
    }

    public function testIsVCardData() {
        $this->assertFalse(TestInvitationFormHelper::isVCardData('hello world'));
        $this->assertTrue(TestInvitationFormHelper::isVCardData('   begin:vcard'   ));
        $this->assertTrue(TestInvitationFormHelper::isVCardData('   BEGIN:VCARD   '));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('begin:vcard', 'ASCII')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   begin:vcard   ', 'ASCII')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   BEGIN:VCARD   ', 'ASCII')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   begin:vcard   ', 'UTF-8')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   BEGIN:VCARD   ', 'UTF-8')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   begin:vcard   ', 'UTF-16')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   BEGIN:VCARD   ', 'UTF-16')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   begin:vcard   ', 'UTF-16BE')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   BEGIN:VCARD   ', 'UTF-16BE')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   begin:vcard   ', 'UTF-16LE')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   BEGIN:VCARD   ', 'UTF-16LE')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   begin:vcard   ', 'UTF-32')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   BEGIN:VCARD   ', 'UTF-32')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   begin:vcard   ', 'UTF-32BE')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   BEGIN:VCARD   ', 'UTF-32BE')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   begin:vcard   ', 'UTF-32LE')));
        $this->assertTrue(TestInvitationFormHelper::isVCardData(mb_convert_encoding('   BEGIN:VCARD   ', 'UTF-32LE')));
    }

    public function testScreenNamesToPseudoEmailAddresses() {
        $this->assertEqual(array('a@users', 'b@users', 'c@users'), Index_InvitationFormHelper::screenNamesToPseudoEmailAddresses(array('a', 'b', 'c')));
        $this->assertEqual(array(), Index_InvitationFormHelper::screenNamesToPseudoEmailAddresses(array()));
    }

    public function testProcessInviteFriendsForm1() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $_POST = array(
            'friendSet' => Index_MessageHelper::ALL_FRIENDS,
            'screenNamesIncluded' => $json->encode(array('a1', 'a2')),
            'screenNamesExcluded' => $json->encode(array('a3', 'a4')),
            'inviteFriendsMessage' => str_repeat('a', Index_InvitationHelper::MAX_MESSAGE_LENGTH),
            );
        $this->assertEqual(array(
                'friendSet' => Index_MessageHelper::ALL_FRIENDS,
                'contactList' => array(array('name' => NULL, 'emailAddress' => 'a1@users'), array('name' => NULL, 'emailAddress' => 'a2@users')),
                'screenNamesExcluded' => array('a3', 'a4')),
            Index_InvitationFormHelper::processInviteFriendsForm());
    }

    public function testProcessInviteFriendsForm2() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $_POST = array(
            'friendSet' => Index_MessageHelper::ALL_FRIENDS,
            'screenNamesIncluded' => $json->encode(array('a1', 'a2')),
            'screenNamesExcluded' => $json->encode(array('a3', 'a4')),
            'inviteFriendsMessage' => str_repeat('a', Index_InvitationHelper::MAX_MESSAGE_LENGTH + 1),
            );
        $this->assertEqual(array('errorHtml' => xg_html('MESSAGE_TOO_LONG', Index_InvitationHelper::MAX_MESSAGE_LENGTH)),
            Index_InvitationFormHelper::processInviteFriendsForm());
    }

    public function testProcessInviteFriendsForm3() {
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $_POST = array(
            'friendSet' => NULL,
            'screenNamesIncluded' => $json->encode(array()),
            'screenNamesExcluded' => $json->encode(array('a3', 'a4')),
            'inviteFriendsMessage' => str_repeat('a', Index_InvitationHelper::MAX_MESSAGE_LENGTH),
            );
        $this->assertEqual(array('errorHtml' => xg_html('PLEASE_CHOOSE_FRIENDS')),
            Index_InvitationFormHelper::processInviteFriendsForm());
    }

}

class TestInvitationFormHelper extends Index_InvitationFormHelper {
    public static function extractEmailAddresses($s) { return parent::extractEmailAddresses($s); }
    public static function getEmailDomainsProper($importServices) { return parent::getEmailDomainsProper($importServices); }
    public static function importedContactsToContactList($importedContacts) { return parent::importedContactsToContactList($importedContacts); }
    public static function allImportedContacts($contactImportResult) {
        return Index_InvitationFormHelper::allImportedContacts($contactImportResult);
    }
    public static function isVCardData($data) { return parent::isVCardData($data); }
}

class TestImportedContact extends XN_ImportedContact {
    public static function create($name, $email) {
        $contact = new XN_ImportedContact();
        $contact->_data = array('name' => $name, 'email' => $email);
        return $contact;
    }
}

class FakeRest extends XN_REST {
    public static $requestCount = 0;
    public function __construct() {}
    public function doRequest($method, $url, &$body = null,$contentType=null, $additionalHeaders = null) {
        static $rest = null;
        if (! $rest) { $rest = new XN_REST(); }
        self::$requestCount++;
        return $rest->doRequest($method, $url, $body,$contentType, $additionalHeaders);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
