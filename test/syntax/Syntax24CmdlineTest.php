<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax24CmdlineTest extends CmdlineTestCase {

    public function testTrimUploadsOnSubmitIsSet() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'dojoType="BazelImagePicker"') == false) { continue; }
            $contents = str_replace("\r", '', str_replace("\n", '', $contents));
            $this->assertPattern('@(dojoType="BazelImagePicker").*(trimUploadsOnSubmit)@ui', $contents, $file);
        }
    }

    public function testDoNotEscapeEventDescription() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '/widgets/events', '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'description') == false) { continue; }
            $this->assertNoPattern('@xnhtmlentities.*description@ui', $contents, $file);
        }
    }

    public function testDoNotPutMyInFrontOfIsPrivate() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertNoPattern('@my->isPrivate@ui', $contents, $file);
        }
    }

    public function testUseStringInsteadOfUrl() {
        // XN_Attribute::URL is deprecated. Use XN_Attribute::STRING instead. [Jon Aquino 2008-04-04]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            $contents = self::getFileContent($file);
            $this->assertNoPattern('@XN_Attribute::URL@ui', $contents, $file);
        }
    }

    public function testCheckAreQueriesEnabled() {
        // addPromotedFilter() calls should be accompanied by an areQueriesEnabled()
        // check (BAZ-6713) [Jon Aquino 2008-04-05]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'XG_PromotionHelper.php') !== false) { continue; }
            if (strpos($file, 'Groups_Filter.php') !== false) { continue; }
            if (strpos($file, 'Forum_Filter.php') !== false) { continue; }
            if (strpos($file, 'Photo_Context.php') !== false) { continue; }
            if (strpos($file, 'Photo_PhotoHelper.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($file, 'Photo_AlbumHelper.php') !== false) {
                $contents = str_replace('if ($filters[\'promoted\']) { XG_PromotionHelper::addPromotedFilterToQuery($query); }', '', $contents);
            }
            if (mb_strpos($contents, 'addPromotedFilter') === false) { continue; }
            $contents = str_replace("\r", ' ', str_replace("\n", ' ', $contents));
            preg_match_all('@.{300,300}addPromotedFilter@ui', $contents, $matches);
            foreach ($matches[0] as $match) {
                $this->assertPattern('@areQueriesEnabled@ui', $file . ' ' . $match);
            }
        }
    }

    public function testAddSearchFilterShouldBeCalledWithAddExcludeFromPublicSearchFilter() {
        // Most addSearchFilter calls should be accompanied by a call to addExcludeFromPublicSearchFilter [Jon Aquino 2008-04-12]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'XG_QueryHelper.php') !== false) { continue; }
            if (strpos($file, 'Photo_AlbumHelper.php') !== false) { continue; }
            if (strpos($file, 'Photo_PhotoHelper.php') !== false) { continue; }
            if (strpos($file, 'Video_VideoHelper.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (mb_strpos($contents, 'addSearchFilter') === false) { continue; }
            $contents = str_replace("\r", '', str_replace("\n", '', $contents));
            preg_match_all('@addSearchFilter.{0,100}@', $contents, $matches);
            foreach ($matches[0] as $match) {
                $this->assertPattern('@addExcludeFromPublicSearchFilter@ui', $match, $file);
            }
        }
    }

    public function testUseRelativeUrlNotNameWithDomainSuffix() {
        // Use XN_Application::load()->relativeUrl . XN_AtomHelper::$DOMAIN_SUFFIX
        // not XN_Application::load()->name . XN_AtomHelper::$DOMAIN_SUFFIX  [Jon Aquino 2008-04-18]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'error.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'DOMAIN_SUFFIX') == false) { continue; }
            $this->assertNoPattern('@->name.*DOMAIN_SUFFIX@u', $contents, $file);
        }
    }

    public function testBaz7745() {
        // Ensure that POST <form>s have CSRF token [Jon Aquino 2008-05-27]
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if ($this->shouldSkipFile($file)) {
                continue;
            }
            $contents = self::getFileContent($file);
            if (mb_stripos($contents, '<form') === false) { continue; }
            preg_match_all('@method=.post@ui', $contents, $methodMatches);
            preg_match_all('@csrfTokenHiddenInput@ui', $contents, $csrfTokenMatches);
            $this->assertEqual(count($methodMatches[0]), count($csrfTokenMatches[0]), 'Expected ' . count($methodMatches[0]) . ', found ' . count($csrfTokenMatches[0]) . ' in ' . $file);
        }
    }

    /**
     * Returns true if the provided $file should be skipped
     *
     * @param string $file
     * @return bool
     */
    private function shouldSkipFile($file) {
        return $_SERVER['DOCUMENT_ROOT'] . '/runner.php' == $file
            || $_SERVER['DOCUMENT_ROOT'] . '/opensocial.php' == $file
            || strpos($file, 'test/') !== false
            || strpos($file, '/lib/scripts/') !== false;
    }

    public function testBaz7333() {
        $this->assertPattern('@xg_membership_no_questions_div.hidden@ui', self::getFileContent($_SERVER['DOCUMENT_ROOT'] . '/xn_resources/widgets/index/css/component.css'));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
