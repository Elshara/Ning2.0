<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_MessageCatalog_en_US.php');
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageCatalogReader.php');

class I18N03Test extends BazelTestCase {

    /**
     * @see "Capitalization in Titles", http://www.writersblock.ca/tips/monthtip/tipmar98.htm
     * @see Chicago Manual of Style
     */
    public function testTitleCase() {
        $pronouns = array('all', 'another', 'any', 'anybody', 'anyone', 'anything', 'both', 'each', 'each other', 'either', 'everybody', 'everyone', 'everything', 'few', 'he', 'her', 'hers', 'herself', 'him', 'himself', 'his', 'I', 'it', 'its', 'itself', 'little', 'many', 'me', 'mine', 'more', 'most', 'much', 'myself', 'neither', 'no one', 'nobody', 'none', 'nothing', 'one', 'one another', 'other', 'others', 'ours', 'ourselves', 'several', 'she', 'some', 'somebody', 'someone', 'something', 'that', 'theirs', 'them', 'themselves', 'these', 'they', 'those', 'us', 'we', 'what', 'whatever', 'which', 'whichever', 'who', 'whoever', 'whom', 'whomever', 'whose', 'you', 'yours', 'yourself', 'yourselves');
        $subordinateConjunctions = array('after', 'because', 'although', 'if', 'before', 'since', 'though', 'unless', 'when', 'now that', 'even though', 'only if', 'while', 'as', 'whereas', 'whether or not', 'since', 'in order that', 'while', 'even if', 'until', 'so', 'in case', 'in case that');
        $articles = array('a', 'an', 'the');
        $coordinateConjunctions = array('and', 'but', 'or', 'yet', 'for', 'nor', 'so');
        $prepositions = array('aboard', 'about', 'above', 'absent', 'according to', 'across', 'after', 'against', 'ahead of', 'all over', 'along', 'alongside', 'amid or amidst', 'among', 'around', 'as', 'as of', 'as to', 'aside', 'astride', 'at', 'away from', 'barring', 'because of', 'before', 'behind', 'below', 'beneath', 'beside', 'besides', 'between', 'beyond', 'but', 'by', 'by the time of', 'circa', 'close by', 'close to', 'concerning', 'considering', 'despite', 'down', 'due to', 'during', 'except', 'except for', 'excepting', 'excluding', 'failing', 'for', 'from', 'in', 'in between', 'in front of', 'in spite of', 'in view of', 'including', 'inside', 'instead of', 'into', 'less', 'like', 'minus', 'near', 'near to', 'next to', 'notwithstanding', 'of', 'off', 'on', 'on top of', 'onto', 'opposite', 'out', 'out of', 'outside', 'over', 'past', 'pending', 'per', 'plus', 'regarding', 'respecting', 'round', 'save', 'saving', 'similar to', 'since', 'than', 'through', 'throughout', 'till', 'to', 'toward or towards', 'under', 'underneath', 'unlike', 'until', 'unto', 'up', 'upon', 'versus', 'via', 'wanting', 'while', 'with', 'within', 'without');
        $messages = array();
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*en_US*') as $file) {
            $contents = file_get_contents($file);
            foreach (explode("\n", $contents) as $line) {
                $line = trim($line);
                if ($line[0] == '*') { continue; }
                if (strpos($line, '/*') === 0) { continue; }
                if (strpos($line, "'Add Photos & Videos By Phone'") !== false) { continue; }
                if (strpos($line, "'Add By Phone'") !== false) { continue; }
                if (strpos($line, "'Upload Videos From Your Computer'") !== false) { continue; }
                if (strpos($line, "'Upload a Video From Your Computer'") !== false) { continue; }
                if (strpos($line, "'Upload Photos From Your Computer'") !== false) { continue; }
                if (strpos($line, "'Not Yet RSVPed'") !== false) { continue; }
                if (strpos($line, "'Upload Music From Your Computer'") !== false) { continue; }
                if (strpos($line, "this Gadget URL feed") !== false) { continue; }
                if (strpos($line, "'Problems Signing Up or Signing In'") !== false) { continue; }
                if (strpos($line, "'COUNTRY_") === 0) { continue; }
                if (strpos($line, "'Set as Main Feature'") !== FALSE) { continue; }
                if (strpos($line, 'Have another great idea for a Social Network?') !== FALSE) { continue; }
                // Detect if this is a title [Jon Aquino 2007-02-16]
                if (! preg_match("/[A-Z][a-z]* (.*[A-Z].*) [A-Z][a-z]*(?![a-z]* [a-z])/", $line, $matches)) { continue; }
                $middle = $matches[1];
                if (preg_match('/[.?!]/', $middle)) { continue; }
                $words = explode(' ', $line);
                if (count($words) > 10) { continue; }
                foreach ($pronouns as $pronoun) {
                    $this->assertFalse(preg_match('/\b' . strtolower($pronoun) . '\b/', $middle), 'Capitalize pronoun "' . $this->escape($pronoun) . '": ' . $this->escape($line));
                }
                foreach ($subordinateConjunctions as $subordinateConjunction) {
                    $this->assertFalse(preg_match('/\b' . strtolower($subordinateConjunction) . '\b/', $middle), 'Capitalize subordinateConjunction "' . $this->escape($subordinateConjunction) . '": ' . $this->escape($line));
                }
                foreach ($articles as $article) {
                    $this->assertFalse(preg_match('/\b' . ucfirst($article) . '\b/', $middle), 'Lowercase article "' . $this->escape($article) . '": ' . $this->escape($line));
                }
                foreach ($coordinateConjunctions as $coordinateConjunction) {
                    if (strpos($line, 'BROWSE_FOR_FILE') !== false) { break; }
                    $this->assertFalse(preg_match('/\b' . ucfirst($coordinateConjunction) . '\b/', $middle), 'Lowercase coordinateConjunction "' . $this->escape($coordinateConjunction) . '": ' . $this->escape($line));
                }
                foreach ($prepositions as $preposition) {
                    if (strpos($line, 'BROWSE_FOR_FILE') !== false) { break; }
                    if (strlen($preposition) >= 5) { continue; } // See http://www.writersblock.ca/tips/monthtip/tipmar98.htm
                    if ($preposition == 'up' && strpos($line, 'Setting Up') !== FALSE) { continue; } // See http://www.writersblock.ca/tips/monthtip/tipmar98.htm
                    if ($preposition == 'like' && strpos($line, 'You Like') !== FALSE) { continue; } // Verb in this case [Jon Aquino 2007-02-16]
                    if ($preposition == 'out' && strpos($line, 'Lay Out') !== FALSE) { continue; } // Verb in this case [Jon Aquino 2007-02-16]
                    if ($preposition == 'as' && strpos($line, 'As Others See It') !== FALSE) { continue; } // Subordinate conjunction in this case [Jon Aquino 2007-02-16]
                    if ($preposition == 'by' && strpos($line, 'By Invitation Only') !== FALSE) { continue; }
                    if ($preposition == 'from' && strpos($line, "'Removed From Main'") !== FALSE) { continue; }
                    if ($preposition == 'with' && strpos($line, 'Share This Item With Your Friends') !== FALSE) { continue; }
                    if ($preposition == 'as' && strpos($line, 'Remove As Friend') !== FALSE) { continue; }
                    $this->assertFalse(preg_match('/\b' . ucfirst($preposition) . '\b/', $middle), 'Lowercase preposition "' . $this->escape($preposition) . '": ' . $this->escape($line));
                }
            }
        }
    }

    public function testObsoleteTerminology() {
        $messages = array();
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*en_US*') as $file) {
            if (strpos($file, 'xn_volatile/backups') !== false) { continue; }
            $contents = file_get_contents($file);
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $line = str_replace('site update services', '', $line);
                $line = str_replace('site-tracking', '', $line);
                $line = str_replace('keep track', '', $line);
                $line = str_replace('Track visitors', '', $line);
                $line = str_replace('tracking', '', $line);
                $line = str_replace('The track(s) seem to have exceeded the limit', '', $line);
                $line = str_replace('\'TRACKS\' => \'Tracks\',', '', $line);
                $line = str_replace('Tracking', '', $line);
                $line = str_replace('TrackTo', '', $line);
                $line = str_replace('TRACK_STATISTICS', '', $line);
                $line = str_replace('Track Statistics', '', $line);
                $line = str_replace('TrackLink', '', $line);
                $line = str_replace('Videos Home', '', $line);
                $line = str_replace('Photos Home', '', $line);
                $line = str_replace('Notes Home', '', $line);
                $line = str_replace('Events Home', '', $line);
                $line = str_replace('2,4 - user name', '', $line);
                $line = str_replace('Forum Home', '', $line);
                $line = str_replace('external sites', '', $line);
                $line = str_replace('Facebook users', '', $line);
                $line = str_replace('Facebook, a popular social networking site', '', $line);
                $line = str_replace('to be a developer to promote', '', $line);
                $line = str_replace('Promote your network across the web', '', $line);
                $line = str_replace('home to a host of configuration options', '', $line);
                $line = str_replace('Select <strong>Users</strong>', '', $line);
                $lineNumber++;
                if (strpos($line, 'call_user_func_array') !== FALSE) { continue; }
                if (preg_match('/'
                        . '[Uu]ser(?!name| Agreement| authentication)' // User => person or member [Jon Aquino 2007-02-14]
                        . '|(?<!from any popular video |Add this video to your |another |Found a problem with the )\b[Ss]ites?\b(?! has an issue| off your network)'  // Site => network, social network, your social network, appname [Jon Aquino 2007-02-14]
                        . '|\b[Pp]romoted?\b' // Promote -> Feature [David Sklar 2007-02-21]
                        . '|.ayout .our' // layout your => arrange your (or lay out your) [Jon Aquino 2007-02-24]
                        . '|track' // track => song [Jon Aquino 2008-01-21]
                        . '|Track' // Track => Song [Jon Aquino 2008-01-21]
                        . '/', $line, $matches)) {
                    if (strpos($line, 'Promote to Administrator') !== false) { continue; }
                    if (strpos($line, 'USERS_COLON') !== false) { continue; }
                    if (strpos($line, 'Promote Your Network') !== false) { continue; }
                    if (strpos($line, 'Artist Site') !== false) { continue; }
                    if (strpos($line, 'Hosting Site') !== false) { continue; }
                    if (strpos($line, 'Label Site') !== false) { continue; }
                    if (strpos($line, 'tab when a user first adds it') !== false) { continue; }
                    if (strpos($line, 'Groups Home') !== false) { continue; }
                    if (strpos($line, 'any of the following sites') !== false) { continue; }
                    $this->assertTrue(FALSE, $matches[0] . ' in ' . $line . ' - ' . $file . ':' . $lineNumber . ' ***');
                }
            }
        }
    }

    public function testABeforeVowel() {
        $messages = array();
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*en_US*') as $file) {
            $contents = file_get_contents($file);
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                $line = str_replace('a URL', '', $line);
                $line = str_replace('a unique', '', $line);
                $line = str_replace('a user', '', $line);
                if (strpos($line, 'call_user_func_array') !== FALSE) { continue; }
                if (preg_match('/\ba [aeiou]/ui', $line, $matches)) {
                    $this->assertTrue(FALSE, $matches[0] . ' in ' . $line . ' - ' . $file . ':' . $lineNumber . ' ***');
                }
            }
        }
    }

    public function testStringFunctionsMultibyte() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test/') !== false) { continue; }
            if (strpos($file, 'W_WIDGETAPP_6_12_STUB.php') !== false) { continue; }
            if (strpos($file, 'buildSpamWords.php') !== false) { continue; }
            if (strpos($file, 'OpenSocial_SecurityHelper.php') !== false) { continue; }
            if (strpos($file, 'XG_SpamHelper.php') !== false) { continue; }
            if (strpos($file, 'Notes_Scrubber.php') !== false) { continue; }
            if (strpos($file, 'XG_PerfLogger.php') !== false) { continue; }
            if (strpos($file, 'RSA.class.php') !== false) { continue; }
            if (strpos($file, '/XG_Cache.php') !== false) { continue; }
            if (strpos($file, '/XG_Query.php') !== false) { continue; }
            if (basename($file) == 'XG_ImageHelper.php') { continue; }
            if (strpos($file, '/lib/ext/facebook') !== false) { continue; }
            $this->assertTest($file, '/\bstrlen\(|\bstripos\(|\bstrpos\(|\bstrrpos\(|\bsubstr\(|\bstrtolower\(|\bstrtoupper\(|\bsubstr_count\(|\bereg\(|\beregi\(|\bereg_replace\(|\beregi_replace\(|\bsplit\(/',
                         '/\/\*+\s*@non-mb\s*\*\//'); // if you wish to bypass flagging of non-mb string functions, use the comment: /** @non-mb */
        }
    }
    public function testPregFunctionsUFlag() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/test/') !== false) { continue; }
            if (strpos($file, 'buildSpamWords.php') !== false) { continue; }
            if (strpos($file, 'XG_SpamHelper.php') !== false) { continue; }
            if (strpos($file, 'Notes_Scrubber.php') !== false) { continue; }
            if (strpos($file, 'XG_PerfLogger.php') !== false) { continue; }
            if (basename($file) == 'XG_ImageHelper.php') { continue; }
            if (strpos($file, '/lib/ext/facebook') !== false) { continue; }
            if (strpos($file, 'OpenSocial_GadgetHelper.php') !== false) { continue; }
            $this->assertTest($file, "/(preg_grep|preg_match_all|preg_match|preg_replace_callback|preg_replace|preg_split).'[^']*[%@!\/][abcdefghijklmnopqrstvwxyz]*',/",
                    '/\/\*+\s*@non-mb\s*\*\//'); // if you wish to bypass flagging of non-mb string functions, use the comment: /** @non-mb */
            $this->assertTest($file, '/(preg_grep|preg_match_all|preg_match|preg_replace_callback|preg_replace|preg_split)."[^"]*[%@!\/][abcdefghijklmnopqrstvwxyz]*",/',
                    '/\/\*+\s*@non-mb\s*\*\//'); // if you wish to bypass flagging of non-mb string functions, use the comment: /** @non-mb */
        }
    }

    /**
     * searches the specified file line-by-line for the specified pattern; generates
     * an error if the pattern matches except for a few cases (see code below).  we
     * also accept an optional third parameter skipCheckPattern which if present in
     * a line will prevent the line from being checked.
     *
     * @param file string  the file to search
     * @param pattern string  regular expression pattern as a string
     * @param skipCheckPattern string  regular expression pattern, which if present in a line will bypass the pattern check
     */
    private function assertTest($file, $pattern, $skipCheckPattern = null) {
        if ($this->skippableFile($file)) {
            return;
        }
        $contents = file_get_contents($file);
        if (basename($file) === 'XG_LangHelper.php') {
            $contents = preg_replace('@lbrks = .*@u', '', $contents);
            $contents = preg_replace('@htmlwrap\(.str[^{]+\{' . XG_TestHelper::NESTED_CURLY_BRACKETS_PATTERN . '\}@us', '', $contents);
        }
        if (basename($file) === 'BlockedContactList.php') {
            $contents = preg_replace('@substr..decrypted@u', '', $contents);
        }
        if (basename($file) === 'Index_AppearanceHelper.php') {
            $contents = str_replace('$css = preg_replace(\'/@import/i\', \'@im /*disabled for the security reasons*/port\', $css);', '', $contents);
            $contents = str_replace('$css = preg_replace(\'/\bexpression\b/i\', \'expre  /*disabled for the security reasons*/ssion\', $css);', '', $contents);
            $contents = str_replace('$css = preg_replace(\'/-moz-binding\b/i\', \'-moz /*disabled for the security reasons*/-binding\', $css);', '', $contents);
            $contents = str_replace('return (strlen($m[1]) ? $m[1] : \' \') . $m[2];', '', $contents);
        }
        if (basename($file) === 'Notes_UrlHelper.php') {
            $contents = str_replace('preg_match(\'@[|?#/%.]@\'', '', $contents);
        }
        if (! preg_match($pattern, $contents)) {
            $this->assertTrue(TRUE);
        } else {
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                // if specified, we bypass checks if the line matches skipCheckPattern (see doc above) [ywh 2008-07-31]
                if (! is_null($skipCheckPattern) && preg_match($skipCheckPattern, $line)) { continue; }
                if (preg_match($pattern, $line, $matches)) {
                    if (strpos($line, 'strpos($thumbnailData, \'i:\')') !== false) { continue; }
                    if (strpos($line, 'firstBytes') !== false) { continue; }
                    if (strpos($line, 'if (!strlen($content)) {') !== false) { continue; }
                    if (strpos($line, '$type = strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE))') !== false) { continue; }
                    if (strpos($line, '// not mb_substr') !== false) { continue; }
                    if (strpos($line, 'strlen($xhtml) > self::getMaxLength($this->_widget)') !== false) { continue; }
                    $this->assertTrue(FALSE, $matches[0] . ' in ' . basename($file) . ':' . $lineNumber . ' ' . $line . ' ' . $file);
                }
            }
        }
    }

    /**
     * Returns true if the provided $file is something that's considered skippable
     *
     * @param string $file
     * @return bool
     */
    private function skippableFile($file) {
        static $skippableFiles = null;
        if (is_null($skippableFiles)) {
            $skippableFiles = array(
                $_SERVER['DOCUMENT_ROOT'] . '/runner.php' => '',
            );
        }
        return isset($skippableFiles[$file]);
    }

    public function testTranslateDefaultWidgetTitle() {
        $this->assertTrue('Foo', XG_MessageCatalog_en_US::translateDefaultWidgetTitle('Foo'));
        $this->assertTrue('Forum', XG_MessageCatalog_en_US::translateDefaultWidgetTitle('Forum'));
        // If the following defaults change, we'll need to change them in the non-English message catalogs [Jon Aquino 2007-02-10]
        $this->assertEqual('Blog', W_Cache::getWidget('profiles')->config['title']);
        $this->assertEqual('RSS', W_Cache::getWidget('feed')->config['title']);
        $this->assertEqual('Forum', W_Cache::getWidget('forum')->config['title']);
        $this->assertEqual('Videos', W_Cache::getWidget('video')->config['title']);
        $this->assertEqual('Photos', W_Cache::getWidget('photo')->config['title']);
        $this->assertEqual('Text Box', W_Cache::getWidget('html')->config['title']);
    }

    public function testMessageArraysCanBeExtracted() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, 'XG_MessageCatalog_*.php') as $filename) {
            $contents = file_get_contents($filename);
            $this->assertTrue(Index_MessageCatalogReader::extractPhpArray($contents), basename($filename));
            $this->assertTrue(Index_MessageCatalogReader::extractPhpSpecialRules($contents), basename($filename));
            $this->assertTrue(strpos(Index_MessageCatalogReader::extractPhpArray($contents), '/**') === false, basename($filename));
            $this->assertTrue(strpos(Index_MessageCatalogReader::extractPhpSpecialRules($contents), '/**') === false, basename($filename));
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
