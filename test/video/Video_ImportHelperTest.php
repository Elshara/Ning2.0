<?php   # $Id: $
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_ImportHelper.php');

class Video_ImportHelperTest extends UnitTestCase {
    // video with enabled embedding
    public function testYoutubePublic () { # void
        $result = Video_ImportHelper::parseVideoUrl('http://www.youtube.com/watch?v=h9Qe0OnjeXg');
        $this->assertTrue(is_array($result));
        $this->assertEqual($result['title'], 'Google Code Review - June 20th, 2008');
        $this->assertEqual($result['description'], 'The Google Code Review is a regular news show that goes over the recent escapades in code.google.com land and beyond.');
        $this->assertEqual($result['tags'], array('google', 'googlecode', 'codereview', 'technology', 'webdevelopment'));
        $this->assertPattern('/<object.*h9Qe0OnjeXg/', $result['embedCode']);
    }
    
    // video with html entities in the title and description
    public function testYoutubeEntities () {
        $result = Video_ImportHelper::parseVideoUrl('http://www.youtube.com/watch?v=MGAQIpd-row');
        $this->assertTrue(is_array($result));
        $this->assertEqual($result['title'], 'Cassie - Me & you');
        $this->assertEqual($result['description'], 'Cassie - Me & you');
        $this->assertEqual($result['tags'], array('Me', 'and', 'you', 'Cassie'));
        $this->assertPattern('/<object.*MGAQIpd-row/', $result['embedCode']); 
    }

    // video with disabled embedding
    public function testYoutubePrivate () { # void
        $result = Video_ImportHelper::parseVideoUrl('http://www.youtube.com/watch?v=bWO09ayQ0Gg');
        $this->assertTrue(is_array($result));
        $this->assertEqual($result['title'], 'Learn How to Speak Spanish : Common Spanish Phrases for Food: Learn Spanish: Free Online Video Lesson');
        $this->assertEqual($result['description'], 'Learn how to speak Spanish with common phrases for food in this free online video lesson.');
        $this->assertEqual($result['tags'], array('spanish', 'learn', 'language', 'how', 'to', 'speak', 'online', 'video', 'class', 'course', 'espanol'));
        $this->assertTrue(!isset($result['embedCode']));
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
