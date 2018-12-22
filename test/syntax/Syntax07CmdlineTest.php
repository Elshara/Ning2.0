<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax07CmdlineTest extends CmdlineTestCase {

    /**
     * Test that when accessing certain user properties the appropriate accessor methods are used and not the properties themselves.
     */
    public function testUseXgUserHelperAccessors() {
        foreach (array('fullName', 'gender', 'birthdate', 'location', 'country', 'thumbnailUrl', 'age') as $fieldName) {
            foreach (XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
                if (strpos($file, 'test/') !== false) { continue; }
                if (strpos($file, '/lib/scripts/eoc167a.php') !== false) { continue; }
                if (strpos($file, 'XG_UserHelper.php') !== false) { continue; }
                if (strpos($file, 'InvitationController.php') !== false) { continue; }
                if (strpos($file, 'admin/controllers/IndexController.php') !== false) { continue; }
                $contents = self::getFileContent($file);
                $contents = str_replace("query->filter('my->location'", '', $contents);
                $contents = str_replace('$this->fullName', '', $contents);
                $contents = str_replace('\'my->fullName\'', '', $contents);
                $contents = str_replace('event->my->location', '', $contents);
                $contents = str_replace('group->my->location', '', $contents);
                $contents = str_replace('photo->my->location', '', $contents);
                $contents = str_replace('video->my->location', '', $contents);
                $contents = str_replace('$this->location', '', $contents);
                $contents = str_replace('embership->my->fullName', '', $contents);
                if (strpos($file, 'XG_Message.php') !== false) { $contents = str_replace('$fullName = $profile->fullName;', '', $contents); }
                if (strpos($contents, '->' . $fieldName) === false) { continue; }
                $i = 0;
                foreach (explode("\n", $contents) as $line) {
                    $i++;
                    if (strpos($line, '$profile->birthdate = $birthdate') !== false && strpos($file, 'AuthorizationController.php')) { continue; }
                    if (strpos($line, '[skip-Syntax7Test]') !== false) { continue; }
                    if (strpos($line, ', ! $profile->') !== false) { continue; }
                    if (strpos($line, 'Anyway, just use my->fullName') !== false) { continue; }
                    if (strpos($line, ' my->fullName') !== false) { continue; }
                    if (strpos($line, '$profile->fullName == $profile->screenName || ! mb_strlen($profile->fullName)') !== false) { continue; }
                    if (strpos($line, 'to match XN_Profile->fullName max') !== false) { continue; }
                    if (strpos($line, '// Matches XN_Profile->location max') !== false) { continue; }
                    if (strpos($line, '->filter(\'my->location\', \'=\', $location)') !== false) { continue; }
                    if (strpos($line, 'XG_UserHelper::set') !== false) { continue; }
                    $this->assertTrue(strpos($line, '->' . $fieldName) === false || ! preg_match('@->' . $fieldName . '\b@', $line), $fieldName . ': ' . $this->escape($line) . ' - ' . $file . ' line ' . $i);
                }
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
