<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/test/AbstractLanguageTest.php');

/**
 * Tests I18N strings for the es_AR translation.
 */
class es_AR_TestTemp extends AbstractLanguageTest {

    public function setUp() {
        $this->language = 'es_AR';
        $this->allowedCharacters = ' -~…’»©–—←«\táíéóñ·¿êÁãçõÉàúôÍÚüâ‚¡Ì˙';
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
