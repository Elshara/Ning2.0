<?php

class BulkHelperTestCase extends UnitTestCase {

    protected function checkPrivacy($currentlyPrivate, $ids) {
        $query = XN_Query::create("Content");
        $query->filter('owner');
        $filters = array();
        foreach ($ids as $id) {
            $filters[] = XN_Filter('id', '=', $id);
        }
        $query->filter(call_user_func_array(array('XN_Filter','any'), $filters));
        foreach ($query->execute() as $object) {
            if (in_array($object->type, XG_PrivacyHelper::$exclude)) {
                $this->assertTrue($object->isPrivate);
            } else {
                $this->assertEqual($currentlyPrivate, $object->isPrivate);
            }
        }
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}
