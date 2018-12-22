<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class I18N04CmdlineTest extends CmdlineTestCase {

    public function testInvalidCharacters() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/xn_resources/widgets/shared/js/messagecatalogs', '*.js') as $filename) {
            $contents = self::getFileContent($filename);
            $this->assertTrue(mb_strpos($contents, '‚Äî') === false, '‚Äî in ' . $filename);
            $this->assertTrue(mb_strpos($contents, '¬') === false, '¬ in ' . $filename);
        }
    }

    public function testUseUtf8CharactersInsteadOfHtmlEntities() {
        $messages = array();
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*en_US*') as $file) {
            if (strpos($file, 'xn_volatile/backups') !== false) { continue; }
            $contents = self::getFileContent($file);
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (! preg_match('@&[^ ]+;@u', $line) !== FALSE) { continue; }
                // Be lenient toward existing violations [Jon Aquino 2008-06-10]
                if (mb_strpos($line, 'Use UTF-8') !== FALSE) { continue; }
                if (mb_strpos($line, "'&nbsp;('") !== FALSE) { continue; }
                if (mb_strpos($line, "'MEMBERS_AND_FRIENDS_ADDED_THIS_FEATURE'") !== FALSE) { continue; }
                if (mb_strpos($line, "'TO_ADD_THE_WIDGET_GET_AND_SHARE'") !== FALSE) { continue; }
                if (mb_strpos($line, "'BY_SIGNING_UP_YOU_AGREE'") !== FALSE) { continue; }
                if (mb_strpos($line, "'BY_SIGNING_IN_YOU_AGREE_AMENDED'") !== FALSE) { continue; }
                if (mb_strpos($line, "'SIGN_IN_OR_SIGN_UP'") !== FALSE) { continue; }
                if (mb_strpos($line, "'COPYRIGHT_CREATED_BY'") !== FALSE) { continue; }
                if (mb_strpos($line, "'N_MEMBERS_AND_N_FRIENDS_ADDED_THIS_FEATURE'") !== FALSE) { continue; }
                if (mb_strpos($line, "'COPYRIGHT_CREATED_BY_WITH_NING'") !== FALSE) { continue; }
                if (mb_strpos($line, "'FLICKR_STEP1_B'") !== FALSE) { continue; }
                if (mb_strpos($line, "'YOU_CAN_SHARE_TYPE_TWO_WAYS'") !== FALSE) { continue; }
                if (mb_strpos($line, "'BACK_TO_USERNAMES_PAGE'") !== FALSE) { continue; }
                if (mb_strpos($line, "'BACK_TO_MY_PAGE'") !== FALSE) { continue; }
                if (mb_strpos($line, "'FACEBOOK_MUSIC_BUTTON'") !== FALSE) { continue; }
                if (mb_strpos($line, "'FACEBOOK_VIDEO_BUTTON'") !== FALSE) { continue; }
                if (mb_strpos($line, "'FACEBOOK_SLIDESHOW_BUTTON'") !== FALSE) { continue; }
                if (mb_strpos($line, "'FACEBOOK_INSTR_SETUP_10'") !== FALSE) { continue; }
                if (mb_strpos($line, "'FACEBOOK_URL_INFO_DESC'") !== FALSE) { continue; }
                if (mb_strpos($line, "noteExists") !== FALSE) { continue; }
                if (mb_strpos($line, "embedCodeMissingTag") !== FALSE) { continue; }
                if (mb_strpos($line, "<ul><li><strong>Groups</strong> &ndash; Let your members create groups on your network") !== FALSE) { continue; }
                if (mb_strpos($line, "<li><strong>Music Player</strong> &ndash; Add music and podcasts to your network</li></ul>") !== FALSE) { continue; }
                if (mb_strpos($line, "<ul><li><strong>Latest Activity</strong> &ndash; Follow the latest member activity on your network</li>") !== FALSE) { continue; }
                if (mb_strpos($line, "<li><strong>Badges & Widgets</strong> &ndash; Promote your network across the web with custom badges and widgets</li></ul>") !== FALSE) { continue; }
                $this->assertTrue(FALSE, $matches[0] . ' in ' . $line . ' - ' . $file . ':' . $lineNumber . ' ***');
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
