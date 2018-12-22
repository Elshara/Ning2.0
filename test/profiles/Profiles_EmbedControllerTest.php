<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/controllers/EmbedController.php');
Mock::generate('XG_Embed');

class Profiles_EmbedControllerTest extends UnitTestCase {

    public function testUsersPerRow() {
        $controller = new TestEmbedController();
        $this->assertEqual(2, $controller->usersPerRow($this->createEmbed('large'), 220));
        $this->assertEqual(4, $controller->usersPerRow($this->createEmbed('small'), 220));
        $this->assertEqual(5, $controller->usersPerRow($this->createEmbed('large'), 502));
        $this->assertEqual(9, $controller->usersPerRow($this->createEmbed('small'), 502));
        $this->assertEqual(1, $controller->usersPerRow($this->createEmbed('large'), 173));
        $this->assertEqual(3, $controller->usersPerRow($this->createEmbed('small'), 173));
    }

    private function createEmbed($displaySet) {
        $embed = new MockXG_Embed();
        $embed->setReturnValue('get', $displaySet, array('displaySet'));
        return $embed;
    }

}

class TestEmbedController extends Profiles_EmbedController {
    public function __construct() {
        return parent::__construct(W_Cache::getWidget('profiles'));
    }
    public function usersPerRow($embed, $maxEmbedWidth) {
        return parent::usersPerRow($embed, $maxEmbedWidth);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';

