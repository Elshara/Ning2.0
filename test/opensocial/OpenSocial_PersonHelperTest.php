<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class OpenSocial_PersonHelperTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('opensocial');
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/OpenSocial_SecurityHelper.php');
        $this->appUrl = "http://opensocial-resources.googlecode.com/svn/samples/coderunner/trunk/coderunner.xml";
    }

    public function testGetPeople() {
        $currentUser = XN_Profile::current()->screenName;
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $currentUser, $currentUser, array('OWNER'), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(1, count($r['people']));
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $currentUser, $currentUser, array($currentUser), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(1, count($r['people']));
        
        $friends = $this->findFriends($currentUser);
        $MIN_FRIENDS = 2;
        if (count($friends) < $MIN_FRIENDS) {
            echo "<p>Cannot test all of getPeople when logged in as a user with less than $MIN_FRIENDS friends</p>";
            return;
        }
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $currentUser, $currentUser, array('VIEWER_FRIENDS'), '', 'ALL', 'NAME', 0, 2));
        $this->assertEqual(2, count($r['people']));
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $currentUser, $currentUser, array('VIEWER_FRIENDS'), '', 'ALL', 'NAME', 0, 100));
        $this->assertTrue(count($r['people']) >= $MIN_FRIENDS && count($r) <= 100);
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $currentUser, $currentUser, array($friends[0]), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(1, count($r['people']));
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $currentUser, $currentUser, array('Not a user'), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(0, count($r['people']));
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $currentUser, $currentUser, array('OWNER', 'VIEWER'), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(1, count($r['people']));
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, OpenSocial_PersonHelper::ANONYMOUS, $currentUser, array('OWNER', 'VIEWER'), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(2, count($r['people']));
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, OpenSocial_PersonHelper::ANONYMOUS, OpenSocial_PersonHelper::ANONYMOUS, array('OWNER', 'VIEWER'), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(1, count($r['people']));
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, OpenSocial_PersonHelper::ANONYMOUS, OpenSocial_PersonHelper::ANONYMOUS, array('OWNER', 'VIEWER'), '', 'ALL', 'NAME', 1, 100));
        $this->assertEqual(0, count($r['people']));
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, OpenSocial_PersonHelper::ANONYMOUS, OpenSocial_PersonHelper::ANONYMOUS, array('OWNER_FRIENDS'), '', 'ALL', 'NAME', 1, 100));
        $this->assertEqual(0, count($r['people']));
        
        $notFriend = $this->findNotFriend($currentUser, $friends);
        if (! $notFriend) {
            echo "<p>Cannot test all of getPeople when logged in as this user as could not find a user who is not their friend quickly enough.</p>";
            return;
        }
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $currentUser, $currentUser, array($notFriend), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(0, count($r['people'])); 
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $notFriend, $currentUser, array('OWNER', 'VIEWER'), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(2, count($r['people']));
        $r = (OpenSocial_PersonHelper::getPeople($this->appUrl, $notFriend, $currentUser, array(''), '', 'ALL', 'NAME', 0, 100));
        $this->assertEqual(0, count($r['people']));
    }

    public function testRequestForAnonymous() {
        $currentUser = XN_Profile::current()->screenName;
        $this->assertTrue(OpenSocial_PersonHelper::requestForAnonymous(OpenSocial_PersonHelper::ANONYMOUS, $currentUser, $currentUser));
        $this->assertTrue(OpenSocial_PersonHelper::requestForAnonymous("VIEWER", OpenSocial_PersonHelper::ANONYMOUS, $currentUser));
        $this->assertTrue(OpenSocial_PersonHelper::requestForAnonymous("OWNER", $currentUser, OpenSocial_PersonHelper::ANONYMOUS));
        $this->assertFalse(OpenSocial_PersonHelper::requestForAnonymous($currentUser, $currentUser, $currentUser));
        $this->assertFalse(OpenSocial_PersonHelper::requestForAnonymous("VIEWER", $currentUser, OpenSocial_PersonHelper::ANONYMOUS));
        $this->assertFalse(OpenSocial_PersonHelper::requestForAnonymous("OWNER", OpenSocial_PersonHelper::ANONYMOUS, $currentUser));
        $this->assertFalse(OpenSocial_PersonHelper::requestForAnonymous("OWNER_FRIENDS", OpenSocial_PersonHelper::ANONYMOUS, OpenSocial_PersonHelper::ANONYMOUS));
    }

    public function testRequestForAnonymousFriends() {
        $currentUser = XN_Profile::current()->screenName;
        $this->assertTrue(OpenSocial_PersonHelper::requestForAnonymousFriends("VIEWER_FRIENDS", OpenSocial_PersonHelper::ANONYMOUS, $currentUser));
        $this->assertTrue(OpenSocial_PersonHelper::requestForAnonymousFriends("OWNER_FRIENDS", $currentUser, OpenSocial_PersonHelper::ANONYMOUS));
        $this->assertFalse(OpenSocial_PersonHelper::requestForAnonymousFriends("VIEWER_FRIENDS", $currentUser, OpenSocial_PersonHelper::ANONYMOUS));
        $this->assertFalse(OpenSocial_PersonHelper::requestForAnonymousFriends("OWNER_FRIENDS", OpenSocial_PersonHelper::ANONYMOUS, $currentUser));
        $this->assertFalse(OpenSocial_PersonHelper::requestForAnonymousFriends("", OpenSocial_PersonHelper::ANONYMOUS, OpenSocial_PersonHelper::ANONYMOUS));
    }
    
    public function testCheckIds() {
        $ids = array('a', 'b', 'c', 'VIEWER_FRIENDS');
        $people = array(array('id' => 'a'), array('id' => 'b'), array('id' => 'c'));
        $this->assertTrue(OpenSocial_PersonHelper::checkIds($ids, $people));
        $ids = array('a', 'b', 'z');
        $this->assertFalse(OpenSocial_PersonHelper::checkIds($ids, $people));
        $people = array(array('id' => 'a'), array('id' => 'b'));
        $this->assertFalse(OpenSocial_PersonHelper::checkIds($ids, $people));
    }
    
    //TODO: testOnlyIncludeViewerOwnerAndTheirFriendsFilter [Thomas David Baker 2008-08-04]
    
    private function findFriends($user) {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $friendsObjs = Profiles_UserHelper::findFriendsOf($user, 0, 100);
        $friends = array();
        foreach ($friendsObjs['friends'] as $friend) {
            $friends[] = $friend->title;
        }
        return $friends;
    }
    
    private function findNotFriend($user, $friends) {
        $friends[] = $user;
        $query = XN_Query::create('Content')->filter('type', '=', 'User')->filter('owner')->filter('title', '!in', $friends)->filter('my->xg_index_status', '!=', 'pending')->filter('my->xg_index_status', '!=', 'blocked')->filter('my->xg_index_status', '!=', 'unfinished')->begin(0)->end(5);
        foreach ($query->execute() as $u) {
            // Make sure they really definitely aren't a friend of $user and just didn't happen to make it into $friends.
            $query = XN_Query::create('Content')->filter('type', '=', 'User')->filter('owner')->filter('title', '=', $u->title)->filter('contributorName', 'in', XN_Query::FRIENDS($user))->begin(0)->end(5);
            if (! $query->execute()) {
                return $u->title;
            }
        }
        return null;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
