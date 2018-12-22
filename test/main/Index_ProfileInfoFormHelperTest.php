<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_ProfileInfoFormHelper.php');

class Index_ProfileInfoFormHelperTest extends UnitTestCase {

    public function testRead() {
        $profile = XG_TestHelper::createProfile('charlie');
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertEqual('1', $values['doNotDisplayAge']);
        $this->assertEqual('', $values['doNotDisplayGender']);
        $user = XN_Content::create('User');
        TestUser::setScreenNameToUserMap(array('charlie' => $user));
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertNull($values['fullName']);
        $this->assertNull($values['birthdateYear']);
        $this->assertNull($values['birthdateMonth']);
        $this->assertNull($values['birthdateDay']);
        $this->assertEqual('', $values['doNotDisplayAge']);
        $this->assertEqual('', $values['doNotDisplayGender']);
        $profile->birthdate = '';
        $user->my->displayAge = 'Y';
        $user->my->displayGender = 'Y';
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertNull($values['fullName']);
        $this->assertNull($values['birthdateYear']);
        $this->assertNull($values['birthdateMonth']);
        $this->assertNull($values['birthdateDay']);
        $this->assertEqual('', $values['doNotDisplayAge']);
        $profile->birthdate = '1977-02-15';
        $user->my->displayAge = 'N';
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertNull($values['fullName']);
        $this->assertEqual(1977, $values['birthdateYear']);
        $this->assertEqual(2, $values['birthdateMonth']);
        $this->assertEqual(15, $values['birthdateDay']);
        $this->assertEqual('1', $values['doNotDisplayAge']);
        $profile->birthdate = '1977-01-32';
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertNull($values['fullName']);
        $this->assertEqual(1977, $values['birthdateYear']);
        $this->assertEqual(1, $values['birthdateMonth']);
        $this->assertEqual(32, $values['birthdateDay']);
        $this->assertEqual('', $values['gender']);
        $values = Index_ProfileInfoFormHelper::read($profile);
        $profile->gender = 'm';
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertEqual('m', $values['gender']);
        $profile->gender = 'f';
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertEqual('f', $values['gender']);
        $profile->gender = '';
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertNull($values['fullName']);
        $this->assertEqual('', $values['gender']);
        $this->assertNull($values['location']);
        $this->assertNull($values['country']);
        $profile->location = 'Victoria';
        $profile->country = 'AU';
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertEqual('Victoria', $values['location']);
        $this->assertEqual('AU', $values['country']);
        $profile->fullName = 'Boswell';
        $values = Index_ProfileInfoFormHelper::read($profile);
        $this->assertEqual('Boswell', $values['fullName']);
    }

    public function testWrite() {
        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $user = XN_Content::create('User');
        $_POST = array();
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual(null, $profile->fullName);
        $this->assertEqual(null, $profile->gender);
        $this->assertEqual(null, $profile->birthdate);
        $this->assertEqual(null, $profile->location);
        $this->assertEqual(null, $profile->country);
        $this->assertNull($user->my->displayAge);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $_POST = array('fullName' => 'Sue', 'gender' => 'f', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'location' => 'Victoria', 'country' => 'CA', 'doNotDisplayAge' => '1');
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual('Sue', $profile->fullName);
        $this->assertEqual('f', $profile->gender);
        $this->assertEqual(null, $profile->birthdate);
        $this->assertEqual('Victoria', $profile->location);
        $this->assertEqual('CA', $profile->country);
        $this->assertEqual('N', $user->my->displayAge);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $_POST = array('fullName' => 'Sue', 'gender' => 'f', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '2', 'birthdateDay' => '15', 'location' => 'Victoria', 'country' => 'CA', 'doNotDisplayAge' => '');
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual('Sue', $profile->fullName);
        $this->assertEqual('f', $profile->gender);
        $this->assertEqual('1977-02-15', $profile->birthdate);
        $this->assertEqual('Victoria', $profile->location);
        $this->assertEqual('CA', $profile->country);
        $this->assertEqual('Y', $user->my->displayAge);

        $_POST = array('aboutQuestionsShown' => 'Y');
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual('Sue', $profile->fullName);
        $this->assertEqual('f', $profile->gender);
        $this->assertEqual('1977-02-15', $profile->birthdate);
        $this->assertEqual('Victoria', $profile->location);
        $this->assertEqual('CA', $profile->country);
        $this->assertEqual('Y', $user->my->displayAge);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $_POST = array('fullName' => 'Sue', 'gender' => 'f', 'birthdateYear' => '1977', 'location' => 'Victoria', 'country' => 'CA');
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual('Sue', $profile->fullName);
        $this->assertEqual(null, $profile->gender);
        $this->assertEqual(null, $profile->birthdate);
        $this->assertEqual(null, $profile->location);
        $this->assertEqual(null, $profile->country);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $_POST = array('fullName' => 'Jason', 'gender' => 'm', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '2', 'birthdateDay' => '15');
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual('Jason', $profile->fullName);
        $this->assertEqual('m', $profile->gender);
        $this->assertEqual('1977-02-15', $profile->birthdate);
        $this->assertEqual(null, $profile->location);
        $this->assertEqual(null, $profile->country);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $_POST = array('fullName' => 'Jason', 'gender' => 'x', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '2', 'birthdateDay' => '31');
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual('Jason', $profile->fullName);
        $this->assertEqual(null, $profile->gender);
        $this->assertEqual('1977-03-03', $profile->birthdate);
        $this->assertEqual(null, $profile->location);
        $this->assertEqual(null, $profile->country);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $_POST = array('fullName' => 'Jason', 'gender' => 'x', 'aboutQuestionsShown' => 'N', 'birthdateYear' => '1977', 'birthdateMonth' => '1', 'birthdateDay' => '33');
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual('Jason', $profile->fullName);
        $this->assertEqual(null, $profile->gender);
        $this->assertEqual(null, $profile->birthdate);
        $this->assertEqual(null, $profile->location);
        $this->assertEqual(null, $profile->country);

        $profile = XG_TestHelper::createProfile('charlie');
        TestUser::setScreenNameToUserMap(array('charlie' => $user));
        $profile->gender = 'm';
        $profile->birthdate = '1977-03-03';
        $profile->location = 'Victoria';
        $profile->country = 'CA';
        $_POST = array('fullName' => 'Jason', 'gender' => 'x', 'aboutQuestionsShown' => 'N', 'birthdateYear' => '1977', 'birthdateMonth' => '1', 'birthdateDay' => '33');
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual('Jason', $profile->fullName);
        $this->assertEqual('m', $profile->gender);
        $this->assertEqual('1977-03-03', $profile->birthdate);
        $this->assertEqual('Victoria', $profile->location);
        $this->assertEqual('CA', $profile->country);
        $this->assertEqual('Jason', $user->my->fullName);
        $this->assertEqual('m', $user->my->gender);
        $this->assertEqual('1977-03-03', $user->my->birthdate);
        $this->assertEqual('Victoria', $user->my->location);
        $this->assertEqual('CA', $user->my->country);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $_POST = array('fullName' => 'Jason', 'gender' => 'x', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '1', 'birthdateDay' => '2');
        Index_ProfileInfoFormHelper::write($profile, $user, true);
        $this->assertEqual('Jason', $profile->fullName);
        $this->assertEqual(null, $profile->gender);
        $this->assertEqual('1977-01-02', $profile->birthdate);
        $this->assertEqual(null, $profile->location);
        $this->assertEqual(null, $profile->country);
    }

    public function testWrite2() {
        // BAZ-4716 [Jon Aquino 2007-10-02]
        $user = XN_Content::create('User');

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $_POST = array('fullName' => 'Sue', 'gender' => 'f', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '2', 'birthdateDay' => '15', 'location' => 'Victoria', 'country' => 'CA');
        Index_ProfileInfoFormHelper::write($profile, $user, false);
        $this->assertEqual('Sue', $profile->fullName);
        $this->assertEqual('f', $profile->gender);
        $this->assertEqual('1977-02-15', $profile->birthdate);
        $this->assertEqual('Victoria', $profile->location);
        $this->assertEqual('CA', $profile->country);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $profile->fullName = '0';
        $_POST = array('fullName' => 'Sue', 'gender' => 'f', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '2', 'birthdateDay' => '15', 'location' => 'Victoria', 'country' => 'CA');
        Index_ProfileInfoFormHelper::write($profile, $user, false);
        $this->assertEqual('0', $profile->fullName);
        $this->assertEqual('f', $profile->gender);
        $this->assertEqual('1977-02-15', $profile->birthdate);
        $this->assertEqual('Victoria', $profile->location);
        $this->assertEqual('CA', $profile->country);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $profile->fullName = 'Ann';
        $profile->gender = 'x';
        $_POST = array('fullName' => 'Sue', 'gender' => 'f', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '2', 'birthdateDay' => '15', 'location' => 'Victoria', 'country' => 'CA');
        Index_ProfileInfoFormHelper::write($profile, $user, false);
        $this->assertEqual('Ann', $profile->fullName);
        $this->assertEqual('x', $profile->gender);
        $this->assertEqual('1977-02-15', $profile->birthdate);
        $this->assertEqual('Victoria', $profile->location);
        $this->assertEqual('CA', $profile->country);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $profile->fullName = 'Ann';
        $profile->gender = 'x';
        $profile->birthdate = '2007-01-01';
        $_POST = array('fullName' => 'Sue', 'gender' => 'f', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '2', 'birthdateDay' => '15', 'location' => 'Victoria', 'country' => 'CA');
        Index_ProfileInfoFormHelper::write($profile, $user, false);
        $this->assertEqual('Ann', $profile->fullName);
        $this->assertEqual('x', $profile->gender);
        $this->assertEqual('2007-01-01', $profile->birthdate);
        $this->assertEqual('Victoria', $profile->location);
        $this->assertEqual('CA', $profile->country);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $profile->fullName = 'Ann';
        $profile->gender = 'x';
        $profile->birthdate = '2007-01-01';
        $profile->location = 'Melbourne';
        $_POST = array('fullName' => 'Sue', 'gender' => 'f', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '2', 'birthdateDay' => '15', 'location' => 'Victoria', 'country' => 'CA');
        Index_ProfileInfoFormHelper::write($profile, $user, false);
        $this->assertEqual('Ann', $profile->fullName);
        $this->assertEqual('x', $profile->gender);
        $this->assertEqual('2007-01-01', $profile->birthdate);
        $this->assertEqual('Melbourne', $profile->location);
        $this->assertEqual('CA', $profile->country);

        $profile = XN_Profile::create('charlie@example.net', 'z3braMu55e1');
        $profile->fullName = 'Ann';
        $profile->gender = 'x';
        $profile->birthdate = '2007-01-01';
        $profile->location = 'Melbourne';
        $profile->country = 'AU';
        $_POST = array('fullName' => 'Sue', 'gender' => 'f', 'aboutQuestionsShown' => 'Y', 'birthdateYear' => '1977', 'birthdateMonth' => '2', 'birthdateDay' => '15', 'location' => 'Victoria', 'country' => 'CA');
        Index_ProfileInfoFormHelper::write($profile, $user, false);
        $this->assertEqual('Ann', $profile->fullName);
        $this->assertEqual('x', $profile->gender);
        $this->assertEqual('2007-01-01', $profile->birthdate);
        $this->assertEqual('Melbourne', $profile->location);
        $this->assertEqual('AU', $profile->country);
    }

    public function testIsShowingGenderFieldOnCreateProfilePage() {
        $this->assertTrue(Index_ProfileInfoFormHelper::isShowingGenderFieldOnCreateProfilePage(self::createWidget(array())));
        $this->assertFalse(Index_ProfileInfoFormHelper::isShowingGenderFieldOnCreateProfilePage(self::createWidget(array('showGenderFieldOnCreateProfilePage' => '0'))));
        $this->assertTrue(Index_ProfileInfoFormHelper::isShowingGenderFieldOnCreateProfilePage(self::createWidget(array('showGenderFieldOnCreateProfilePage' => '1'))));
    }

    public function testIsShowingLocationFieldOnCreateProfilePage() {
        $this->assertTrue(Index_ProfileInfoFormHelper::isShowingLocationFieldOnCreateProfilePage(self::createWidget(array())));
        $this->assertFalse(Index_ProfileInfoFormHelper::isShowingLocationFieldOnCreateProfilePage(self::createWidget(array('showLocationFieldOnCreateProfilePage' => '0'))));
        $this->assertTrue(Index_ProfileInfoFormHelper::isShowingLocationFieldOnCreateProfilePage(self::createWidget(array('showLocationFieldOnCreateProfilePage' => '1'))));
    }

    public function testIsShowingCountryFieldOnCreateProfilePage() {
        $this->assertTrue(Index_ProfileInfoFormHelper::isShowingCountryFieldOnCreateProfilePage(self::createWidget(array())));
        $this->assertFalse(Index_ProfileInfoFormHelper::isShowingCountryFieldOnCreateProfilePage(self::createWidget(array('showCountryFieldOnCreateProfilePage' => '0'))));
        $this->assertTrue(Index_ProfileInfoFormHelper::isShowingCountryFieldOnCreateProfilePage(self::createWidget(array('showCountryFieldOnCreateProfilePage' => '1'))));
    }

    private function createWidget($config) {
        $widget = new stdClass();
        $widget->config = $config;
        return $widget;
    }

    public function testIsBirthdateValid() {
        $this->assertTrue(Index_ProfileInfoFormHelper::isBirthdateValid(array()));
        $this->assertFalse(Index_ProfileInfoFormHelper::isBirthdateValid(array('birthdateYear' => '1950')));
        $this->assertTrue(Index_ProfileInfoFormHelper::isBirthdateValid(array('birthdateYear' => '1950', 'birthdateMonth' => '2', 'birthdateDay' => '28')));
        $this->assertFalse(Index_ProfileInfoFormHelper::isBirthdateValid(array('birthdateYear' => '1950', 'birthdateMonth' => '2', 'birthdateDay' => '30')));
        $this->assertTrue(Index_ProfileInfoFormHelper::isBirthdateValid(array('birthdateYear' => '1950', 'birthdateMonth' => '12', 'birthdateDay' => '13')));
        $this->assertFalse(Index_ProfileInfoFormHelper::isBirthdateValid(array('birthdateYear' => '1950', 'birthdateMonth' => '13', 'birthdateDay' => '12')));
        $this->assertTrue(Index_ProfileInfoFormHelper::isBirthdateValid(array('birthdateYear' => '1952', 'birthdateMonth' => '2', 'birthdateDay' => '29')));
    }

}

class TestUser extends User {
    public static function setScreenNameToUserMap($screenNameToUserMap) {
        parent::$screenNameToUserMap = $screenNameToUserMap;
    }
}


require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
