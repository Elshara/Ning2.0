<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_Filter.php');

class CategoryTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
        TestCategory::setCategoriesForTitlesAndIds(null);
    }

    public function testCleanDescription() {
        $this->assertEqual('<b>Hello</b> <a href="http://google.com">http://google.com</a> world', Category::cleanDescription('  <b>Hello</b> http://google.com world<script>  '));
        $this->assertEqual('', Category::cleanDescription('  '));
    }

    public function testCleanTitle() {
        $this->assertEqual('<b>Hello</b> http://google.com world<script>', Category::cleanTitle('  <b>Hello</b> http://google.com world<script>  '));
        $this->assertEqual('Untitled Category', Category::cleanTitle('  '));
    }

    public function testBuildCategories() {
        $this->assertEqual('', $this->toString(Category::findAll()));
        Category::buildCategories(array());

        Category::buildCategories(array(
                array('title' => 'red', 'description' => 'Ruddy things', 'membersCanAddTopics' => true, 'membersCanReply' => false, 'alternativeIds' => 'null'),
                array('title' => 'blue', 'description' => 'Moody things', 'membersCanAddTopics' => false, 'membersCanReply' => true)));
        $categories = Category::findAll();
        list($red, $blue) = $categories;
        $this->assertEqual("{$red->id},1,red,Ruddy things,Y,Y,null {$red->id};{$blue->id},2,blue,Moody things,N,Y,{$blue->id}", $this->toString($categories));

        Category::buildCategories(array(
                array('title' => 'green', 'description' => 'Envious things', 'membersCanAddTopics' => true, 'membersCanReply' => true, 'alternativeIds' => 'null'),
                array('id' => $red->id, 'title' => 'rose', 'description' => 'Rosy things', 'membersCanAddTopics' => false, 'membersCanReply' => false)));
        $categories = Category::findAll();
        $green = $categories[0];
        $this->assertNotEqual($green->id, $red->id);
        $this->assertNotEqual($green->id, $blue->id);
        $this->assertTrue($green);
        $this->assertEqual("{$green->id},1,green,Envious things,Y,Y,null {$green->id};{$red->id},2,rose,Rosy things,N,N,{$red->id}", $this->toString($categories));
        $this->assertEqual(1, count(Category::findAll(false)));

        Category::buildCategories(array());
        $this->assertEqual('', $this->toString(Category::findAll()));
    }

    public function testRecentTopics() {
        list($category) = Category::buildCategories(array(
                array('title' => 'Pink', 'description' => 'Pink materials', 'membersCanAddTopics' => true, 'membersCanReply' => true)));
        $this->assertEqual(0, count(Category::recentTopics($category)));
        $topic = Topic::create('Pink boxes', 'Rhombuses with a rosy hue.');
        $topic->my->categoryId = $category->id;
        $topic->save();
        Category::invalidateRecentTopicsCache($category);
        $this->assertEqual(1, count(Category::recentTopics($category)));
    }

    public function testRecentTopicsInvalidationKey() {
        $this->assertEqual('recent-topics-foo', TestCategory::recentTopicsInvalidationKey('foo'));
    }

    public function testAddCategoryFilter() {
        list($A, $B, $C) = Category::buildCategories(array(array('A'), array('B'), array('C')));
        $topic1 = Topic::create('test', 'test');
        $topic1->save();
        $topic2 = Topic::create('test', 'test');
        $topic2->my->categoryId = "{$A->id}";
        $topic2->save();
        $topic3 = Topic::create('test', 'test');
        $topic3->my->categoryId = "{$B->id}";
        $topic3->save();
        $category = Category::create();
        $category->my->alternativeIds = "{$C->id}";
        $this->assertEqual(0, count(Category::addCategoryFilter(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Topic')->filter('my.test', '=', 'Y'), $category)->execute()));
        $category->my->alternativeIds = "null";
        $this->assertEqual(1, count(Category::addCategoryFilter(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Topic')->filter('my.test', '=', 'Y'), $category)->execute()));
        $category->my->alternativeIds = "null {$A->id}";
        $this->assertEqual(2, count(Category::addCategoryFilter(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Topic')->filter('my.test', '=', 'Y'), $category)->execute()));
        $category->my->alternativeIds = "null {$A->id} {$B->id}";
        $this->assertEqual(3, count(Category::addCategoryFilter(XN_Query::create('Content')->filter('owner')->filter('type', '=', 'Topic')->filter('my.test', '=', 'Y'), $category)->execute()));
    }

    public function testFind() {
        list($category) = Category::buildCategories(array(array('Announcements')));
        $this->assertNotNull(Category::findById($category->id));
        $this->assertNull(Category::findById(null));
        $category->my->alternativeIds .= ' null';
        $category->save();
        $this->assertNotNull(Category::findById($category->id));
        $this->assertNotNull(Category::findById(null));
    }

    private function toString($categories) {
        $categoryStrings = array();
        foreach ($categories as $category) {
            $categoryStrings[] = $category->id . ',' . $category->my->order . ',' . $category->title . ',' . $category->description . ',' . $category->my->membersCanAddTopics . ',' . $category->my->membersCanReply . ',' . $category->my->alternativeIds;
        }
        return implode(';', $categoryStrings);
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

    public function testCategoriesForTitlesAndIds() {
        $this->assertNull(TestCategory::getCategoriesForTitlesAndIds());
        $this->assertTrue(is_array(TestCategory::categoriesForTitlesAndIds()));
        $this->assertTrue(is_array(TestCategory::getCategoriesForTitlesAndIds()));
    }

    public function testCategoriesForTitlesAndIds2() {
        TestCategory::setCategoriesForTitlesAndIds('foo');
        $this->assertEqual('foo', TestCategory::getCategoriesForTitlesAndIds());
        $this->assertEqual('foo', TestCategory::categoriesForTitlesAndIds());
        $this->assertEqual('foo', TestCategory::getCategoriesForTitlesAndIds());
    }
}

class TestCategory extends Category {
    public static function recentTopicsInvalidationKey($categoryId) {
        return parent::recentTopicsInvalidationKey($categoryId);
    }
    public static function setCategoriesForTitlesAndIds($categoriesForTitlesAndIds) {
        parent::$categoriesForTitlesAndIds = $categoriesForTitlesAndIds;
    }
    public static function getCategoriesForTitlesAndIds() {
        return parent::$categoriesForTitlesAndIds;
    }
    public static function categoriesForTitlesAndIds() {
        return parent::categoriesForTitlesAndIds();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
