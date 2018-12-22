<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class XG_AuthorizationHelperTest extends UnitTestCase {

    public function testSignUpNingUserUrl() {
        $this->assertEqual('/main/authorization/signUpNingUser?target=' . urlencode('http://example.org'),
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signUpNingUserUrl('http://example.org', null)));
        $this->assertEqual('/main/authorization/signUpNingUser?target=' . urlencode('http://example.org') . '&emailAddress=' . urlencode('jon@example.org'),
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signUpNingUserUrl('http://example.org', null, 'jon@example.org')));
        $this->assertEqual('/main/authorization/signUpNingUser?target=' . urlencode('http://devbazjon.xnqx.ningops.net/main/authorization/signUp'),
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signUpNingUserUrl('http://devbazjon.xnqx.ningops.net/main/authorization/signUp', null)));
    }

    public function testSignUpUrl() {
        $this->assertEqual('/main/authorization/signUp?target=' . urlencode('http://example.org'),
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signUpUrl('http://example.org', null)));
        $this->assertEqual('/main/authorization/signUp?target=' . urlencode('http://example.org') . '&emailAddress=' . urlencode('jon@example.org'),
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signUpUrl('http://example.org', null, 'jon@example.org')));
        $this->assertEqual('/main/authorization/signUp?target=' . urlencode('http://devbazjon.xnqx.ningops.net/main/authorization/signUp'),
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signUpUrl('http://devbazjon.xnqx.ningops.net/main/authorization/signUp', null)));
        $this->assertEqual('/main/authorization/signUp?',
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signUpUrl(xg_absolute_url('/'), null)));
        $this->assertEqual('/main/authorization/signUp?',
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signUpUrl('blah/main/error/404/blah', null)));
    }

    public function testSignInUrl() {
        $this->assertEqual('/main/authorization/signIn?target=' . urlencode('http://example.org'),
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signInUrl('http://example.org', null)));
        $this->assertEqual('/main/authorization/signIn?target=' . urlencode('http://devbazjon.xnqx.ningops.net/main/authorization/signUp'),
                preg_replace('@^.*?Test.php@', '', XG_AuthorizationHelper::signInUrl('http://devbazjon.xnqx.ningops.net/main/authorization/signUp', null)));
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
