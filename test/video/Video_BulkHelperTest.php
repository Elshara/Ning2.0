<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/BulkHelperTestCase.php');
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_BulkHelper.php');

class Video_BulkHelperTest extends BulkHelperTestCase {

    public function setUp() {
        XG_TestHelper::setCurrentWidget('video');
    }

    public function testSetPrivacy() {
        $video = $this->createVideo();
        $videoId = $video->id;
        $videoPreviewFrameId = $this->createVideoPreviewFrame($video);
        $videoAttachmentId = $this->createVideoAttachment($video);

        Video_BulkHelper::setPrivacy(30, false);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);

        $video = XN_Content::load($videoId);
        $videoPreviewFrame = XN_Content::load($videoPreviewFrameId);
        $videoAttachment = XN_Content::load($videoAttachmentId);
        $this->assertEqual(false, $video->isPrivate);
        $this->assertEqual(false, $videoPreviewFrame->isPrivate);
        $this->assertEqual(false, $videoAttachment->isPrivate);

        Video_BulkHelper::setPrivacy(30, true);
        XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);

        $this->checkPrivacy(true, array($videoId, $videoPreviewFrameId, $videoAttachmentId));
    }

    /**
     * Create a dummy video, save it to the content store, return the video object.
     *
     * @return  Video   Video object that has been created.
     */
    private function createVideo() {
        $video = Video::create();
        $video->isPrivate = true;
        $video->my->mozzle = 'video';
        $video->my->approved = 'Y';
        $video->save();
        return $video;
    }

    /**
     * Create a dummy VideoPreviewFrame attached to the specified Video and return the Id.
     *
     * @param   $video   Video  Video to attach the VideoPreviewFrame to.
     * @return  string          ID of the VideoPreviewFrame created.
     */
    private function createVideoPreviewFrame($video) {
        $previewFrame = W_Content::create('VideoPreviewFrame');
        $previewFrame->my->mozzle = W_Cache::current('W_Widget')->dir;
        $previewFrame->my->video = $video->id;
        $previewFrame->isPrivate = $video->isPrivate;
        $previewFrame->set('data', null, XN_Attribute::UPLOADEDFILE);
        $previewFrame->save();
        return $previewFrame->id;
    }

    /**
     * Create a dummy VideoAttachment attached to the specified Video and return the Id.
     *
     * @param   $video   Video  Video to attach the VideoAttachment to.
     * @return  string          ID of the VideoAttachment created.
     */
    private function createVideoAttachment($video) {
        $videoAttachment = W_Content::create('VideoAttachment');
        $videoAttachment->my->mozzle = W_Cache::current('W_Widget')->dir;
        $videoAttachment->title = 'video_attachment_test';
        $videoAttachment->my->video = $video->id;
        $videoAttachment->isPrivate = $video->isPrivate;
        $videoAttachment->my->isSource = 'N';
        $videoAttachment->set('data', null, XN_Attribute::UPLOADEDFILE);
        $videoAttachment->save();
        return $videoAttachment->id;
    }

    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
