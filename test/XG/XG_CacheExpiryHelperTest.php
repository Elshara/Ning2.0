<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');

class XG_CacheExpiryHelperTest extends UnitTestCase {

    public function setUp() {
        TestCacheExpiryHelper::setTypesOfChangedPromotedObjects(array());
    }

    public function testBannedScreenNames() {
        $user = new stdClass();
        $user->my->xg_index_status = null;
        $user->title = $user->contributorName = 'PerpetuaAntioch';
        User::setStatus($user, 'blocked');

        $user->my->xg_index_status = null;
        $user->title = $user->contributorName = 'CorazonAramaic';
        User::setStatus($user, 'blocked');

        $user->my->xg_index_status = 'blocked';
        $user->title = $user->contributorName = 'JeremiahManasseh';
        User::setStatus($user, '');

        $user->my->xg_index_status = 'blocked';
        $user->title = $user->contributorName = 'MosesEphraim';
        User::setStatus($user, '');

        $this->assertEqual(array('PerpetuaAntioch' => 'PerpetuaAntioch', 'CorazonAramaic' => 'CorazonAramaic'), TestCacheExpiryHelper::getBannedScreenNames());
        $this->assertEqual(array('JeremiahManasseh' => 'JeremiahManasseh', 'MosesEphraim' => 'MosesEphraim'), TestCacheExpiryHelper::getUnbannedScreenNames());
    }

    public function testPromotedObjectsChangedCondition1() {
        XG_PromotionHelper::promote(XN_Content::create('A'));
        XG_PromotionHelper::remove(XN_Content::create('B'));
        $c = XN_Content::create('C');
        XN_Content::delete($c);
        $d = XN_Content::create('D');
        $d->my->set('xg_main_promotedOn', '2008-05-11T15:30:00Z', XN_Attribute::DATE);
        XN_Content::delete($d);
        $this->assertEqual(array('A' => 'A', 'B' => 'B', 'D' => 'D'), TestCacheExpiryHelper::getTypesOfChangedPromotedObjects());
    }

    public function testPromotedObjectsChangedCondition2() {
        $user = XN_Content::create('User');
        $user->my->set('xg_main_promotedOn', '2008-05-11T15:30:00Z', XN_Attribute::DATE);
        $user->save();
        $this->assertEqual(array(), TestCacheExpiryHelper::getTypesOfChangedPromotedObjects());
        XN_Content::delete($user);
        $this->assertEqual(array('User' => 'User'), TestCacheExpiryHelper::getTypesOfChangedPromotedObjects());
    }

    public function testPromotedObjectsChangedCondition3() {
        $user = XN_Content::create('User');
        $user->my->xg_index_status = 'blocked';
        $user->my->set('xg_main_promotedOn', '2008-05-11T15:30:00Z', XN_Attribute::DATE);
        $user->save();
        $this->assertEqual(array('User' => 'User'), TestCacheExpiryHelper::getTypesOfChangedPromotedObjects());
        XN_Content::delete($user);
    }

}

class TestCacheExpiryHelper extends XG_CacheExpiryHelper {
    public static function getBannedScreenNames() {
        return parent::$bannedScreenNames;
    }
    public static function getUnbannedScreenNames() {
        return parent::$unbannedScreenNames;
    }
    public static function getTypesOfChangedPromotedObjects() {
        return parent::$typesOfChangedPromotedObjects;
    }
    public static function setTypesOfChangedPromotedObjects($typesOfChangedPromotedObjects) {
        return parent::$typesOfChangedPromotedObjects = $typesOfChangedPromotedObjects;
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

