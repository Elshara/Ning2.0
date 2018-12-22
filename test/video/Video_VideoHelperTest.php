<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/video/lib/helpers/Video_VideoHelper.php');
XG_App::includeFileOnce('/lib/XG_TagHelper.php');
XG_App::includeFileOnce('/widgets/video/models/Video.php');

class Video_VideoHelperTest extends UnitTestCase {

    public function testBaz4056() {
        $this->assertTrue(is_array(Video_VideoHelper::embedPreviewFrameUrlAndMimeType('<object type="application/x-shockwave-flash" data="http://blip.tv/ scripts/flash/showplayer.swf?file=http%3A%2F%2Ftechtrek%2Eblip%2Etv%2Frss%2Fflash%2Feie%3Fnsfw%3Ddc&showplayerpath=http%3A%2F%2Fblip%2Etv%2Fscripts%2Fflash%2Fshowplayer%2Eswf" width="550" height="350" allowfullscreen="true" id="showplayer"><param name="movie" value="http://blip.tv/scripts/flash/showplayer.swf?file=http%3A%2F%2Ftechtrek%2Eblip%2Etv%2Frss%2Fflash%2F%3Fnsfw%3Ddc&showplayerpath=http%3A%2F%2Fblip%2Etv%2Fscripts%2Fflash%2Fshowplayer%2Eswf" /><param name="quality" value="best" /></object>')));
        $this->assertTrue(is_array(Video_VideoHelper::embedPreviewFrameUrlAndMimeType('<object type="application/x-shockwave-flash" data="http://terraadmin.blip.tv/scripts/flash/showplayer.swf?autostart=true&enablejs=true&feedurl=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss&file=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss%2Fflash%2F320807&showplayerpath=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Fscripts%2Fflash%2Fshowplayer%2Eswf" width="680" height="412" allowfullscreen="true" id="showplayer"><param name="movie" value="http://terraadmin.blip.tv/scripts/flash/showplayer.swf?autostart=true&enablejs=true&feedurl=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss&file=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Frss%2Fflash%2F320807&showplayerpath=http%3A%2F%2Fterraadmin%2Eblip%2Etv%2Fscripts%2Fflash%2Fshowplayer%2Eswf" /><param name="quality" value="best" /></object>')));
        $this->assertTrue(is_array(Video_VideoHelper::embedPreviewFrameUrlAndMimeType('<embed wmode="transparent" src="http://blip.tv/scripts/flash/blipplayer.swf?autoStart=false&file=http://blip.tv/file/get/Greentime-Episode14HanginOut436.flv%3Fsource%3D3" quality="high" width="320" height="240" name="movie" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>')));
        $this->assertTrue(is_array(Video_VideoHelper::embedPreviewFrameUrlAndMimeType("<embed class='castfire_player' id='cf_2259' name='cf_2259' width='640' height='520' src='http://p.castfire.com/1P48R/video/2259/aanq_2007-09-18-212531.flv' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'&gt;&lt;/embed>")));
        $this->assertTrue(is_array(Video_VideoHelper::embedPreviewFrameUrlAndMimeType("<embed class='castfire_player' id='cf_2048' name='cf_2048' width='640' height='520' src='http://p.castfire.com/1P48R/video/2048/aanq_2007-08-29-230055.flv' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'&gt;&lt;/embed>")));
    }

    public function testHasVideoExtension() {
        $this->assertFalse(Video_VideoHelper::hasVideoExtension('foo.txt'));
        $this->assertTrue(Video_VideoHelper::hasVideoExtension('foo.mp4'));
        $this->assertFalse(Video_VideoHelper::hasVideoExtension('c:\\foo.txt'));
        $this->assertTrue(Video_VideoHelper::hasVideoExtension('c:\\foo.mp4'));
        $this->assertFalse(Video_VideoHelper::hasVideoExtension('/usr/foo.txt'));
        $this->assertTrue(Video_VideoHelper::hasVideoExtension('/usr/foo.mp4'));
    }


    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
