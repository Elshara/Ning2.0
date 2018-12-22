<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax26CmdlineTest extends CmdlineTestCase {

    /*
    Test if any file contains the strings:
    Services_JSON, json_encode, or json_decode
    These are the old JSON libraries we're not using anymore.
    The test ignores comments and NS_JSON(SERVICES_JSON_LOOSE_TYPE) calls.

    One limitation is that it does not check to see if a comment is within a
    string and should be ignored.
    For example:
        echo '//';

    */
    public function testJsonProper(){
        // Commenting this out until we get a chance to fix it in BAZ-7892 [Jon Aquino 2008-06-10]
        /*
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if(mb_strpos($file, 'Syntax26Test.php') !== FALSE){ continue; }
            $contents = self::getFileContent($file);
            $lineNumber = 0;
            $multilineComment = FALSE;
            foreach(explode("\n", $contents) as $line){
                $lineNumber++;
                $line = str_replace('SERVICES_JSON_LOOSE_TYPE', '', $line);
                $line = preg_replace('@//.+$@', '', $line);
                if($multilineComment === FALSE && mb_strpos($line, "/" . "*")){
                    $multilineComment = TRUE;
                    $line = preg_replace("@/" . "\*.+(\*" . "/)?@", " ", $line);
                }else if($multilineComment === TRUE && mb_strpos($line, "*" . "/")){
                    $multilineComment = FALSE;
                    $line = preg_replace("@.+\*" . "/@", "", $line);
                }else if($multilineComment === TRUE){
                    continue;
                }
                if( mb_strripos($line, 'Services_JSON') === false &&
                    mb_strripos($line, 'json_encode') === false &&
                    mb_strripos($line, 'json_decode') === false){ continue; }
                $this->assertTrue(FALSE, $this->format("Illegal JSON call", $file, $line, $lineNumber));
            }
        }
        */
    }

    private function format($errorStr, $file, $line, $lineNumber) {
        return $errorStr . ' in ' . $line . ' ' . $file . ' ' . $lineNumber . ' ***';
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
