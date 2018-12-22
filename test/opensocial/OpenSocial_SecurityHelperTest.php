<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

XG_App::includeFileOnce('/widgets/opensocial/lib/helpers/OpenSocial_SecurityHelper.php');
XG_App::includeFileOnce('/widgets/opensocial/lib/helpers/OpenSocial_GadgetHelper.php');

class OpenSocial_SecurityHelperTest extends UnitTestCase {

    public function setUp() {
    }

    public function tearDown() {
    }

    public function testKey() {

      $key = OpenSocial_SecurityHelper::appKey();
      $key2 = OpenSocial_SecurityHelper::appKey();
      
      $this->assertTrue($key == $key2, 'OpenSocial_SecurityHelper::appKey() should always return the same key');
    }

    public function testEncryptDecrypt() {

      $message = 'very secret message';

      $encrypted = OpenSocial_SecurityHelper::encrypt($message);

      $result = OpenSocial_SecurityHelper::decrypt($encrypted);

      $this->assertEqual($message, $result);
    }


    public function testEncryptDecryptSpaces() {

      $message = '                ';

      $encrypted = OpenSocial_SecurityHelper::encrypt($message);

      $result = OpenSocial_SecurityHelper::decrypt($encrypted);

      $this->assertEqual($message, $result);
    }


    public function testEncryptDecryptMultiLength() {

      $str = '';
      for ($i = 0; $i < 1000; $i++) {

	$encrypted = OpenSocial_SecurityHelper::encrypt($str);
	
	$result = OpenSocial_SecurityHelper::decrypt($encrypted);
	
	$this->assertEqual(strlen($result), $i);
	// Base 64 because displaying the raw strings is ugly...
	$this->assertEqual(base64_encode($str), base64_encode($result));
	
	// no nul bytes please
	$str .= chr(rand(1, 255));
      }
    }

    // Bazel can no longer decrypt the security token BAZ-9784 [dkf 2008-09-15]
    /*
    public function testToken() {
      $domain = 'test.example.com';
      $viewer = 'viewer_id';
      $owner = 'owner_id';
      $url = "http://www.gadgetsrus.com/gadget.xml";
      $index = 0;

      $gadget = new OpenSocialGadget($index, $domain, $url, $viewer, $owner);

      $token = OpenSocial_SecurityHelper::generateSecureToken($gadget);

      $result = OpenSocial_SecurityHelper::decrypt($token);

      $json = new NF_JSON();

      $values = $json->decode($result);

      $this->assertEqual($values->d, $domain);
      $this->assertEqual($values->v, $viewer);
      $this->assertEqual($values->o, $owner);
      $this->assertEqual($values->u, $url);
      $this->assertEqual($values->m, $index);
    }*/
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
