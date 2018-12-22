<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax27CmdlineTest extends CmdlineTestCase {
    /**
     * 	Test if any JS files contains extra commas (which causes errors in IE)
     */
    public function testJsExtraCommas(){
        foreach(XG_TestHelper::globr(NF_APP_BASE,'*.js') as $name) {
            if (strpos($name, '/dojo') !== FALSE) { continue; }
            if (mb_stripos($name, '.min.js') !== false) { continue; }
            $contents = self::getFileContent($name);
            $this->assertTrue(
                !preg_match('/,\s*[})\]]/s',$contents) || $this->isSkippableFile($name),
                $name
            );
        }
    }

    private function isSkippableFile($name) {
        return substr($name, -18) == 'jquery.json.min.js';
    }

    private static $files = null;

    private function getFileNames() {
        if (is_null($this->files)) {
            $this->files = XG_TestHelper::globr(NF_APP_BASE, '*.php');
        }
    }

    public function testUnmatchedOutputBuffer(){
        // work-in-progress; commented out for check-in to prevent false positives
        //$this->getFileNames();
        //foreach($this->files as $file) {
        //    if (preg_match('/^\/apps\/[^\/]+\/test\//', $file)) { continue; } // skip unit tests
        //    $contents = file_get_contents($file);
        //    $lines = explode("\n", $contents);
        //    $numLines = count($lines);
        //    $this->extractBlockContextAndCheckFast($file, $lines, $numLines, 0);
        //}
    }

    private function extractBlockContextAndCheckFast($file, $lines, $numLines, $lineNum, $linePos = null) {
        $thisBlock = "";
        if (is_null($linePos)) { $linePos = 0; }
        while ($lineNum < $numLines) {
            $line = $lines[$lineNum++];
            $lineLen = strlen($line);
            $blockStartPos = strpos($line, '{', $linePos); $blockStartPos = $blockStartPos === false ? $lineLen : $blockStartPos;
            $blockEndPos = strpos($line, '}', $linePos);   $blockEndPos = $blockEndPos === false ? $lineLen : $blockEndPos;
            if ($blockStartPos < $blockEndPos) {
                $pos = $blockStartPos;
                if ($pos > $linePos) {
                    $thisBlock .= $lineNum . '!!' . substr($line, $linePos, $pos - $linePos) . "\n";
                }
                list ($lineNum, $linePos) = $this->extractBlockContextAndCheckFast($file, $lines, $numLines, $lineNum - 1, $pos + 1);
            } elseif ($blockEndPos < $blockStartPos) {
                $pos = $blockEndPos;
                if ($pos > $linePos) {
                    $thisBlock .= $lineNum . '!!' . substr($line, $linePos, $pos - $linePos) . "\n";
                }
                $this->doBlockObCheck($thisBlock, $file);
                return array($lineNum - 1, $pos + 1);
            } else {
                $thisBlock .= $lineNum . '!!' . substr($line, $linePos) . "\n";
                $linePos = 0;
            }
        }
        $this->doBlockObCheck($thisBlock, $file);
        return array($lineNum, 0);
    }

    private function doBlockObCheck($blockContents, $file) {
        $level = 0;
        $startStack = array();

        foreach (explode("\n", $blockContents) as $lineCode) {
            list ($lineNum, $line) = split('!!', $lineCode, 2);

            $offset = 0;
            while (preg_match('/(ob_(?:start|end_clean|end_flush|get_clean))/i', $line, $matches, PREG_OFFSET_CAPTURE, $offset)) {
                $func = strtolower($matches[1][0]);
                $offset = intval($matches[1][1]) + strlen($func);
                if ($func === 'ob_start') {
                    array_push($startStack, $lineNum);
                    $level++;
                } else {
                    if ($level > 0) {
                        $level--;
                        array_pop($startStack);
                        $this->assertTrue(true);
                    } else {
                        $this->assertTrue(false, $this->format("$func called without ob_start", $file, $lineNum));
                    }
                }
            }

        }

        if ($level > 0) {
            for ($i = 0; $i < $level; $i++) {
                $this->assertTrue(false, $this->format("unterminated ob_start", $file, $startStack[$i]));
            }
        }
    }

    private function removeComments($contents) {
        $inComment = false;
        $ret = "";
        foreach (explode("\n", $contents) as $line) {
            $line = preg_replace('/\/\*.*?\*\//', '', $line); // erase one-line comments of /* comment */
            if (preg_match('/\/\*/', $line)) {
                $line = preg_replace('/\/\*.*$/', '', $line);
                $inComment = true;
            } elseif ($inComment) {
                if (preg_match('/\*\//', $line)) {
                    $line = preg_replace('/.*\*\//', '', $line);
                    $inComment = false;
                } else {
                    $line = "";
                }
            }
            $line = preg_replace('/\/\/.*$/', '', $line); // erase one-line comments like this one
            $ret .= $line . "\n";
        }
        return $ret;
    }

    private function format($errorStr, $file, $lineNumber) {
        return $errorStr . ' in ' . $file . ' ' . $lineNumber . ' ***';
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
