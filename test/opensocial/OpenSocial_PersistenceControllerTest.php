<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class OpenSocial_PersistenceControllerTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('opensocial');
        W_Cache::current('W_Widget')->includeFileOnce('/controllers/PersistenceController.php');
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
    }
    
    public function testIdQueryNeeded() {
        $ids = array('a', 'b');
        $this->assertFalse(OpenSocial_PersistenceController::idQueryNeeded('a', 'b', $ids));
        $this->assertTrue(OpenSocial_PersistenceController::idQueryNeeded('a', 'c', $ids));
        $this->assertTrue(OpenSocial_PersistenceController::idQueryNeeded('c', 'b', $ids));
    }

    public function testIdQuery() {
        $currentUser = XN_Profile::current()->screenName;
        //$this->assertEqual(array(), OpenSocial_PersistenceController::idQuery(OpenSocial_PersonHelper::ANONYMOUS, 'OpenSocialOwner', array($currentUser)));
        $this->assertEqual(array($currentUser), OpenSocial_PersistenceController::idQuery('', $currentUser, array($currentUser)));

        $query = XN_Query::create('Content')->filter('owner')
            ->filter('type', '=', 'User')->filter('contributorName', 'in', XN_Query::FRIENDS($currentUser))
            ->begin(0)->end(3);
        $currentUserFriends = array();
        foreach ($query->execute() as $friend) {
            $currentUserFriends[] = $friend->title;
        }
        if ($currentUserFriends) {
            $ids = OpenSocial_PersistenceController::idQuery($currentUser, $currentUser, $currentUserFriends);
            $this->assertEqual(count($currentUserFriends), count($ids));
            foreach ($currentUserFriends as $friend) {
                $this->assertTrue(in_array($friend, $ids));
            }
        } else {
            echo "<p>Current user has not friends so unable to fully test idQuery.</p>";
        }
    }
    
    public function testAssembleData() {
        $currentUser = XN_Profile::current()->screenName;
        $x = OpenSocialAppData::create('http://example.com/9999999996', $currentUser, false /* installed from application directory */);
        $x->save();
        $x->set('foo', 'bar');
        $x->set('baz', 'quux');
        $d = OpenSocial_PersistenceController::assembleData(array($currentUser => $x), '*');
        $this->assertEqual(array($currentUser => array('foo' => 'bar', 'baz' => 'quux')), $d);
        $d = OpenSocial_PersistenceController::assembleData(array($currentUser => $x), 'foo,monkey');
        $this->assertEqual(array($currentUser => array('foo' => 'bar')), $d);
        $d = OpenSocial_PersistenceController::assembleData(array($currentUser => $x), 'monkey,donkey,tortoise,camel');
        $this->assertEqual(array(), $d);
    }
    
    public function testExtractData() {
        $data = array('a' => 1, 'b' => 2, 'c' => 3);
        $this->assertEqual($data, OpenSocial_PersistenceController::extractData($data, '*'));
        $this->assertEqual(array('b' => 2), OpenSocial_PersistenceController::extractData($data, 'b'));
        $this->assertEqual(array('b' => 2, 'c' => 3), OpenSocial_PersistenceController::extractData($data, 'b,c'));
        $this->assertEqual(array(), OpenSocial_PersistenceController::extractData($data, 'z'));
        $this->assertEqual(array('b' => 2), OpenSocial_PersistenceController::extractData($data, 'b,z'));
    }
    
    public function testValidIds() {
        $ids = array('a', 'b');
        $this->assertEqual($ids, OpenSocial_PersistenceController::validIds('a', 'b', $ids));
    }
    
    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
