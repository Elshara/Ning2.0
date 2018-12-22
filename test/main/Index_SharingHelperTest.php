<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_SharingHelper.php');

/**
 * Tests for Index_SharingHelper.
 */
class Index_SharingHelperTest extends UnitTestCase {

    public function testCreateMessage() {
        $message = Index_SharingHelper::createMessage(array(), $message);
        $body = preg_replace('@xgo=.*@', '...', $message->build(null, null, false));
        // The above code is used to create the body for the mailto: link on the
        // signed-out Share This page. This test makes sure that the text is correct. [Jon Aquino 2007-10-26]
        error_log($body); // See the error log if you need to update the text for this test. [Jon Aquino 2007-10-26]
        $this->assertEqual('Check out "" on ' . XN_Application::load()->name . '

To view it, visit:


', $body);
    }

    public function testGetItemInfo() {
        $blogPost = XN_Content::create('BlogPost');
        $blogPost->title = $blogPost->description = 'This &amp; that';
        $itemInfo = TestSharingHelper::getItemInfoProper($blogPost);
        $this->assertEqual('This & that', $itemInfo['share_title']);
        $this->assertPattern('@This &amp; that@u', $itemInfo['description']);
    }
}

class TestSharingHelper extends Index_SharingHelper {
    public static function getItemInfoProper($item) {
        return parent::getItemInfoProper($item);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
