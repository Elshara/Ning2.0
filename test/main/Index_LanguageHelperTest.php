<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_LanguageHelper.php');

class Index_LanguageHelperTest extends UnitTestCase {

    public function testMessagesSame() {
        $this->assertEqual(true, Index_LanguageHelper::messagesSame('a', 'a'));
        $this->assertEqual(true, Index_LanguageHelper::messagesSame(' a ', 'a'));
        $this->assertEqual(false, Index_LanguageHelper::messagesSame(' ab ', 'a'));
        $this->assertEqual(true, Index_LanguageHelper::messagesSame(" a\na ", 'aa'));
        $this->assertEqual(true, Index_LanguageHelper::messagesSame(" a\n\ra ", 'aa'));
        $this->assertEqual(true, Index_LanguageHelper::messagesSame(" a\ra ", 'aa'));
        $this->assertEqual(true, Index_LanguageHelper::messagesSame(" a\r\na ", 'aa'));
        $this->assertEqual(true, Index_LanguageHelper::messagesSame('', null));
        $this->assertEqual(true, Index_LanguageHelper::messagesSame(null, ''));
    }

    public function testValidate() {
        $this->assertEqual(array(), Index_LanguageHelper::validate('en_US', array()));
        $this->assertEqual(array(), Index_LanguageHelper::validate('en_US', array('FOO' => 'Foo')));
        $this->assertEqual(array(), Index_LanguageHelper::validate('en_US', array('xg.foo.nls.bar' => 'function() {}')));
        $this->assertEqual(array('xg.foo.nls.bar' => 'Mismatched curly brackets'), Index_LanguageHelper::validate('en_US', array('xg.foo.nls.bar' => 'function() {{}')));
        $this->assertEqual(array('xg.foo.nls.bar' => 'Mismatched parentheses'), Index_LanguageHelper::validate('en_US', array('xg.foo.nls.bar' => 'function() {(}')));
        $this->assertEqual(array('xg.foo.nls.bar' => 'Mismatched square brackets'), Index_LanguageHelper::validate('en_US', array('xg.foo.nls.bar' => 'function() {[}')));
        $this->assertEqual(array(), Index_LanguageHelper::validate('en_US', array('xg.foo.nls.bar' => 'function() {[]}')));
        $this->assertEqual(array(), Index_LanguageHelper::validate('en_US', array(Index_LanguageHelper::SPECIAL_RULES_KEY => '$a = 5+5;')));
        $errors = Index_LanguageHelper::validate('en_US', array(Index_LanguageHelper::SPECIAL_RULES_KEY => '$a = 5();'));
        $this->assertTrue(preg_match('@unexpected \'\(\'@', $errors[Index_LanguageHelper::SPECIAL_RULES_KEY]));
        $this->assertEqual(array(), Index_LanguageHelper::validate('en_US', array(Index_LanguageHelper::TAB_NAMES_KEY => '$a = 5+5;')));
        $errors = Index_LanguageHelper::validate('en_US', array(Index_LanguageHelper::TAB_NAMES_KEY => '$a = 5();'));
        $this->assertTrue(preg_match('@unexpected \'\(\'@', $errors[Index_LanguageHelper::TAB_NAMES_KEY]));
    }

    public function testDisplayName() {
        $this->assertEqual('FOO', Index_LanguageHelper::displayName('FOO'));
        $this->assertEqual('Special Rules', Index_LanguageHelper::displayName(Index_LanguageHelper::SPECIAL_RULES_KEY));
        $this->assertEqual('Tab Names', Index_LanguageHelper::displayName(Index_LanguageHelper::TAB_NAMES_KEY));
    }

    public function testMissingSpecialRulesNames() {
        $this->assertEqual('', implode(', ', TestLanguageHelper::missingSpecialRulesNames('', '')));
        $this->assertEqual('', implode(', ', TestLanguageHelper::missingSpecialRulesNames('
            if ($s == \'N_SECONDS_AGO\') { return self::pluralize($args[1], \'second\') . \' ago\'; }
            if ($s == \'N_MINUTES_AGO\') { return self::pluralize($args[1], \'minute\') . \' ago\'; }
            if ($s == \'N_HOURS_AGO\') { return self::pluralize($args[1], \'hour\') . \' ago\'; }
            ', '
            if ($s == \'N_SECONDS_AGO\') { return self::pluralize($args[1], \'second\') . \' ago\'; }
            if ($s == \'N_MINUTES_AGO\') { return self::pluralize($args[1], \'minute\') . \' ago\'; }
            if ($s == \'N_HOURS_AGO\') { return self::pluralize($args[1], \'hour\') . \' ago\'; }
            ')));
        $this->assertEqual('N_MINUTES_AGO, N_HOURS_AGO', implode(', ', TestLanguageHelper::missingSpecialRulesNames('
            if ($s == \'N_SECONDS_AGO\') { return self::pluralize($args[1], \'second\') . \' ago\'; }
            if ($s == \'N_MINUTES_AGO\') { return self::pluralize($args[1], \'minute\') . \' ago\'; }
            if ($s == \'N_HOURS_AGO\') { return self::pluralize($args[1], \'hour\') . \' ago\'; }
            ', '
            if ($s == "N_SECONDS_AGO") { return self::pluralize($args[1], \'second\') . \' ago\'; }
            ')));
    }

    public function testSpecialRulesNames() {
        $this->assertEqual('', implode(', ', TestLanguageHelper::specialRulesNames('')));
        $this->assertEqual('N_SECONDS_AGO, N_MINUTES_AGO, N_HOURS_AGO', implode(', ', TestLanguageHelper::specialRulesNames('
            if ($s == \'N_SECONDS_AGO\') { return self::pluralize($args[1], \'second\') . \' ago\'; }
            if ($s == \'N_MINUTES_AGO\') { return self::pluralize($args[1], \'minute\') . \' ago\'; }
            if ($s == \'N_HOURS_AGO\') { return self::pluralize($args[1], \'hour\') . \' ago\'; }')));
    }

    public function testMessagesProper() {
        $sourceData = array(
                '<Special Rules>' => 'Art',
                'STABS' => 'Stabs',
                'CAT_TAB_TEXT' => 'Cat');
        $targetData = array(
                '<Special Rules>' => 'Arte',
                'STABS' => 'Stabse',
                'CAT_TAB_TEXT' => 'Cate');
        $submittedMessages = array('STABS' => 'stabs');
        $submissionErrors = array('CAT_TAB_TEXT' => 'Must be more than 100 characters');
        $actualMessages = TestLanguageHelper::messagesProper($sourceData, $targetData, null, 'all', $submittedMessages, $submissionErrors);
        $expectedMessages = array (
                'STABS' => array (
                    'name' => 'STABS',
                    'sourceText' => 'Stabs',
                    'targetText' => 'stabs',
                    'changed' => true,
                    'missing' => false,
                    'rows' => 1),
                'CAT_TAB_TEXT' => array (
                    'name' => 'CAT_TAB_TEXT',
                    'sourceText' => 'Cat',
                    'targetText' => 'Cate',
                    'changed' => true,
                    'missing' => false,
                    'rows' => 1,
                    'isTabText' => true,
                    'errorMessage' => 'Must be more than 100 characters'),
                '<Special Rules>' => array (
                    'name' => '<Special Rules>',
                    'sourceText' => 'Art',
                    'targetText' => 'Arte',
                    'changed' => true,
                    'missing' => false,
                    'rows' => 1,
                    'wrap' => false),
                );
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode($expectedMessages), $json->encode($actualMessages));
        // echo '<pre>' . var_export($actualMessages, true) . '</pre>';
    }

    public function testMessagesProperWithChangedFilter() {
        $sourceData = array(
                '<Special Rules>' => 'Art',
                'STABS' => 'Stabs',
                'CAT_TAB_TEXT' => 'Cat');
        $targetData = array(
                '<Special Rules>' => 'Art',
                'STABS' => 'Stabse',
                'CAT_TAB_TEXT' => 'Cat');
        $actualMessages = TestLanguageHelper::messagesProper($sourceData, $targetData, null, 'changed', $submittedMessages, $submissionErrors);
        $expectedMessages = array (
                'STABS' => array (
                    'name' => 'STABS',
                    'sourceText' => 'Stabs',
                    'targetText' => 'Stabse',
                    'changed' => true,
                    'missing' => false,
                    'rows' => 1),
                );
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode($expectedMessages), $json->encode($actualMessages));
        // echo '<pre>' . var_export($actualMessages, true) . '</pre>';
    }

    public function testMessagesProperWithTabSearch() {
        $sourceData = array(
                '<Special Rules>' => 'Art',
                'STABS' => 'Stabs',
                'CAT_TAB_TEXT' => 'Cat');
        $targetData = array(
                '<Special Rules>' => 'Arte',
                'STABS' => 'Stabse',
                'CAT_TAB_TEXT' => 'Cate');
        $submittedMessages = array('STABS' => 'stabs');
        $submissionErrors = array('CAT_TAB_TEXT' => 'Must be more than 100 characters');
        $actualMessages = TestLanguageHelper::messagesProper($sourceData, $targetData, 'tabs', 'all', $submittedMessages, $submissionErrors);
        $expectedMessages = array (
                'CAT_TAB_TEXT' => array (
                    'name' => 'CAT_TAB_TEXT',
                    'sourceText' => 'Cat',
                    'targetText' => 'Cate',
                    'changed' => true,
                    'missing' => false,
                    'rows' => 1,
                    'isTabText' => true,
                    'errorMessage' => 'Must be more than 100 characters'),
                'STABS' => array (
                    'name' => 'STABS',
                    'sourceText' => 'Stabs',
                    'targetText' => 'stabs',
                    'changed' => true,
                    'missing' => false,
                    'rows' => 1),
                );
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->assertEqual($json->encode($expectedMessages), $json->encode($actualMessages));
        // echo '<pre>' . var_export($actualMessages, true) . '</pre>';
    }

}

class TestLanguageHelper extends Index_LanguageHelper {
    public static function missingSpecialRulesNames($sourceSpecialRulesCode, $targetSpecialRulesCode) {
        return parent::missingSpecialRulesNames($sourceSpecialRulesCode, $targetSpecialRulesCode);
    }
    public static function specialRulesNames($sourceSpecialRulesCode) {
        return parent::specialRulesNames($sourceSpecialRulesCode);
    }
    public static function messagesProper(&$sourceData, &$targetData, $searchText = null, $filter = 'all', &$submittedMessages = null, &$submissionErrors = array(), &$percentComplete = null) {
        return parent::messagesProper($sourceData, $targetData, $searchText, $filter, $submittedMessages, $submissionErrors, $percentComplete);
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
