<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_QueryHelper.php');
XG_App::includeFileOnce('/widgets/profiles/controllers/FriendController.php');
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_FriendListHelper.php');
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_UserSort.php');
Mock::generate('Profiles_FriendListHelper');
Mock::generate('XN_Query');
Mock::generate('stdClass', 'MockXN_Content', array('save'));

class Profiles_FriendControllerTest extends UnitTestCase {

    public function testUserInfoForListAction1a() {
        $this->doTestUserInfoForListAction(array(
                'q' => null,
                'screenName' => null,
                'searchMethod' => 'content',
                'throwSearchException' => false,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction1b() {
        $this->doTestUserInfoForListAction(array(
                'q' => 'cool',
                'screenName' => null,
                'searchMethod' => 'content',
                'throwSearchException' => false,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction1c() {
        $this->doTestUserInfoForListAction(array(
                'q' => null,
                'screenName' => 'jane',
                'searchMethod' => 'content',
                'throwSearchException' => false,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction1d() {
        $this->doTestUserInfoForListAction(array(
                'q' => 'cool',
                'screenName' => 'jane',
                'searchMethod' => 'content',
                'throwSearchException' => false,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction2a() {
        $this->doTestUserInfoForListAction(array(
                'q' => null,
                'screenName' => null,
                'searchMethod' => 'search',
                'throwSearchException' => false,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction2b() {
        $this->doTestUserInfoForListAction(array(
                'q' => 'cool',
                'screenName' => null,
                'searchMethod' => 'search',
                'throwSearchException' => false,
                'expectedFindUsersOrder' => null,
                'expectedSearchQuery' => true,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction2c() {
        $this->doTestUserInfoForListAction(array(
                'q' => null,
                'screenName' => 'jane',
                'searchMethod' => 'search',
                'throwSearchException' => false,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction2d() {
        $this->doTestUserInfoForListAction(array(
                'q' => 'cool',
                'screenName' => 'jane',
                'searchMethod' => 'search',
                'throwSearchException' => false,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction3a() {
        $this->doTestUserInfoForListAction(array(
                'q' => null,
                'screenName' => null,
                'searchMethod' => 'search',
                'throwSearchException' => true,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction3b() {
        $this->doTestUserInfoForListAction(array(
                'q' => 'cool',
                'screenName' => null,
                'searchMethod' => 'search',
                'throwSearchException' => true,
                'expectedFindUsersOrder' => null,
                'expectedSearchQuery' => true,
                'expectedUsers' => array(),
                'expectedNumUsers' => 0,
			));
    }

    public function testUserInfoForListAction3c() {
        $this->doTestUserInfoForListAction(array(
                'q' => null,
                'screenName' => 'jane',
                'searchMethod' => 'search',
                'throwSearchException' => true,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    public function testUserInfoForListAction3d() {
        $this->doTestUserInfoForListAction(array(
                'q' => 'cool',
                'screenName' => 'jane',
                'searchMethod' => 'search',
                'throwSearchException' => true,
                'expectedFindUsersOrder' => 'createdDate',
                'expectedSearchQuery' => false,
                'expectedUsers' => '[users]',
                'expectedNumUsers' => 17,
			));
    }

    private function doTestUserInfoForListAction($args) {
        $controller = new TestFriendController();
        $helper = new MockProfiles_FriendListHelper();
        if ($args['q'] && ! $args['screenName']) {
            $helper->expectOnce('getSearchMethod');
            $helper->setReturnValue('getSearchMethod', $args['searchMethod']);
        }
        if (! $args['expectedFindUsersOrder']) {
            $helper->expectNever('findUsers');
        } else {
            $filters = array();
            if ($args['screenName']) { $filters['contributorName'] = array('in', XN_Query::FRIENDS($args['screenName'])); }
            if ($args['q']) { $filters['my->searchText'] = array('likeic', $args['q']); }
            $helper->expectOnce('findUsers', array($filters, 50, 150, $args['expectedFindUsersOrder'], 'desc', ($args['q'] || $args['screenName']) ? false : true));
            $helper->setReturnValue('findUsers', array('users' => '[users]', 'numUsers' => 17));
        }
        if (! $args['expectedSearchQuery']) {
            $helper->expectNever('createQuery');
            $helper->expectNever('content');
        } else {
            $helper->expectOnce('createQuery', array('Search'));
            $helper->setReturnValue('createQuery', $searchQuery = new ExceptionMockDecorator(new MockXN_Query()));
            $searchQuery->expectOnce('begin', array(50));
            $searchQuery->setReturnValue('begin', $searchQuery);
            $searchQuery->expectOnce('end', array(150));
            $searchQuery->setReturnValue('end', $searchQuery);
            $searchQuery->expectAt(0, 'filter', array('type', 'like', 'User'));
            $searchQuery->expectAt(1, 'filter', array('my.approved', '!like', 'N'));
            $searchQuery->expectAt(2, 'filter', array('fulltext', 'like', $args['q']));
            $searchQuery->expectAt(3, 'filter', array('my.excludeFromPublicSearch', '!like', 'Y'));
            $searchQuery->expectAt(4, 'filter', array('my->xg_index_status', '!like', 'blocked'));
            $searchQuery->expectAt(5, 'filter', array('my->xg_index_status', '!like', 'pending'));
            $searchQuery->setReturnValue('filter', $searchQuery);
            $searchQuery->expectOnce('alwaysReturnTotalCount', array(true));
            $searchQuery->setReturnValue('alwaysReturnTotalCount', $searchQuery);
            $searchQuery->expectOnce('execute', array());
            if ($args['throwSearchException']) {
                $searchQuery->setReturnValue('execute', new Exception('Test exception'));
            } else {
                $searchQuery->expectOnce('getTotalCount', array());
                $searchQuery->setReturnValue('getTotalCount', 17);
                $searchQuery->setReturnValue('execute', array($this->createSearchResult(1), $this->createSearchResult(2), $this->createSearchResult(3)));
                $helper->expectOnce('content', array(array(1, 2, 3)));
                $helper->setReturnValue('content', '[users]');
            }
        }
        $userInfo = $controller->userInfoForListAction($args['q'], $args['screenName'], 50, 150, Profiles_UserSort::get('mostRecent'), $helper);
        $this->assertEqual($args['expectedUsers'], $userInfo['users']);
        $this->assertEqual($args['expectedNumUsers'], $userInfo['numUsers']);
        $this->assertNull($controller->searchTerm);
    }

    private function createSearchResult($id) {
        $searchResult = new stdClass();
        $searchResult->id = $id;
        return $searchResult;
    }

    public function testSortOptions() {
        $controller = new TestFriendController();
        $sortOptions = $controller->sortOptions(array('mostRecent', 'alphabetical', 'random'), Profiles_UserSort::get('mostRecent'), 'http://example.org/');
        $this->assertEqual(array(
                array('displayText' => 'Recently Added', 'url' => 'http://example.org/?sort=mostRecent', 'selected' => true),
                array('displayText' => 'Alphabetical', 'url' => 'http://example.org/?sort=alphabetical', 'selected' => false),
                array('displayText' => 'Random', 'url' => 'http://example.org/?sort=random', 'selected' => false)
                ), $sortOptions);
    }

}

class TestFriendController extends Profiles_FriendController {
    public function __construct() {
        return parent::__construct(W_Cache::getWidget('profiles'));
    }
    public function userInfoForListAction($q, $screenName, $start, $end, $sort, $helper) {
        return parent::userInfoForListAction($q, $screenName, $start, $end, $sort, $helper);
    }
    public function sortOptions($sortIds, $currentSort, $currentUrl) {
        return parent::sortOptions($sortIds, $currentSort, $currentUrl);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
