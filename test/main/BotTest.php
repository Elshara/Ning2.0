<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class BotTest extends UnitTestCase {

    public function test() {
        $contents = strip_tags(file_get_contents(xg_absolute_url('/lib/scripts/bot.php?test_generate_resources=1')));
        $this->assertEqual('', $contents);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
