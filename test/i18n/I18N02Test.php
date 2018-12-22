<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageCatalogReader.php');
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageCatalogWriter.php');

class I18N02Test extends UnitTestCase {

    public function testInvalidCharacters() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*en_US.*') as $filename) {
            if (strpos($filename, '/backups') !== false) { continue; }
            $lineNumber = 0;
            foreach (explode("\n", file_get_contents($filename)) as $line) {
                $lineNumber++;
                if (preg_match('@[^ -~…’»©–—←«→\t]@ui', $line, $matches)) {
                    echo 'Invalid character: ' . $matches[0] . ' (' . ord($matches[0]) . ') – ' . $line;
                    $this->assertFalse(true, $filename . ' ' . $lineNumber . ' ***');
                }
            }
        }
    }

    public function testNoPrivateFunctionsInPhpCatalogs() {
        // The centralized PHP message catalogs should contain no private functions.
        // Make them protected, so that decentralized catalogs (which are subclasses) can access them. [Jon Aquino 2007-08-08]
        foreach(XG_TestHelper::globr(NF_APP_BASE, 'XG_MessageCatalog_*.php') as $filename) {
            $this->assertEqual(0, preg_match('@private.*function@i', file_get_contents($filename)), $filename);
        }
    }

    public function testNoDollarSInPhpCatalogs() {
        // $s should be %s [Jon Aquino 2008-01-24]
        $contents = file_get_contents(NF_APP_BASE . '/lib/XG_MessageCatalog_en_US.php');
        preg_match_all('@[^\d]\$s\b@i', Index_MessageCatalogReader::extractPhpArray($contents), $matches);
        foreach ($matches[0] as $match) {
            $this->fail($match);
        }
    }

    public function testSearchMembersShouldNotHaveColon() {
        // Should not have a colon because this text is now in a button [Jon Aquino 2007-07-27]
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib', 'XG_MessageCatalog*.*') as $filename) {
            $this->assertFalse(preg_match('@SEARCH_MEMBERS.*:@', file_get_contents($filename)), $filename);
        }
    }

    public function testMessageNamesValid() {
        XG_App::includeFileOnce('/lib/XG_MessageCatalog_en_US.php');
        foreach (XG_MessageCatalog_en_US::getMessagesForTesting() as $name => $value) {
            $this->assertTrue(preg_match(Index_MessageCatalogWriter::PHP_MESSAGE_CATALOG_NAME_PATTERN, $name), $name);
        }
    }

    public function testFunctionsShouldNotBePrivate() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib', 'XG_MessageCatalog*.*') as $filename) {
            foreach (explode("\n", file_get_contents($filename)) as $line) {
                $this->assertFalse(preg_match('@private.*function@', $line), $filename . ' - ' . $line . ' - functions should not be private; otherwise subclasses (like decentralized catalogs) will not be able to call them');
            }
        }
    }

    /**
     * Finds text that has not been internationalized – not all of it, but a good deal of it
     * in the template files.
     */
    public function testUninternationalizedTemplateText() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $filename) {
            if (strpos($filename, 'templates') === false) { continue; }
            if (strpos($filename, 'termsOfService.php') !== false) { continue; }
            if (strpos($filename, 'fixAvatars.php') !== false) { continue; }
            if (strpos($filename, 'notes/templates/embed/fragment_block.php') !== false) { continue; }
            if (strpos($filename, 'testMessages.php') !== false) { continue; }
            if (strpos($filename, 'privacyPolicy.php') !== false) { continue; }
            // Skip files that are no longer used [Jon Aquino 2007-08-25]
            if (strpos($filename, '/music/templates/rating/updateFromPlayer.php') !== false) { continue; }
            if (strpos($filename, '/photo/templates/tag/fragment_list.php') !== false) { continue; }
            if (strpos($filename, '/profiles/templates/index/index.php') !== false) { continue; }
            if (strpos($filename, '/admin/templates/index/grid.php') !== false) { continue; }
            if (strpos($filename, '/admin/templates/index/index.php') !== false) { continue; }
            if (strpos($filename, '/admin/templates/index/manualSortAndSearchUpdate.php') !== false) { continue; }
            $contents = file_get_contents($filename);
            $lenient = false;
            // Be a little lenient toward files created before this test (2007-08-24) [Jon Aquino 2007-08-24]
            if (strpos($filename, '/html/templates/embed/widgets.php') !== false) { $lenient = true; }
            if (strpos($filename, '/activity/templates/log/fragment_logItem.php') !== false) { $lenient = true; }
            if (strpos($filename, '/forum/templates/_shared/fragment_topic.php') !== false) { $lenient = true; }
            if (strpos($filename, '/forum/templates/topic/fragment_attachments.php') !== false) { $lenient = true; }
            if (strpos($filename, '/forum/templates/topic/list.php') !== false) { $lenient = true; }
            if (strpos($filename, '/forum/templates/topic/show.php') !== false) { $lenient = true; }
            if (strpos($filename, '/forum/templates/user/list.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/activity/edit.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/embed/footer.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/embed/sidebarUserBox.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/embeddable/edit.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/embed/_networkCreator.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/embeddable/list.php') !== false) { $lenient = true; }
            if (strpos($filename, '/widgets/index/templates/facebook/instructions.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/facebook/postInstructions.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/feature/add.php') !== false) { $lenient = true; }
            if (strpos($filename, '/widgets/index/templates/index/signIn.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/index/signUp.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/membership/tabs.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/search/content.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/search/fragment_pagination.php') !== false) { $lenient = true; }
            if (strpos($filename, '/index/templates/sharing/shareSignedOut.php') !== false) { $lenient = true; }
            if (strpos($filename, '/music/templates/index/error.php') !== false) { $lenient = true; }
            if (strpos($filename, '/groups/templates/index/error.php') !== false) { $lenient = true; }
            if (strpos($filename, '/music/templates/playlist/edit.php') !== false) { $lenient = true; }
            if (strpos($filename, '/music/templates/playlist/show.php') !== false) { $lenient = true; }
            if (strpos($filename, '/music/templates/rating/updateFromPlayer.php') !== false) { $lenient = true; }
            if (strpos($filename, '/music/templates/track/editMultiple.php') !== false) { $lenient = true; }
            if (strpos($filename, '/page/templates/page/fragment_attachments.php') !== false) { $lenient = true; }
            if (strpos($filename, '/page/templates/page/fragment_comment.php') !== false) { $lenient = true; }
            if (strpos($filename, '/page/templates/page/fragment_commentForm.php') !== false) { $lenient = true; }
            if (strpos($filename, '/page/templates/page/fragment_page.php') !== false) { $lenient = true; }
            if (strpos($filename, '/page/templates/user/list.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/album/fragment_editForm.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/comment/fragment_comment.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/index/error.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/photo/editMultiple.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/photo/fragment_grid_ncolumns.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/photo/fragment_pagination.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/photo/slideshow.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/photo/slideshowFeed.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/tag/fragment_list.php') !== false) { $lenient = true; }
            if (strpos($filename, '/photo/templates/user/fragment_list.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/appearance/edit.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/blog/fragment_pagination.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/blog/managePosts.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/blog/new.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/blog/showProper.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/embed/embed1smallbadge.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/embed/fragment_blogposts_body.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/index/index.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/profile/edit.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/profile/emailSettings.php') !== false) { $lenient = true; }
            if (strpos($filename, '/profiles/templates/profile/privacySettings.php') !== false) { $lenient = true; }
            if (strpos($filename, '/video/templates/comment/fragment_comment.php') !== false) { $lenient = true; }
            if (strpos($filename, '/video/templates/embed/fragment_moduleBodyAndFooter.php') !== false) { $lenient = true; }
            if (strpos($filename, '/video/templates/index/error.php') !== false) { $lenient = true; }
            if (strpos($filename, '/video/templates/user/fragment_list.php') !== false) { $lenient = true; }
            if (strpos($filename, '/video/templates/video/edit.php') !== false) { $lenient = true; }
            if (strpos($filename, '/video/templates/video/show.php') !== false) { $lenient = true; }
            if ($lenient) {
                $contents = preg_replace('@\.|,|&#187;|&#171;|\(|\)|\'|\||:|&larr;|1\.|2\.|3\.|&gt;|!@u', '', $contents);
            }
            $contents = preg_replace('@<\?xml.*?\?>@su', '', $contents);
            $contents = preg_replace('@<\?php.*?\?>@su', '', $contents);
            $contents = preg_replace('@<%.*?%>@su', '', $contents);
            $contents = preg_replace('@<style.*?</style>@su', '', $contents);
            $contents = preg_replace('@<script.*?</script>@su', '', $contents);
            if (basename($filename) == '_networkCreator.php') { $contents = preg_replace('@Last updated.*? in@su', '', $contents); }
            if (basename($filename) == 'fragment_moduleBodyAndFooter.php') { $contents = str_replace('class="xg_osskin_link">link</a>', '', $contents); }
            if (basename($filename) == 'fragment_moduleBodyAndFooter.php') { $contents = str_replace('class="xg_osskin_text">text</span>', '', $contents); }
            if (basename($filename) == 'header_iphone.php') { $contents = str_replace('+', '', $contents); }
            if (basename($filename) == 'footer_iphone.php' || basename($filename) == 'show_iphone.php') { $contents = str_replace('X', '', $contents); }
            if (mb_strpos($filename, 'message/fragment_listBody.php') !== FALSE) { $contents = str_replace(',', '', $contents); }
            if (mb_strpos($filename, 'message/friendList.php') !== FALSE) { $contents = str_replace(',', '', $contents); }
            if (mb_strpos($filename, 'profiles/templates/message/list.php') !== FALSE) { $contents = str_replace(',', '', $contents); }
            $contents = str_replace('http//appsfacebookcom/', '', $contents);
            $contents = str_replace('http//', '', $contents);
            $contents = str_replace('&ndash;', '', $contents);
            $contents = str_replace('—', '', $contents);
            $contents = str_replace('|', '', $contents);
            $contents = str_replace('&nbsp;', '', $contents);
            $contents = str_replace('&#160;', '', $contents);
            $contents = str_replace('/profile/', '', $contents);
            $contents = str_replace('/xn/detail/', '', $contents);
            $contents = str_replace('Hello from parent!', '', $contents);
            $contents = str_replace('&#9654;', '', $contents);
            $contents = str_replace('&#9650;', '', $contents);
            $contents = str_replace('()', '', $contents);
            $contents = str_replace('&#9658;', '', $contents);
            $contents = str_replace('&#9660;', '', $contents);
            $contents = str_replace('&#8212;', '', $contents);
            $contents = str_replace('&#8211;', '', $contents);
            $contents = str_replace('&#9668;', '', $contents);
            $contents = str_replace('&bull;', '', $contents);
            $contents = str_replace('<span class="invite-more">&#8211;', '', $contents);
            $contents = str_replace('&lt;', '', $contents);
            $contents = str_replace('&gt;', '', $contents);
            $contents = str_replace('&shy;<wbr />/', '', $contents);
            $contents = str_replace('&#64;', '', $contents);
            $contents = str_replace('THEME NAME (Customized)', '', $contents);
            $contents = str_replace('THEME NAME Customized', '', $contents);
            $contents = str_replace('http://', '', $contents);
            $contents = str_replace('*/ ?>', '', $contents);
            $contents = str_replace('&#8211;', '', $contents);
            $contents = trim(strip_tags($contents));
            if (basename($filename) == 'fragment_composeRecipients.php') {
                // after stripping the html tags, remove '(' and ')'
                $contents = str_replace('(', '', $contents);
                $contents = str_replace(')', '', $contents);
            }
            $this->assertEqual('', $contents, $contents . ' in ' . $filename);
        }
    }

    public function testKeepAttributesOutOfI18nMessages() {
        // Skip problems that existed prior to the creation of this test (2007-08-27)  [Jon Aquino 2007-08-27]
        $oldProblemLines = array(
                '$a[\'N_COMMENTS\'][\'1\'] = \'<span class="comment-count">1</span> Comment\';',
                '$a[\'N_COMMENTS\'][\'n\'] = \'<span class="comment-count">%s</span> Comments\';',
                '\'GET_STARTED_BY_IMPORT\' => \'We\\\'ll automatically get your titles, tags and any mapping information you have.<br/>How long will it take?</p> <p><ul style="padding-top:10px;"><li>10 Photos: <em>less than 30 seconds</em>.</li><li>50 Photos: <em>about 2 minutes</em>.</li><li>200 Photos: <em>about 6 minutes</em>.</li><li>500 photos: <em>about 15 minutes</em>.</li></ul>\',',
                '\'NEW_FEATURES_GROUPS_MUSIC\' => \'<h3 style="margin-top:0">New Features!</h3>',
                '<p><a href="%s" class="desc add">Click here to add features</a></p>\',',
                '\'ADD_A_VIDEO_MORE_OPTIONS\' => \'Add a video to your website or MySpace page. Looking for more options? <a href="%s">View all videos</a> on %s.\',',
                '\'IF_YOU_WANT_TO_ALLOW_USERNAME_APPNAME_LINK\' => \'If you want to allow %s back on %s, <a href="%s">click here to go to banned people page</a> to remove the ban.\',',
                '\'SKIP_FEATURES\' => \'Click <a href="%s">Skip This Step</a> to continue with the default layout and features.\',',
                '\'LOREM_IPSUM_3\' => \'Lorem ipsum dolor sit amet, <span class="preview_link">consectetuer</span> adipiscing\',',
                '\'CUSTOMIZE_THE_APPEARANCE_NETWORK\' => \'Customize the appearance of your social network by adding your own Cascading Style Sheets (CSS) in the field on the left. (<a href="%s">What is CSS?</a>)\',',
                '\'CUSTOMIZE_THE_APPEARANCE_PAGE\' => \'Customize the appearance of your page by adding your own Cascading Style Sheets (CSS) in the field on the left. (<a href="%s">What is CSS?</a>)\',',
                '\'NEED_HELP_FIGURING_OUT_CSS\' => \'Need help figuring out the name of a specific CSS class on your network? We recommend the "Inspect" feature of the free <a href="%s">Firebug</a> extension (for the Firefox browser).\',',
                '\'X_ALREADY_REGISTERED_Y_TO_RESET\' => \'There is already a Ning ID registered with the email address %s. Please sign in below with your Ning ID or email address. If you\\\'ve forgotten your password, <a href="%s">click here to reset your password</a>.\',',
                '\'YOUR_PRIVACY_IS_IMPORTANT' => 'Your privacy is important to us. If you have any questions on your email notifications, please send us a note via the <a href="http://help.ning.com/?page_id=27">Help Center</a>.\',',
                'addAWidget: function(url) { return \'<a href="\' + url + \'">Add a widget</a> to this textbox\'; }',
                '\'NEW_FEATURES_ACTIVITY_BADGES\' => \'<h3 style="margin-top:0">New Features!</h3>',
                '<p><a href="%s" class="desc add">Click here to add/edit features</a></p>\',',
                'importingNofMPhotos: function(n,m) { return \'Importing <span id="currentP">\' + n + \'</span> of \' + m + \' photos.\'},');
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*en_US.*') as $filename) {
            if (strpos($filename, '/backups') !== false) { continue; }
            foreach (explode("\n", file_get_contents($filename)) as $line) {
                $line = str_replace('value="Sign In"', '', $line);
                $line = str_replace('href="#"', '', $line);
                if (in_array(trim($line), $oldProblemLines)) { continue; }
                if (strpos($line, 'HaveTheRight:') !== false) { continue; }
                if (strpos($line, 'THERE_ARE_X_LINK_VIDEOS_ON_APPNAME') !== false) { continue; }
                if (strpos($line, 'THERE_ARE_X_LINK_EVENTS_ON_APPNAME') !== false) { continue; }
                if (strpos($line, 'THERE_ARE_X_LINK_PHOTOS_ON_APPNAME') !== false) { continue; }
                if (strpos($line, 'will be replaced with <a href=""></a>') !== false) { continue; }
                if (strpos($line, 'X_NEW_LINK_MEMBERS_JOINED_PAST_WEEK') !== false) { continue; }
                if (strpos($line, 'THERE_ARE_X_LINK_MEMBERS_ON_APPNAME') !== false) { continue; }
                if (strpos($line, 'THERE_ARE_X_LINK_FORUM_TOPIC_ON_APPNAME') !== false) { continue; }
                if (strpos($line, 'THERE_ARE_X_LINK_GROUPS_ON_APPNAME') !== false) { continue; }
                if (strpos($line, 'YOUR_PRIVACY_IS_IMPORTANT') !== false) { continue; }
                $this->assertFalse(preg_match('@([a-z]+=)["\']@ui', $line, $matches), $matches[1] . '... in ' . $line . ' - Keep attributes out of the message catalog - pass them in instead (e.g., <a %s>%s</a>).');
            }
        }
    }

    public function testKeepAttributesOutOfI18nMessages2() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, 'XG_MessageCatalog_*.*') as $filename) {
            preg_match_all('@
                    APPNAME_HAS_NEW_PROFILE_QUESTIONS.*="
                    |APPNAME_NOW_HAS_.*="
                    |YOUVE_TURNED_OFF_THE_ACTIVITY_DISPLAY.*="
                    @x', file_get_contents($filename), $matches);
            foreach ($matches[0] as $match) {
                $this->assertTrue(false, $match . ' in ' . basename($filename));
            }
        }
    }

    public function testEnUsSpelling() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*en_US.*') as $filename) {
            if (strpos($filename, '/backups') !== false) { continue; }
            $contents = strtolower(file_get_contents($filename));
            foreach(array('coment') as $mistake) {
                $this->assertTrue(strpos($contents, $mistake) === false, $mistake . ' - ' . basename($filename));
            }
        }
    }

    public function testUseXgAbstractMessageCatalog() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib', 'XG_MessageCatalog_*.*') as $filename) {
            if (strpos($filename, '/backups') !== false) { continue; }
            $contents = file_get_contents($filename);
            $this->assertTrue(strpos($contents, 'extends XG_AbstractMessageCatalog') !== false, $filename);
            $this->assertTrue(strpos($contents, "XG_App::includeFileOnce('/lib/XG_AbstractMessageCatalog.php');") !== false, $filename);
        }
    }

    public function testValidateMessageCatalogs() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib', 'XG_MessageCatalog_*.*') as $filename) {
            if (strpos($filename, '/backups') !== false) { continue; }
            preg_match('@XG_MessageCatalog_(.._..).php@', $filename, $matches);
            $locale = $matches[1];
            $reader = new Index_MessageCatalogReader();
            $reader->read(file_get_contents($filename));
            Index_LanguageHelper::validate($locale, $reader->getData());
        }
    }

    public function testStopwordsSeparatedBySpacesBaz4376() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib', 'XG_MessageCatalog_*.*') as $filename) {
            if (strpos($filename, '/backups') !== false) { continue; }
            $contents = file_get_contents($filename);
            $this->assertFalse(preg_match('@STOPWORDS.*\w\|\w@u', $contents), $filename);
        }
    }

    public function testCallUserFuncArrayIgnoresWarningsBaz4377() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/lib', 'XG_MessageCatalog_*.*') as $filename) {
            if (strpos($filename, '/backups') !== false) { continue; }
            $contents = file_get_contents($filename);
            $this->assertFalse(preg_match('/[^@]call_user_func_array/u', $contents), $filename);
        }
    }

    public function testAltTextInternationalized() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'php') === false && strpos($file, 'js') === false) { continue; }
            $contents = file_get_contents($file);
            preg_match_all('@alt="[a-z0-9][^"]+"@ui', $contents, $matches);
            foreach ($matches[0] as $match) {
                if (strpos($match, 'yourminis.com') !== false) { continue; }
                if (strpos($match, 'clearspring.com') !== false) { continue; }
                if (strpos($match, 'Google Gadgets') !== false) { continue; }
                if (strpos($match, 'Microsoft Gadgets') !== false) { continue; }
                if (strpos($match, 'eBay To Go') !== false) { continue; }
                if (strpos($match, 'Digg Tools') !== false) { continue; }
                if (strpos($match, 'Widgetbox') !== false) { continue; }
                if (strpos($match, 'Clearspring') !== false) { continue; }
                if (strpos($match, 'Snipperoo') !== false) { continue; }
                if (strpos($match, 'Spring Widgets') !== false) { continue; }
                if (strpos($match, 'ustream.tv') !== false) { continue; }
                if (strpos($match, 'EyeJot') !== false) { continue; }
                if (strpos($match, 'Flickr') !== false) { continue; }
                if (strpos($match, 'YouTube') !== false) { continue; }
                if (strpos($match, 'Google') !== false) { continue; }
                $this->assertFalse(true, $match . ' - ' . $file);
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
