<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/AbstractLanguageTest.php');

/**
 * Tests I18N strings for the en_GB translation.
 */
class en_GB_TestTemp extends AbstractLanguageTest {

    public function setUp() {
        $this->language = 'en_GB';
        $this->allowedCharacters = ' -~…’»©–—←«\táíéóñ·¿êÁãçõÉàúôÍÚü';
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

