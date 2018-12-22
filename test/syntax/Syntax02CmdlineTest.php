<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

/**
 * Syntax tests continued.
 */
class Syntax02CmdlineTest extends CmdlineTestCase {

    /** @see BAZ-2609 */
    public function testDoNotUseIncludeOrRequire() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            if (strpos($file, 'lib/ext/facebook') !== FALSE) { continue; }
            if (strpos($file, 'termsOfService.php') !== FALSE) { continue; }
            if (strpos($file, 'privacyPolicy.php') !== FALSE) { continue; }
            if (strpos($file, 'termsOfService_iphone.php') !== FALSE) { continue; }
            if (strpos($file, 'privacyPolicy_iphone.php') !== FALSE) { continue; }
            if (strpos($file, 'applicationTos.php') !== FALSE) { continue; }
            $pattern = '@(?<!\$)include(?![a-zA-Z])|(?<!ning.loader.)(?<!dojo.)require(?![a-zA-Z])@';
            $contents = self::getFileContent($file);
            $contents = str_replace('$require', '', $contents);
            if (preg_match($pattern, $contents)) {
                $lineNumber = 0;
                foreach (explode("\n", $contents) as $line) {
                    $lineNumber++;
                    if ($this->isAllowedLine($line)) { continue; }
                    $line = str_replace('require our affiliates', '', $line);
                    $line = str_replace('include Code or Content', '', $line);
                    $line = preg_replace('@//.*@', '', $line);
                    $line = preg_replace('@\* .*@', '', $line);
                    if (strpos($line, 'findInclude') !== false) { continue; }
                    if (strpos($line, 'Cache/Lite') !== false) { continue; }
                    if (strpos($line, 'do not include') !== false) { continue; }
                    if (strpos($line, 'include features such as') !== false) { continue; }
                    if (strpos($line, 'include Member registration') !== false) { continue; }
                    if (strpos($line, 'include with your Content') !== false) { continue; }
                    if (strpos($line, 'to include') !== false) { continue; }
                    if (strpos($line, 'xn-app://') !== false) { continue; }
                    if (strpos($line, '=>') !== false) { continue; }
                    if (strpos($file, 'lib/scripts') !== false) { continue; }
                    if (strpos($file, 'xn/ningbar.php') !== false) { continue; }
                    if (trim($line) == 'require_once($x);') { continue; }
                    if (trim($line) == 'require_once $include;') { continue; }


// Remove these lines when BAZ-2551 is done [Jon Aquino 2007-05-09]
if (strpos($line, "NF_APP_BASE . '/lib") !== false) { continue; }
if (strpos($line, "phpCatalogPath") !== false) { continue; }
if (strpos($line, 'XG_') !== false) { continue; }
if (strpos($file, 'XG_') !== false) { continue; }
if (strpos($file, 'lib/index.php') !== false) { continue; }
if (strpos($line, 'buildPath') !== false) { continue; }
if (strpos($line, 'XNC') !== false) { continue; }
if (strpos($line, 'XML/RPC') !== false) { continue; }
if (strpos($line, 'WWF/') !== false) { continue; }
// Found this in /ext/facebook/music/appinclude.php [Jon Aquino 2007-08-25]
if (strpos($line, "NF_APP_BASE . '/xn_private") !== false) { continue; }


                    $this->assertFalse(preg_match($pattern, $line, $matches), $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    /** @see BAZ-2696 */
    public function testDoNotUseDojoAddOnLoad() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== FALSE) { continue; }
            if (strpos($file, 'index/templates/embed/footer.php') !== FALSE) { continue; }
            if (strpos($file, '/dojo') !== FALSE) { continue; }
            if (strpos($file, 'adapter/core.js') !== FALSE) { continue; }
            if (strpos($file, 'xn_resources/widgets/lib/core.min.js') !== false) { continue; }
            $pattern = '@dojo.addOnLoad@';
            $contents = self::getFileContent($file);
            if (preg_match($pattern, $contents)) {
                $lineNumber = 0;
                foreach (explode("\n", $contents) as $line) {
                    $lineNumber++;
                    $line = preg_replace('@//.*@', '', $line);
                    $line = preg_replace('@\* .*@', '', $line);
                    if (strpos($file, 'slideshow.js') !== false) { continue; } // Loaded via <script> tag, for IE workaround [Jon Aquino 2007-05-10]
                    if (strpos($file, 'player.js') !== false) { continue; } // Loaded via <script> tag, for IE workaround [Jon Aquino 2007-05-10]
                    $this->assertFalse(preg_match($pattern, $line, $matches), $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    public function testMultipleClassAttributes() {
        foreach(XG_TestHelper::globr(NF_APP_BASE . '', '*.php') as $file) {
            if (strpos($file, '/test') !== false) { continue; }
            $pattern = '/class=.*class=/i';
            $contents = self::getFileContent($file);
            $contents = str_replace('xg_html(\'KEY_MISSING_TEXT\', \'class="missing"\', \'class="changed"\')', '', $contents);
            $lineNumber = 0;
            foreach (explode("\n", $contents) as $line) {
                $lineNumber++;
                if (preg_match('/class=[^<>]+class=/', str_replace('<%', '[%', str_replace('%>', '%]', $line)), $matches)) {
                    if (strpos($line, '_controlAttributes') !== false) { continue; }
                    if (strpos($line, 'FROM_SENDER_TO_RECIPIENT_LIST') !== false) { continue; }
                    if (strpos($line, 'FROM_SENDER_TO_RECIPIENT') !== false) { continue; }
                    if (strpos($line, 'HEADLINE_TITLE_COUNT_UPDATE_SHOWZERO') !== false) { continue; }
                    if (strpos($line, 'HEADLINE_TITLE_COUNT_UPDATE') !== false) { continue; }
                    if (preg_match("@' class.*' class@u", $line)) { continue; }
                    $this->assertTrue(FALSE, $this->format($matches[0], $file, $line, $lineNumber));
                }
            }
        }
    }

    private function format($match, $file, $line, $lineNumber) {
        return $this->escape($match) . ' in ' . $this->escape($line) . ' ' . $file . ' ' . $lineNumber . ' ***';
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
