<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_PrivacyHelper.php');

/**
 * Tests for Index_PrivacyHelper.
 */
class Index_PrivacyHelperTest extends UnitTestCase {

    public function testSetPrivacySettings() {
        $widget = W_Cache::getWidget('main');

        $privacyLevel = $widget->config['appPrivacy'];
        $originalNonregVisibility = $widget->config['nonregVisibility'];
        $originalAllowInvites = $widget->config['allowInvites'];
        $originalAllowRequests = $widget->config['allowRequests'];
        $originalApproveMedia = $widget->config['moderate'];

        $form = array('privacyLevel' => 'public', 'nonregVisibility' => 'message', 'allowInvites' => 'yes', 'allowRequests' => null,
            'approveMedia' => 'yes', 'allowJoin' => 'all');
        Index_PrivacyHelper::setPrivacySettings($form);
        $this->assertEqual('message', $widget->config['nonregVisibility']);
        $this->assertEqual('yes', $widget->config['allowInvites']);
        $this->assertEqual('yes', $widget->config['allowRequests']);
        $this->assertEqual('yes', $widget->config['moderate']);

        $form = array('privacyLevel' => 'private', 'nonregVisibility' => 'homepage', 'allowInvites' => null, 'allowRequests' => 'yes',
            'approveMedia' => null, 'allowJoin' => 'all');
        Index_PrivacyHelper::setPrivacySettings($form);
        $this->assertEqual('message', $widget->config['nonregVisibility']);
        $this->assertEqual('no', $widget->config['allowInvites']);
        $this->assertEqual('yes', $widget->config['allowRequests']);
        $this->assertEqual('no', $widget->config['moderate']);

        // Now put everything back and check it is OK.  Public-related settings first.
        $form = array('privacyLevel' => 'public', 'nonregVisibility' => $originalNonregVisibility,
            'allowInvites' => ($originalAllowInvites === 'no' ? null : $originalAllowInvites),
            'allowRequests' => ($originalAllowRequests === 'no' ? null : $originalAllowRequests),
            'approveMedia' => ($originalApproveMedia === 'no' ? null : $originalApproveMedia),
            'allowJoin' => 'all');
        Index_PrivacyHelper::setPrivacySettings($form);
        $form['privacyLevel'] = 'private'; // and again to get the private network stuff in there.
        Index_PrivacyHelper::setPrivacySettings($form);
        $this->assertEqual($originalNonregVisibility, $widget->config['nonregVisibility']);
        $this->assertEqual($originalAllowInvites, $widget->config['allowInvites']);
        $this->assertEqual($originalAllowRequests, $widget->config['allowRequests']);
        $this->assertEqual($originalApproveMedia, $widget->config['moderate']);

        // And an invalid form should throw an exception.
        try {
            $form = array();
            Index_PrivacyHelper::setPrivacySettings($form);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
