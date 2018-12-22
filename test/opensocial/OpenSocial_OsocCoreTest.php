<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class OpenSocial_OsocCoreTest extends UnitTestCase {

    // this is a shindig constant that needs to be stripped off the response
    const UNPARSABLE_CRUFT = 'throw 1; < don\'t be evil\' >';
    
    
    public function testMakeRequest() {
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        $url = "http://" . $_SERVER["HTTP_HOST"] . "/test/opensocial/checkOauth.php";
        $response = XN_REST::post('http://' . OpenSocial_GadgetHelper::getOsocDomain() . '/gadgets/makeRequest', array('url' => $url,
                                                                                      'httpMethod' => 'POST',
                                                                                      'headers' => 'Content-Type=application/x-www-form-urlencoded',
                                                                                      'postData' => 'postvar=postdata',
                                                                                      'authz' => 'signed',
                                                                                      //If the core ever opens up the secure token, this test will fail
                                                                                      //and we'll need to generate it dynamically
                                                                                      'st' => 'e0d9ad848460f4fe2115a546deb853d7f7109cb0e6b54721b154ceb972b8d223c0cb3bf1787ff5ecb49db89399c3362607e2713b63abe48c83d47d1a32cf02c3ae3fb587e43382f706fa3b0532ab6a609d45047b2f0a6e6d3a49be909d0d55264da0a5fe101bec11ecb9244569ed1bbcb8eadeb506d25b00c33474438f4e1cee',
                                                                                      'domain' => $_SERVER['HTTP_HOST'],
                                                                                      'contentType' => 'JSON',
                                                                                      'numEntries' => '3',
                                                                                      'getSummaries' => 'false',
                                                                                      'signOwner' => 'true',
                                                                                      'signViewer' => 'true',
                                                                                      'gadget' => 'http://muckrake.net/~dkf/coderunner.xml',
                                                                                      'bypassSpecCache' => 1,
                                                                                      'viewerId' => XN_Profile::current()->screenName,
                                                                                      'ownerId' => XN_Profile::current()->screenName));
        //osoc's response has structure throw 1; < don\'t be evil\' >{'url': {body: whatever, rc: 200}}
        //checkOauth.php returns json as the body, so we have to decode that after decoding the outer json
        //trim off the beginning constant
        $trimmedResponse = mb_substr($response, mb_strlen(self::UNPARSABLE_CRUFT));
        $json = new NF_JSON();
        //decode the outer json
        $resultWrapped = $json->decode($trimmedResponse);
        //decode the body json
        $result = $json->decode($resultWrapped->$url->body);
        $this->assertEqual($result->validated, true, "makeRequest signature is not valid");
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
