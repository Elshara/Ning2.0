<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_Filter.php');
XG_App::includeFileOnce('/widgets/forum/controllers/EmbedController.php');
XG_App::includeFileOnce('/widgets/groups/controllers/EmbedController.php');
XG_App::includeFileOnce('/widgets/groups/controllers/GroupController.php');
XG_App::includeFileOnce('/widgets/groups/lib/helpers/Groups_Filter.php');
XG_App::includeFileOnce('/widgets/music/lib/helpers/Music_SecurityHelper.php');
XG_App::includeFileOnce('/widgets/music/lib/helpers/Music_TrackHelper.php');
XG_App::includeFileOnce('/widgets/video/controllers/VideoController.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_VideoHelper.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_SecurityHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_PhotoHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_AlbumHelper.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_SecurityHelper.php');
XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
XG_App::includeFileOnce('/lib/XG_Embed.php');
Mock::generate('XG_Embed');

class XG_PromotionHelperTest extends UnitTestCase {

    public function tearDown() {
        $_GET['xn_debug'] = '';
        TestPromotionHelper::setQueriesEnabled(true);
    }

    public function testCurrentUserCanPromote() {
        $food = XN_Content::create('Food');
        $this->assertTrue(XG_PromotionHelper::currentUserCanPromote($food));
        $food->my->visibility = 'me';
        $this->assertFalse(XG_PromotionHelper::currentUserCanPromote($food));
        $food->my->visibility = 'all';
        $this->assertTrue(XG_PromotionHelper::currentUserCanPromote($food));
    }

    public function testIsPromoted() {
        $food = XN_Content::create('Food');
        $this->assertFalse(XG_PromotionHelper::isPromoted($food));
        XG_PromotionHelper::promote($food);
        $this->assertTrue(XG_PromotionHelper::isPromoted($food));
        XG_PromotionHelper::remove($food);
        $this->assertFalse(XG_PromotionHelper::isPromoted($food));
    }

    public function testAreQueriesEnabled1() {
        $this->doTestAreQueriesEnabled(array('albums' => array(), 'numAlbums' => 0),
                'return Photo_AlbumHelper::getSortedAlbums(array(\'promoted\' => true), null);');
    }

    public function testAreQueriesEnabled2() {
        $this->doTestAreQueriesEnabled(array(),
                'return Photo_PhotoHelper::getPromotedPhotos(1);');
    }

    public function testAreQueriesEnabled3() {
        $this->doTestAreQueriesEnabled(array('photos' => array(), 'numPhotos' => 0),
                'return Photo_PhotoHelper::getSortedPhotos(XN_Profile::current(), array("promoted" => true), null);');
    }

    public function testAreQueriesEnabled4() {
        $this->doTestAreQueriesEnabled(array('users' => array(), 'numUsers' => 0),
                'return User::find(array("promoted" => true));');
    }

    public function testAreQueriesEnabled5() {
        $this->doTestAreQueriesEnabled(array(),
                'return Video_VideoHelper::getPromotedVideos(1, false);');
    }

    public function testAreQueriesEnabled6() {
        $this->doTestAreQueriesEnabled(array('videos' => array(), 'numVideos' => 0),
                'return Video_VideoHelper::getPromotedVideos(1, true);');
    }

    public function testAreQueriesEnabled7() {
        $this->doTestAreQueriesEnabled(null,
                '$controller = new TestVideoController(); return $controller->action_getMostRecentPromotedVideo();');
    }

    public function testAreQueriesEnabled8() {
        $this->doTestAreQueriesEnabled(null,
                '$controller = new TestVideoController(); return $controller->action_getMostRecentPromotedLocalVideo();');
    }

    public function testAreQueriesEnabled9() {
        $this->doTestAreQueriesEnabled(array('posts' => array(), 'numPosts' => 0),
                'return BlogPost::find(array("promoted" => true));');
    }

    public function testAreQueriesEnabled10() {
        $this->doTestAreQueriesEnabled(array(),
                'return Music_TrackHelper::getPromotedTracks(1);');
    }

    public static $embed = null;

    public function testAreQueriesEnabled11() {
        self::$embed = new MockXG_Embed();
        self::$embed->setReturnValue('get', 5, array('itemCount'));
        self::$embed->setReturnValue('getType', 'homepage');
        self::$embed->setReturnValue('get', 'promoted', array('groupSet'));
        self::$embed->setReturnValue('getOwnerName', 'jane');
        $this->doTestAreQueriesEnabled(array(),
                'return TestGroupsEmbedController::groups(XG_PromotionHelperTest::$embed);');
    }

    public function testAreQueriesEnabled12() {
        $this->doTestAreQueriesEnabled(array('items' => array(), 'totalCount' => 0),
                'return TestGroupsController::getFeaturedGroups();');
    }

    public static $query;

    public function testAreQueriesEnabled13() {
        self::$query = XN_Query::create('Content')->filter('type', '=', 'Group');
        $this->doTestAreQueriesEnabled(array(array(), 0),
                'return TestGroupsController::executeQuery("promoted", XG_PromotionHelperTest::$query, null);');
    }

    public function testAreQueriesEnabled14() {
        XG_TestHelper::setCurrentWidget('forum');
        self::$embed = new MockXG_Embed();
        self::$embed->setReturnValue('get', 5, array('itemCount'));
        self::$embed->setReturnValue('getType', 'homepage');
        self::$embed->setReturnValue('get', 'promoted', array('topicSet'));
        $this->doTestAreQueriesEnabled(array(),
                'return TestForumEmbedController::topicsAndComments(XG_PromotionHelperTest::$embed);');
    }

    public function doTestAreQueriesEnabled($expectedResult, $code) {
        XN_Debug::allowDebug();
        $_GET['xn_debug'] = 'api-comm';
        $function = create_function('', $code);
        ob_start();
        $function();
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertPattern('@HTTP Method@', $output);
        TestPromotionHelper::setQueriesEnabled(false);
        ob_start();
        $result = $function();
        $output = trim(ob_get_contents());
        ob_end_clean();
        $this->assertEqual($expectedResult, $result);
        $this->assertEqual('', $output);
    }

}

class TestPromotionHelper extends XG_PromotionHelper {
    public static function setQueriesEnabled($queriesEnabled) {
        parent::$queriesEnabled = $queriesEnabled;
    }
}

class TestVideoController extends Video_VideoController {
    public function __construct() {
        parent::__construct(W_Cache::getWidget('video'));
    }
}

class TestGroupsEmbedController extends Groups_EmbedController {
    public function __construct() {
        parent::__construct(W_Cache::getWidget('groups'));
    }
    public function groups($embed, &$totalCount = null) {
        return parent::groups($embed, $totalCount);
    }
}

class TestForumEmbedController extends Forum_EmbedController {
    public function __construct() {
        parent::__construct(W_Cache::getWidget('forum'));
    }
    public function topicsAndComments($embed) {
        return parent::topicsAndComments($embed);
    }
}

class TestGroupsController extends Groups_GroupController {
    public function __construct() {
        parent::__construct(W_Cache::getWidget('groups'));
    }
    public function getFeaturedGroups() {
        return parent::getFeaturedGroups();
    }
    public function executeQuery($filterName, $query, $user) {
        return parent::executeQuery($filterName, $query, $user);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
