<?php
class CmdlineTestCase extends BazelTestCase {
    public static function getFileContent($filename) {
        return file_get_contents($filename);
    }
}
?>
