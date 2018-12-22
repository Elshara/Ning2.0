<?php
/**
 * 	Test for the <form> tags inside dojo.html.createNodesFromText
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax29CmdlineTest extends CmdlineTestCase {
    public function testJsFormInCreateNodes(){
        foreach (XG_TestHelper::globr(NF_APP_BASE,'*.js') as $name) {
            if (preg_match('#/embed(\.js|/)|PostLink.js#',$name)) {
                continue;
            }
            $contents = self::getFileContent($name);
            preg_match_all('#\.createNodesFromText\s*\((\'(\\\\.|[^\'])*\'|"(\\\\.|[^"])*"|[^)]+)+\)#', $contents, $matches, PREG_SET_ORDER);
            foreach ($matches as $m) {
                if (preg_match('/<form.*<input.*hidden/i', $m[1])) {
                    $this->assertTrue(FALSE, "$name: Near `".substr($m[0],0,255)."'");
                    continue 2;
                }
            }
            $this->assertTrue(TRUE, "$name");
        }
    }

    public function testNoHasAttribute(){
        foreach (XG_TestHelper::globr(NF_APP_BASE,'*.js') as $file) {
            if (strpos($file, '/dojo') !== FALSE) { continue; }
            $contents = self::getFileContent($file);
            $this->assertTrue(mb_strpos($contents, 'hasAttribute') === FALSE, 'IE does not support hasAttribute: ' . $file);
        }
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
