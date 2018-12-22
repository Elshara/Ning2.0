<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class XG_AppTest extends UnitTestCase {

    public function testSymlinked() {
        XG_App::symlinked();
        $this->assertTrue(true, 'No exception');
    }

    public function testCanSeeInviteLinksCalledWhenNecessaryBaz4230() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $filename) {
            if (strpos($filename, '/apps/devbazjon/widgets/profiles/templates/embed/embed1friends.php') !== false) { continue; }
            if (strpos($filename, '/index/templates/embed/embed2welcome.php') !== false) { continue; }
            if (strpos($filename, '/index/templates/membership/listBanned.php') !== false) { continue; }
            if (strpos($filename, '/index/templates/membership/listInvited.php') !== false) { continue; }
            if (strpos($filename, '/index/templates/membership/listMembers.php') !== false) { continue; }
            if (strpos($filename, '/index/templates/membership/listAdministrators.php') !== false) { continue; }
            if (strpos($filename, '/index/templates/membership/listRequested.php') !== false) { continue; }
            if (strpos($filename, '/index/templates/membership/listPending.php') !== false) { continue; }
            if (strpos($filename, '/index/templates/membership/tabs.php') !== false) { continue; }
            if (strpos($filename, '/XG_ListTemplateHelper.php') !== false) { continue; }
            $contents = file_get_contents($filename);
            $this->assertTrue(strpos($contents, '"/invite') === false || strpos($contents, 'canSeeInviteLinks') !== false || strpos($contents, 'canCurrentUserSeeInviteLinks') !== false, $filename);
        }
    }

    public function testNoXnHead() {
        // Ignore test files that reference xn:head
        $filesToIgnore = array(__FILE__ => true,
                               dirname(__FILE__) . '/SectionMarkerTest.php' => true);
        foreach (XG_TestHelper::globr(NF_APP_BASE, '*.php') as $filename) {
            if (isset($filesToIgnore[$filename])) { continue; }
            $contents = file_get_contents($filename);
            $this->assertFalse(strpos($contents, 'xn:head'), "$filename contains xn:head");
            $this->assertFalse(strpos($contents, 'ning:xnhtml-head'),
                               "$filename contains ning:xnhtml-head");
        }
    }

    public function testConstant1() {
        try {
            XG_App::constant('XG_AppTest::FOO1');
            $this->fail();
        } catch (Exception $e) {
            $this->assertEqual('Constant not defined: XG_AppTest::FOO1', $e->getMessage());
        }
    }

    const FOO2 = 'bar';

    public function testConstant2() {
        $this->assertEqual('bar', XG_App::constant('XG_AppTest::FOO2'));
    }

    public function testConstant3() {
        W_Cache::getWidget('admin')->config['XG_AppTest_FOO2'] = 'baz';
        $this->assertEqual('baz', XG_App::constant('XG_AppTest::FOO2'));
    }

    const FOO4 = FALSE;

    public function testConstant4() {
        $this->assertIdentical(FALSE, XG_App::constant('XG_AppTest::FOO4'));
    }

    const FOO5 = TRUE;

    public function testConstant5() {
        $this->assertIdentical(TRUE, XG_App::constant('XG_AppTest::FOO5'));
    }

    /**
     * parse the application endpoint xml response; we can't use XN_Application::load()
     * because its results are cached per session so there's no way to see if our values
     * were properly set.
     *
     * @param xml string  XML response returned by /xn/atom/1.0/application endpoint
     *
     * @return Array  an array containing 3 values that we are checking
     */
    private function _parseApplicationXmlResponse($xml) {
        preg_match('/<title.+?>([^<]+)<\/title>/', $xml, $matches);
        $title = $matches[1];
        preg_match('/<xn:active>([^<]+)<\/xn:active>/', $xml, $matches);
        $xnActive = $matches[1];
        preg_match('/<xn:tag>([^<]+)<\/xn:tag>/', $xml, $matches);
        $tag = $matches[1];
        return array('application_name' => $title, 'application_tags' => $tag, 'application_online' => $xnActive);
    }

    /**
     * write some new values to 3 fields and then check they were written correctly.
     * the three fields are <title />, <xn:active />, and <xn:tag /> aka
     * application_name, application_online, and application_tags, respectively.
     *
     * @param method string  what method to use: one of 'post' or 'put'
     * @param online boolean  online status to use
     */
    private function _doTestApplicationEndpoint($method, $name = null, $tags = null, $online = false) {
        $postData['application_name'] = is_null($name) ? md5(uniqid().'name') : $name;
        $postData['application_tags'] = is_null($tags) ? md5(uniqid().'tags') : $tags;
        $postData['application_online'] = $online ? 'true' : 'false';

        try {
            if ($method === 'put') {
                XN_REST::put('/xn/rest/1.0/application:' . urlencode(XN_Application::load()->relativeUrl) . '?xn_method=put', $postData, null);
            } else {
                XN_REST::post('/xn/rest/1.0/application:' . urlencode(XN_Application::load()->relativeUrl) . '?xn_method=put', $postData, null);
            }
            $this->assertEqual($postData, self::_parseApplicationXmlResponse(XN_REST::get('/xn/atom/1.0/application')));
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * tests that we can set values properly using PUT and POST on the application endpoint
     * see BAZ-9603 for more information on why this is necessary
     */
    public function testApplicationEndpoint() {
        // save the current settings we'll be overwriting to restore them later
        $current = self::_parseApplicationXmlResponse(XN_REST::get('/xn/atom/1.0/application'));

        // do test
        foreach (array('put','post') as $method) {
            self::_doTestApplicationEndpoint($method, null, null, $method === 'post');
        }

        // restore app settings
        self::_doTestApplicationEndpoint('put', $current['application_name'], $current['application_tags'], $current['application_online'] === 'true');
    }
}

class Test_XG_App extends XG_App {
    public static function setRequestedRouteForTesting($requestedRoute) {
        parent::setRequestedRouteForTesting($requestedRoute);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
