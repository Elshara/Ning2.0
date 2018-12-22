<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_FileHelper.php');

class XG_FileHelperTest extends UnitTestCase {

    public function testBasename() {
        $this->assertEqual('pepper.txt', XG_FileHelper::basename('/usr/pepper.txt'));
        $this->assertEqual('pepper.txt', XG_FileHelper::basename('c:\\junk\\pepper.txt'));
        $this->assertEqual('c:\\junk\\', XG_FileHelper::basename('c:\\junk\\'));
    }

    public function testFilePutContentsWithBackup() {
        @unlink(NF_APP_BASE . '/XG_FileHelperTest.txt');
        @unlink(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.1');
        @unlink(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.2');
        @unlink(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.3');

        XG_FileHelper::filePutContentsWithBackup(NF_APP_BASE . '/XG_FileHelperTest.txt', 'A', 3);
        $this->assertEqual('A', file_get_contents(NF_APP_BASE . '/XG_FileHelperTest.txt'));
        $this->assertEqual('A', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.1'));
        $this->assertFalse(file_exists(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.2'));
        $this->assertFalse(file_exists(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.3'));
        sleep(1);

        XG_FileHelper::filePutContentsWithBackup(NF_APP_BASE . '/XG_FileHelperTest.txt', 'B', 3);
        $this->assertEqual('B', file_get_contents(NF_APP_BASE . '/XG_FileHelperTest.txt'));
        $this->assertEqual('A', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.1'));
        $this->assertEqual('B', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.2'));
        $this->assertFalse(file_exists(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.3'));
        sleep(1);

        XG_FileHelper::filePutContentsWithBackup(NF_APP_BASE . '/XG_FileHelperTest.txt', 'C', 3);
        $this->assertEqual('C', file_get_contents(NF_APP_BASE . '/XG_FileHelperTest.txt'));
        $this->assertEqual('A', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.1'));
        $this->assertEqual('B', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.2'));
        $this->assertEqual('C', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.3'));
        sleep(1);

        XG_FileHelper::filePutContentsWithBackup(NF_APP_BASE . '/XG_FileHelperTest.txt', 'D', 3);
        $this->assertEqual('D', file_get_contents(NF_APP_BASE . '/XG_FileHelperTest.txt'));
        $this->assertEqual('D', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.1'));
        $this->assertEqual('B', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.2'));
        $this->assertEqual('C', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.3'));
        sleep(1);

        XG_FileHelper::filePutContentsWithBackup(NF_APP_BASE . '/XG_FileHelperTest.txt', 'E', 3);
        $this->assertEqual('E', file_get_contents(NF_APP_BASE . '/XG_FileHelperTest.txt'));
        $this->assertEqual('D', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.1'));
        $this->assertEqual('E', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.2'));
        $this->assertEqual('C', file_get_contents(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.3'));
        sleep(1);

        @unlink(NF_APP_BASE . '/XG_FileHelperTest.txt');
        @unlink(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.1');
        @unlink(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.2');
        @unlink(NF_APP_BASE . '/xn_private/xn_volatile/backups/XG_FileHelperTest.txt.3');
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
