<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_Embed.php');
XG_App::includeFileOnce('/widgets/photo/lib/helpers/Photo_EmbedType.php');
Mock::generate('XG_Embed');

class Photo_EmbedTypeTest extends UnitTestCase {

    public function testRowSize() {
        $this->assertEqual(1, TestThumbnailsEmbedType::rowSize($this->createEmbed(array('getType' => 'profiles')), 1));
        $this->assertEqual(1, TestThumbnailsEmbedType::rowSize($this->createEmbed(array('getType' => 'homepage')), 1));
        $this->assertEqual(4, TestThumbnailsEmbedType::rowSize($this->createEmbed(array('getType' => 'profiles')), 2));
        $this->assertEqual(3, TestThumbnailsEmbedType::rowSize($this->createEmbed(array('getType' => 'homepage')), 2));

        $this->assertEqual(1, TestAlbumsEmbedType::rowSize($this->createEmbed(array('getType' => 'profiles')), 1));
        $this->assertEqual(1, TestAlbumsEmbedType::rowSize($this->createEmbed(array('getType' => 'homepage')), 1));
        $this->assertEqual(3, TestAlbumsEmbedType::rowSize($this->createEmbed(array('getType' => 'profiles')), 2));
        $this->assertEqual(3, TestAlbumsEmbedType::rowSize($this->createEmbed(array('getType' => 'homepage')), 2));
    }

    public function testThumbnailSize() {
        $this->assertEqual(139, TestThumbnailsEmbedType::thumbnailSize($this->createEmbed(array('getType' => 'profiles')), 1));
        $this->assertEqual(139, TestThumbnailsEmbedType::thumbnailSize($this->createEmbed(array('getType' => 'homepage')), 1));
        $this->assertEqual(124, TestThumbnailsEmbedType::thumbnailSize($this->createEmbed(array('getType' => 'profiles')), 2));
        $this->assertEqual(139, TestThumbnailsEmbedType::thumbnailSize($this->createEmbed(array('getType' => 'homepage')), 2));

        $this->assertEqual(165, TestAlbumsEmbedType::thumbnailSize($this->createEmbed(array('getType' => 'profiles')), 1));
        $this->assertEqual(165, TestAlbumsEmbedType::thumbnailSize($this->createEmbed(array('getType' => 'homepage')), 1));
        $this->assertEqual(165, TestAlbumsEmbedType::thumbnailSize($this->createEmbed(array('getType' => 'profiles')), 2));
        $this->assertEqual(139, TestAlbumsEmbedType::thumbnailSize($this->createEmbed(array('getType' => 'homepage')), 2));
    }

    public function testShouldShowCreator() {
        $this->assertFalse(TestThumbnailsEmbedType::shouldShowCreator($this->createEmbed(array('getType' => 'profiles'))));
        $this->assertTrue(TestThumbnailsEmbedType::shouldShowCreator($this->createEmbed(array('getType' => 'homepage'))));

        $this->assertFalse(TestAlbumsEmbedType::shouldShowCreator($this->createEmbed(array('getType' => 'profiles'))));
        $this->assertTrue(TestAlbumsEmbedType::shouldShowCreator($this->createEmbed(array('getType' => 'homepage'))));
    }

    private function createEmbed($returnValues) {
        $embed = new MockXG_Embed();
        foreach ($returnValues as $functionName => $returnValue) {
            $embed->setReturnValue($functionName, $returnValue);
        }
        return $embed;
    }

}

class TestThumbnailsEmbedType extends Photo_ThumbnailsEmbedType {
    public function rowSize($embed, $columnCount) {
        return parent::rowSize($embed, $columnCount);
    }
    public function thumbnailSize($embed, $columnCount) {
        return parent::thumbnailSize($embed, $columnCount);
    }
    public function shouldShowCreator($embed) {
        return parent::shouldShowCreator($embed);
    }
}

class TestAlbumsEmbedType extends Photo_AlbumsEmbedType {
    public function rowSize($embed, $columnCount) {
        return parent::rowSize($embed, $columnCount);
    }
    public function thumbnailSize($embed, $columnCount) {
        return parent::thumbnailSize($embed, $columnCount);
    }
    public function shouldShowCreator($embed) {
        return parent::shouldShowCreator($embed);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
