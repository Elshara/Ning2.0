<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/AbstractLanguageTest.php');

/**
 * Tests I18N strings for the pt_BR translation.
 */
class pt_BR_TestTemp extends AbstractLanguageTest {

    public function setUp() {
        $this->language = 'pt_BR';
        $this->allowedCharacters = ' -~…’»©–—←«\táíéóñ·¿êÁãçõÉàúôÍÚü';
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

