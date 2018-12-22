<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_QueryHelper.php');

class XG_QueryHelperTest extends UnitTestCase {

    public function testSortOrder() {
        $this->assertEqual(array('createdDate', 'asc', XN_Attribute::DATE), XG_QueryHelper::sortOrder(null));
        $this->assertEqual(array('my->memberStatus', 'desc', XN_Attribute::NUMBER), XG_QueryHelper::sortOrder('status_d'));
        $fields = array('date' => array('my->dateJoined', XN_Attribute::DATE), 'status' => array('my->status', XN_Attribute::STRING));
        $this->assertEqual(array('my->dateJoined', 'desc', XN_Attribute::DATE), XG_QueryHelper::sortOrder('date_d', $fields));
        $this->assertEqual(array('my->status', 'asc', XN_Attribute::STRING), XG_QueryHelper::sortOrder('status_a', $fields));
    }

    public function testSetMaxAgeForFriendsQuery() {
        $query = XG_Query::create('Content');
        XG_QueryHelper::setMaxAgeForFriendsQuery($query);
        $this->assertPattern('@maxAge \[300\]@', $query->debugString());
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
