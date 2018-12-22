<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/AbstractLanguageTest.php');

/**
 * Tests I18N strings for the zh_CN translation.
 */
class zh_CN_TestTemp extends AbstractLanguageTest {

    public function setUp() {
        $this->language = 'zh_CN';
        $this->allowedCharacters = ' -~…’»©–—←«\táíéóñ·¿êÁãçõÉàúôÍÚü';
    }

   public function testInvalidCharacters() { }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

