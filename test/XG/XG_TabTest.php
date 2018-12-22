<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_TabLayout.php');

class XG_TabTest extends UnitTestCase {

    public function testConstructor() {
        // test defaults
        $tab = new XG_Tab('test', '/test', 'Test');
        $this->assertEqual($tab->tabKey, 'test');
        $this->assertEqual($tab->url, '/test');
        $this->assertEqual($tab->label, 'Test');
        $this->assertEqual($tab->visibility, XG_Tab::TAB_VISIBILITY_ALL);
        $this->assertEqual($tab->windowTarget, null);
        $this->assertEqual($tab->isSubTab, false);
        $this->assertEqual($tab->pageId, null);

        // test overriding defaults
        $tab = new XG_Tab('test2', '/test2', 'Test2', XG_Tab::TAB_VISIBILITY_MEMBER, 'target', true, '123:Page:123');
        $this->assertEqual($tab->tabKey, 'test2');
        $this->assertEqual($tab->url, '/test2');
        $this->assertEqual($tab->label, 'Test2');
        $this->assertEqual($tab->visibility, XG_Tab::TAB_VISIBILITY_MEMBER);
        $this->assertEqual($tab->windowTarget, 'target');
        $this->assertEqual($tab->isSubTab, true);
        $this->assertEqual($tab->pageId, '123:Page:123');
    }

    public function testIsVisible() {
        $allVisibilities = array('invalid', XG_Tab::TAB_VISIBILITY_ALL, XG_Tab::TAB_VISIBILITY_MEMBER, XG_Tab::TAB_VISIBILITY_ADMIN);
        $expect = array('invalid' => array('invalid' => false, XG_Tab::TAB_VISIBILITY_ALL => false, XG_Tab::TAB_VISIBILITY_MEMBER => false, XG_Tab::TAB_VISIBILITY_ADMIN => false),
                        XG_Tab::TAB_VISIBILITY_ALL => array('invalid' => false, XG_Tab::TAB_VISIBILITY_ALL => true, XG_Tab::TAB_VISIBILITY_MEMBER => true, XG_Tab::TAB_VISIBILITY_ADMIN => true),
                        XG_Tab::TAB_VISIBILITY_MEMBER => array('invalid' => false, XG_Tab::TAB_VISIBILITY_ALL => false, XG_Tab::TAB_VISIBILITY_MEMBER => true, XG_Tab::TAB_VISIBILITY_ADMIN => true),
                        XG_Tab::TAB_VISIBILITY_ADMIN => array('invalid' => false, XG_Tab::TAB_VISIBILITY_ALL => false, XG_Tab::TAB_VISIBILITY_MEMBER => false, XG_Tab::TAB_VISIBILITY_ADMIN => true));
        foreach ($allVisibilities as $tabVisibility) {
            $tab = new XG_Tab('test', '/test', 'Test', $tabVisibility);
            foreach ($allVisibilities as $userVisibility) {
                $this->assertEqual($tab->isVisible($userVisibility), $expect[$tab->visibility][$userVisibility], 'Tab visibility [' . $tab->visibility . '], User visibility status [' . $userVisibility . ']; expecting [' . $expect[$tab->visibility][$userVisibility] . '] but got [' . $tab->isVisible($userVisibility) . ']');
            }
        }
    }

    public function testToArray() {
        $tab = new XG_Tab('test', '/test', 'Test', XG_Tab::TAB_VISIBILITY_MEMBER, 'target', true, '123:Page:123');
        $tabArray = $tab->toArray();
        $this->assertEqual($tab->tabKey, $tabArray['tabKey']);
        $this->assertEqual($tab->url, $tabArray['url']);
        $this->assertEqual($tab->label, $tabArray['label']);
        $this->assertEqual($tab->visibility, $tabArray['visibility']);
        $this->assertEqual($tab->windowTarget, $tabArray['windowTarget']);
        $this->assertEqual($tab->isSubTab, $tabArray['isSubTab']);
        $this->assertEqual($tab->pageId, $tabArray['pageId']);
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
