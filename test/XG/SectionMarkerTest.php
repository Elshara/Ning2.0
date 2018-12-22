<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class SectionMarkerTest extends UnitTestCase {

    public function testDefaultSectionMarker() {
        XG_App::addToSection('one');
        XG_App::addToSection('two');
        $buf = 'three' . XG_App::sectionMarker() . 'four';
        $this->assertEqual(XG_App::populateSections($buf),
                           'threeonetwofour');
    }


    public function testUserSectionMarker() {
        $userSection = 'this <has> HTML & and special < chars ' .chr(7) . ' in " it';
        XG_App::addToSection('one',$userSection);
        XG_App::addToSection('two',$userSection);
        $buf = 'three' . XG_App::sectionMarker($userSection) . 'four';
        $this->assertEqual(XG_App::populateSections($buf),
                           'threeonetwofour');
    }

    public function testAppFrontPage() {
        $html = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/');
        
        // There should *not* be any xn:head references in the retrieved HTML
        $this->assertFalse($pos = mb_strpos($html, '<xn:head>'),
                           "Front page contains <xn:head/> @ character $pos: [..." .
                           mb_substr($html, $pos - 100, 200) . '...]');

        // There *should* be an activity autodiscovery feed link inside the page head
        $result = $this->getXPathQueryResult('//head/link[@type="application/rss+xml"]', $html);
        $this->assertTrue(count($result) > 0,
                             "No activity feed autodiscovery link in the page head: make " .
                             " sure there's an activity embed on the front page of this " .
                             " app for this test to succeed.");
    }

    private function getXPathQueryResult($query, $contents) {
        $d = new DOMDocument();
        $d->loadHTML($contents);
        $x = new DOMXPath($d);
        return $x->query($query);

    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
