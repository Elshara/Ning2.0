<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MembersEndpointHelper.php');

class Index_MembersEndpointHelperTest extends UnitTestCase {

    public function testBuildFeed1() {
        $expectedXml = '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
  <title type="text" />
  <xn:size>1</xn:size>
  <updated>2008-09-04T18:19:35+00:00</updated>
  <entry>
    <id>http://d.e.f</id>
    <xn:id>2ntueiok7wlet</xn:id>
    <title type="text">Joe Smith</title>
    <link href="http://a.b.c" rel="icon"/>
  </entry>
</feed>';
        $helper = new TestMembersEndpointHelper();
        $actualXml = $helper->buildFeedProper(array(array(
            'screenName' => '2ntueiok7wlet',
            'profileUrl' => 'http://d.e.f',
            'fullName' => 'Joe Smith',
            'thumbnailUrl' => 'http://a.b.c',
        )), 1220552375);
        $this->assertEqual($this->normalize($expectedXml), $this->normalize($actualXml));
    }

    public function testBuildFeed2() {
        $expectedXml = '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:xn="http://www.ning.com/atom/1.0">
  <title type="text" />
  <xn:size>0</xn:size>
  <updated>2008-09-04T18:19:35+00:00</updated>
</feed>';
        $helper = new TestMembersEndpointHelper();
        $actualXml = $helper->buildFeedProper(array(), 1220552375);
        $this->assertEqual($this->normalize($expectedXml), $this->normalize($actualXml));
    }

    private function normalize($xml) {
        return str_replace(' ', '', str_replace("\n", '', $xml));
    }

}

class TestMembersEndpointHelper extends Index_MembersEndpointHelper {
    public function buildFeedProper($users, $time) {
        return parent::buildFeedProper($users, $time);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
