<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/AbstractLanguageTest.php');

/**
 * Tests I18N strings for the fr_CA translation.
 */
class fr_CA_TestTemp extends AbstractLanguageTest {

    public function setUp() {
        $this->language = 'fr_CA';
        $this->allowedCharacters = ' -~…’»©–—←«\táíéóñ·¿êÁãçõÉàúôÍÚüèâîÀÊûÎëù';
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

