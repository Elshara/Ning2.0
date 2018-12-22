<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/forum/models/Topic.php');
XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_FileHelper.php');
XG_App::includeFileOnce('/lib/XG_TagHelper.php');
XG_App::includeFileOnce('/lib/XG_Cache.php');

class Forum_FileHelperTest extends UnitTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('forum');
    }

    public function testAddAttachmentProper() {
        $attachment1 = XN_Content::create('UploadedFile');
        $attachment1->isPrivate = false;
        $attachment1->save();
        $attachment2 = XN_Content::create('UploadedFile');
        $attachment2->isPrivate = false;
        $attachment2->save();
        $topic = W_Content::create('Topic');
        Forum_FileHelper::addAttachmentProper($attachment1->id, 'apple.txt', 123, $topic);
        Forum_FileHelper::addAttachmentProper($attachment2->id, 'orange.txt', 456, $topic);
        $this->assertEqual(serialize(array(array('id' => $attachment1->id, 'filename' => 'apple.txt', 'sizeInBytes' => 123), array('id' => $attachment2->id, 'filename' => 'orange.txt', 'sizeInBytes' => 456))), serialize(Forum_FileHelper::getFileAttachments($topic)));
        $attachment1Id = $attachment1->id;
        Forum_FileHelper::deleteAttachment($attachment1, $topic);
        $this->assertEqual(serialize(array(array('id' => $attachment2->id, 'filename' => 'orange.txt', 'sizeInBytes' => 456))), serialize(Forum_FileHelper::getFileAttachments($topic)));
        $this->assertFalse(self::exists($attachment1Id));
    }

    public function testDeleteAttachments() {
        $attachment1 = XN_Content::create('UploadedFile');
        $attachment1->save();
        $attachment1Id = $attachment1->id;
        $attachment2 = XN_Content::create('UploadedFile');
        $attachment2->save();
        $attachment2Id = $attachment2->id;
        $topic = W_Content::create('Topic');
        Forum_FileHelper::addAttachmentProper($attachment1->id, 'apple.txt', 123, $topic);
        Forum_FileHelper::addAttachmentProper($attachment2->id, 'orange.txt', 456, $topic);
        $this->assertEqual(serialize(array(array('id' => $attachment1->id, 'filename' => 'apple.txt', 'sizeInBytes' => 123), array('id' => $attachment2->id, 'filename' => 'orange.txt', 'sizeInBytes' => 456))), serialize(Forum_FileHelper::getFileAttachments($topic)));
        $this->assertTrue(self::exists($attachment1Id));
        $this->assertTrue(self::exists($attachment2Id));
        Forum_FileHelper::deleteAttachments($topic);
        $this->assertFalse(self::exists($attachment1Id));
        $this->assertFalse(self::exists($attachment2Id));
    }

    private function exists($id) {
        try {
            XN_Content::load($id);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
