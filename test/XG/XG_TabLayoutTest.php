<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_TabLayout.php');

class XG_TabLayoutTest extends UnitTestCase {

    public function testConstructor() {
        // test tabs from constructor
        $layout = new XG_TabLayout(array(), array());
        $this->assertTrue(count($layout->getTabs()) === 0);
        $layout = new XG_TabLayout(array('foo' => new XG_Tab('foo', '/foo', 'Foo')), array());
        $this->assertTrue(count($layout->getTabs()) === 1);

        // test subTabColors from constructor
        $subTabColors1 = array('textColor' => 'red', 'textColorHover' => 'green', 'backgroundColor' => 'blue', 'backgroundColorHover' => 'yellow');
        $layout = new XG_TabLayout(array(), $subTabColors1);
        $this->assertEqual($layout->getSubTabColors(), $subTabColors1);
    }

    public function testCreateDefaultLayoutObject() {
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        $navEntries = XG_ModuleHelper::getNavEntriesFromWidgets(true);
        $layout = XG_TabLayout::createDefaultLayoutObject();
        $tabs = $layout->getTabs();
        foreach ($navEntries['tabs'] as $tab) {
            $this->assertTrue(array_key_exists($tab[2], $tabs));
            $this->assertEqual($tab[2], $tabs[$tab[2]]->tabKey);
        }

        // fixed tabs present?
        XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_TablayoutHelper.php');
        $fixedTabKeys = array_merge(Index_TablayoutHelper::$fixedTabsTop, Index_TablayoutHelper::$fixedTabsBottom);
        foreach ($fixedTabKeys as $tabKey) {
            $this->assertTrue(array_key_exists($tabKey, $tabs));
            $this->assertEqual($tabKey, $tabs[$tabKey]->tabKey);
        }

        // check all colors are present
        $subTabColors = $layout->getSubTabColors();
        foreach (array('textColor', 'textColorHover', 'backgroundColor', 'backgroundColorHover') as $colorKey) {
            $this->assertTrue(array_key_exists($colorKey, $subTabColors));
            $this->assertWantedPattern('/^#[a-fA-F0-9]{6}$/', $subTabColors[$colorKey]);
        }
    }

    public function testIsEnabled() {
        $main = W_Cache::getWidget('main');
        unset($main->config[XG_TabLayout::TAB_MANAGER_DISABLE_KEY]);
        $this->assertTrue(XG_TabLayout::isEnabled($main));
        $main->config[XG_TabLayout::TAB_MANAGER_DISABLE_KEY] = true;
        $this->assertFalse(XG_TabLayout::isEnabled($main));
        $main->config[XG_TabLayout::TAB_MANAGER_DISABLE_KEY] = false;
        $this->assertTrue(XG_TabLayout::isEnabled($main));
    }

    public function testLoadOrCreate() {
        $main = W_Cache::getWidget('main');
        // test createIfNecessary == false
        unset($main->config[XG_TabLayout::SITE_TAB_LAYOUT_KEY]);
        $this->assertEqual(XG_TabLayout::loadOrCreate(false), null);

        // test createIfNecessary == true (should return the default layout)
        $layout = XG_TabLayout::loadOrCreate();
        $this->assertEqual($layout, XG_TabLayout::createDefaultLayoutObject());

        // test load (data is in config)
        $main->config[XG_TabLayout::SITE_TAB_LAYOUT_KEY] = serialize(array('tabs' => $layout->getTabs(), 'subTabColors' => $layout->getSubTabColors()));
        $layout2 = XG_TabLayout::loadOrCreate(false);
        $this->assertFalse($layout2 === null);
        $this->assertEqual($layout->getTabs(), $layout2->getTabs());
        $this->assertEqual($layout->getSubTabColors(), $layout2->getSubTabColors());
    }

    public function testAddTab() {
        $layout = new XG_TabLayout(array(), array());
        $this->assertFalse($layout->hasTab('foo'));
        $layout->addTab('foo', '/foo', 'Foo');
        $this->assertTrue($layout->hasTab('foo'));
        $this->assertEqual($layout->getTab('foo'), new XG_Tab('foo', '/foo', 'Foo'));

        // test key collision without replace
        try {
            // should throw an exception
            $layout->addTab('foo', '/foo2', 'Foo');
            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->assertWantedPattern('/already exists/', $e->getMessage());
        }
        // verify we did not override the existing tab
        $this->assertEqual($layout->getTab('foo')->url, '/foo');

        // test key collision with replace
        $layout->addTab('foo', '/foo2', 'Foo2', XG_Tab::TAB_VISIBILITY_ALL, null, false, null, true);
        $this->assertEqual($layout->getTab('foo')->url, '/foo2');
    }

    public function testAddTabAfter() {
        $layout = new XG_TabLayout(array(), array());
        $layout->addTab('foo', '/foo', 'Foo');
        $layout->addTabAfter('foo', 'foo2', '/foo2', 'Foo2');
        // check addition
        $this->assertEqual(count($layout->getTabs()), 2);
        // check ordering
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'foo,foo2');

        // check bad reference tab
        try {
            $layout->addTabAfter('foo3', 'foo4', '/foo4', 'Foo4');
            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->assertWantedPattern('/reference tab key does not exist/', $e->getMessage());
        }
        // check we did not add anything for bad reference tab
        $this->assertEqual(count($layout->getTabs()), 2);

        // check key collision
        try {
            $layout->addTabAfter('foo2', 'foo', '/fooNew', 'FooNew');
            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->assertWantedPattern('/already exists/', $e->getMessage());
        }
        // verify we did not override the existing tab
        $this->assertEqual($layout->getTab('foo')->url, '/foo');

        // test key collision with replace
        $layout->addTabAfter('foo2', 'foo', '/fooNew', 'FooNew', XG_Tab::TAB_VISIBILITY_ALL, null, false, null, true);
        $this->assertEqual(count($layout->getTabs()), 2);
        // check replacement
        $this->assertEqual($layout->getTab('foo')->url, '/fooNew');
        // check ordering
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'foo2,foo');
    }

    public function testInsertTabBefore() {
        $layout = new XG_TabLayout(array(), array());
        $layout->addTab('foo', '/foo', 'Foo');
        $layout->insertTabBefore('foo', 'foo2', '/foo2', 'Foo2');
        // check addition
        $this->assertEqual(count($layout->getTabs()), 2);
        // check ordering
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'foo2,foo');

        // check bad reference tab
        try {
            $layout->insertTabBefore('foo3', 'foo4', '/foo4', 'Foo4');
            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->assertWantedPattern('/reference tab key does not exist/', $e->getMessage());
        }
        // check we did not add anything for bad reference tab
        $this->assertEqual(count($layout->getTabs()), 2);

        // check key collision
        try {
            $layout->insertTabBefore('foo2', 'foo', '/fooNew', 'FooNew');
            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->assertWantedPattern('/already exists/', $e->getMessage());
        }
        // verify we did not override the existing tab
        $this->assertEqual($layout->getTab('foo')->url, '/foo');

        // test key collision with replace
        $layout->insertTabBefore('foo2', 'foo', '/fooNew', 'FooNew', XG_Tab::TAB_VISIBILITY_ALL, null, false, null, true);
        $this->assertEqual(count($layout->getTabs()), 2);
        // check replacement
        $this->assertEqual($layout->getTab('foo')->url, '/fooNew');
        // check ordering
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'foo,foo2');
    }

    public function testHasTab() {
        $layout = new XG_TabLayout(array(), array());
        $this->assertFalse($layout->hasTab('foo'));
        $layout->addTab('foo', '/foo', 'Foo');
        $this->assertTrue($layout->hasTab('foo'));
        $layout->removeTab('foo');
        $this->assertFalse($layout->hasTab('foo'));
    }

    private function _ghettoClone($object) {
        return unserialize(serialize($object));
    }

    public function testRemoveTab() {
        // ascii art skillz++
        // foo
        //  |
        //  +-- foo1
        //  |
        //  `-- foo2
        //
        // bar
        $tabs = array('foo' => new XG_Tab('foo', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, false, null),
                      'foo1' => new XG_Tab('foo1', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, true, null),
                      'foo2' => new XG_Tab('foo2', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, true, null),
                      'bar' => new XG_Tab('bar', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, false, null));

        // remove tab, delete subtabs
        $layout = new XG_TabLayout(self::_ghettoClone($tabs), array());
        $layout->removeTab('foo', true);
        $this->assertEqual(count($layout->getTabs()), 1);
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'bar');

        // remove tab, promote subtabs
        $layout = new XG_TabLayout(self::_ghettoClone($tabs), array());
        $layout->removeTab('foo', false, true);
        $this->assertEqual(count($layout->getTabs()), 3);
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'foo1,foo2,bar');
        $this->assertFalse($layout->getTab('foo1')->isSubTab);
        $this->assertFalse($layout->getTab('foo2')->isSubTab);

        // remove tab, subtabs unchanged but will not show in layout (invalid state)
        $layout = new XG_TabLayout(self::_ghettoClone($tabs), array());
        $layout->removeTab('foo');
        $this->assertEqual(count($layout->getTabs()), 1);
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'bar');

        // remove tab subtabs unchanged in valid state
        $layout = new XG_TabLayout(self::_ghettoClone($tabs), array());
        $layout->insertTabBefore('foo', 'top', '', '');
        $layout->removeTab('foo');
        $this->assertEqual(count($layout->getTabs()), 4);
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'top,foo1,foo2,bar');
        $this->assertTrue($layout->getTab('foo1')->isSubTab);
        $this->assertTrue($layout->getTab('foo2')->isSubTab);

        // remove non-existent tab
        $layout = new XG_TabLayout(self::_ghettoClone($tabs), array());
        try {
            $layout->removeTab('foobar');
            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->assertWantedPattern('/does not exist/', $e->getMessage());
        }
        $this->assertEqual(count($layout->getTabs()), 4);
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'foo,foo1,foo2,bar');
    }

    public function testUpdateTab() {
        $layout = new XG_TabLayout(array(), array());

        // test update non-existent tab with create === false
        try {
            $layout->updateTab('foo', '/foo', 'Foo', XG_Tab::TAB_VISIBILITY_ALL, null, false, null);
            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->assertWantedPattern('/does not exist/', $e->getMessage());
        }
        $this->assertEqual(count($layout->getTabs()), 0);

        // test update existing tab
        $layout->addTab('foo', '/foo', 'Foo');
        $layout->updateTab('foo', '/fooNew', 'FooNew', XG_Tab::TAB_VISIBILITY_ALL, null, false, null);
        $this->assertEqual(count($layout->getTabs()), 1);
        $this->assertEqual($layout->getTab('foo')->url, '/fooNew');

        // test update non-existent tab with create === true
        $layout->updateTab('bar', '/bar', 'Bar', XG_Tab::TAB_VISIBILITY_ALL, null, false, null, true);
        $this->assertEqual(count($layout->getTabs()), 2);
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'foo,bar');
    }

    public function testSave() {
        $config = W_Cache::getWidget('main')->config;
        if (array_key_exists(XG_TabLayout::SITE_TAB_LAYOUT_KEY, $config)) {
            $originalValue = $config[XG_TabLayout::SITE_TAB_LAYOUT_KEY];
        } else {
            $originalValue = null;
        }

        $layout = new XG_TabLayout(array(), array());
        $label = 'Foo & &amp; &eacute; é 很好！';
        $layout->addTab('foo', '/foo', $label);
        $serData = serialize(array('tabs' => $layout->getTabs(), 'subTabColors' => array()));
        $layout->save();
        $this->assertEqual($serData, W_Cache::getWidget('main')->config[XG_TabLayout::SITE_TAB_LAYOUT_KEY]);

        $layout2 = XG_TabLayout::loadOrCreate();
        $this->assertEqual($layout, $layout2);
        $this->assertEqual($layout2->getTab('foo')->label, $label);

        // restore site value
        $main = W_Cache::getWidget('main');
        if (is_null($originalValue)) {
            unset($main->config[XG_TabLayout::SITE_TAB_LAYOUT_KEY]);
        } else {
            $main->config[XG_TabLayout::SITE_TAB_LAYOUT_KEY] = $originalValue;
        }
        $main->saveConfig();
    }

    public function testUpdateFromArray() {
        $layout = new XG_TabLayout(array(), array());
        $tabArray = array(array('tabKey' => 'foo', 'url' => '/foo', 'label' => 'Foo', 'visibility' => XG_Tab::TAB_VISIBILITY_ALL, 'windowTarget' => null, 'isSubTab' => false),
                          array('tabKey' => '', 'url' => '/new', 'label' => 'New', 'visibility' => XG_Tab::TAB_VISIBILITY_ALL, 'windowTarget' => null, 'isSubTab' => false),
                          array('tabKey' => 'bar', 'url' => '/bar', 'label' => '', 'visibility' => XG_Tab::TAB_VISIBILITY_ALL, 'windowTarget' => null, 'isSubTab' => false));
        $layout->updateFromArray($tabArray);
        $this->assertEqual(count($layout->getTabs()), 2);
        $this->assertEqual(implode(',', array_keys($layout->getTabs())), 'foo,xn0');
        $this->assertEqual($layout->getTab('foo')->url, '/foo');
        $this->assertEqual($layout->getTab('xn0')->url, '/new');
    }

    public function testGetNestedTabStructure() {
        $tabs = array('foo' => new XG_Tab('foo', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, false, null),
                      'foo1' => new XG_Tab('foo1', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, true, null),
                      'foo2' => new XG_Tab('foo2', '', '', XG_Tab::TAB_VISIBILITY_MEMBER, null, true, null),
                      'foo3' => new XG_Tab('foo3', '', '', XG_Tab::TAB_VISIBILITY_ADMIN, null, true, null),
                      'bar' => new XG_Tab('bar', '', '', XG_Tab::TAB_VISIBILITY_ADMIN, null, false, null),
                      'bar1' => new XG_Tab('bar1', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, true, null),
                      'bar2' => new XG_Tab('bar2', '', '', XG_Tab::TAB_VISIBILITY_MEMBER, null, true, null),
                      'foobar' => new XG_Tab('foobar', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, false, null));

        $layout = new XG_TabLayout($tabs, array());

        // test user visibility == all
        $testTabs = $layout->getNestedTabStructure(XG_Tab::TAB_VISIBILITY_ALL);
        $this->assertEqual(count($testTabs), 2);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foobar');
        $this->assertEqual(count($testTabs['foo']['subTabs']), 1);
        $this->assertEqual(implode(',', array_keys($testTabs['foo']['subTabs'])), 'foo1');
        $this->assertEqual(count($testTabs['foobar']['subTabs']), 0);

        // test user visibility == member
        $testTabs = $layout->getNestedTabStructure(XG_Tab::TAB_VISIBILITY_MEMBER);
        $this->assertEqual(count($testTabs), 2);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foobar');
        $this->assertEqual(count($testTabs['foo']['subTabs']), 2);
        $this->assertEqual(implode(',', array_keys($testTabs['foo']['subTabs'])), 'foo1,foo2');
        $this->assertEqual(count($testTabs['foobar']['subTabs']), 0);

        // test user visibility == admin
        $testTabs = $layout->getNestedTabStructure(XG_Tab::TAB_VISIBILITY_ADMIN);
        $this->assertEqual(count($testTabs), 3);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,bar,foobar');
        $this->assertEqual(count($testTabs['foo']['subTabs']), 3);
        $this->assertEqual(implode(',', array_keys($testTabs['foo']['subTabs'])), 'foo1,foo2,foo3');
        $this->assertEqual(count($testTabs['bar']['subTabs']), 2);
        $this->assertEqual(implode(',', array_keys($testTabs['bar']['subTabs'])), 'bar1,bar2');
        $this->assertEqual(count($testTabs['foobar']['subTabs']), 0);

        // test max top level tabs
        $testTabs = $layout->getNestedTabStructure(XG_Tab::TAB_VISIBILITY_ADMIN, 1);
        $this->assertEqual(count($testTabs), 1);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo');
        $this->assertEqual(count($testTabs['foo']['subTabs']), 3);
        $this->assertEqual(implode(',', array_keys($testTabs['foo']['subTabs'])), 'foo1,foo2,foo3');

        // test max subtabs
        $testTabs = $layout->getNestedTabStructure(XG_Tab::TAB_VISIBILITY_ADMIN, null, 1);
        $this->assertEqual(count($testTabs), 3);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,bar,foobar');
        $this->assertEqual(count($testTabs['foo']['subTabs']), 1);
        $this->assertEqual(implode(',', array_keys($testTabs['foo']['subTabs'])), 'foo1');
        $this->assertEqual(count($testTabs['bar']['subTabs']), 1);
        $this->assertEqual(implode(',', array_keys($testTabs['bar']['subTabs'])), 'bar1');
        $this->assertEqual(count($testTabs['foobar']['subTabs']), 0);

        // test fixedTabsTop and maxtabs
        $testTabs = $layout->getNestedTabStructure(XG_Tab::TAB_VISIBILITY_ADMIN, 2, null, array('foobar'));
        $this->assertEqual(count($testTabs), 2);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foobar,foo');
        $this->assertEqual(count($testTabs['foobar']['subTabs']), 0);
        $this->assertEqual(count($testTabs['foo']['subTabs']), 3);
        $this->assertEqual(implode(',', array_keys($testTabs['foo']['subTabs'])), 'foo1,foo2,foo3');

        // test fixedTabsBottom and maxtabs
        $testTabs = $layout->getNestedTabStructure(XG_Tab::TAB_VISIBILITY_ADMIN, 2, null, array(), array('bar'));
        $this->assertEqual(count($testTabs), 2);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,bar');
        $this->assertEqual(count($testTabs['foo']['subTabs']), 3);
        $this->assertEqual(implode(',', array_keys($testTabs['foo']['subTabs'])), 'foo1,foo2,foo3');
        $this->assertEqual(count($testTabs['bar']['subTabs']), 2);
        $this->assertEqual(implode(',', array_keys($testTabs['bar']['subTabs'])), 'bar1,bar2');

        // test with fixedTabsTop/Bottom, and maxtabs
        $testTabs = $layout->getNestedTabStructure(XG_Tab::TAB_VISIBILITY_ADMIN, 2, 1, array('foobar'), array('bar'));
        $this->assertEqual(count($testTabs), 2);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foobar,bar');
        $this->assertEqual(count($testTabs['foobar']['subTabs']), 0);
        $this->assertEqual(count($testTabs['bar']['subTabs']), 1);
        $this->assertEqual(implode(',', array_keys($testTabs['bar']['subTabs'])), 'bar1');
    }

    public function testGetVisibleTabs() {
        $tabs = array('foo' => new XG_Tab('foo', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, false, null),
                      'foo1' => new XG_Tab('foo1', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, true, null),
                      'foo2' => new XG_Tab('foo2', '', '', XG_Tab::TAB_VISIBILITY_MEMBER, null, true, null),
                      'foo3' => new XG_Tab('foo3', '', '', XG_Tab::TAB_VISIBILITY_ADMIN, null, true, null),
                      'bar' => new XG_Tab('bar', '', '', XG_Tab::TAB_VISIBILITY_ADMIN, null, false, null),
                      'bar1' => new XG_Tab('bar1', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, true, null),
                      'bar2' => new XG_Tab('bar2', '', '', XG_Tab::TAB_VISIBILITY_MEMBER, null, true, null),
                      'foobar' => new XG_Tab('foobar', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, false, null));

        $layout = new XG_TabLayout($tabs, array());

        // check vis all
        $testTabs = $layout->getVisibleTabs(XG_Tab::TAB_VISIBILITY_ALL);
        $this->assertEqual(count($testTabs), 3);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,foobar');

        // check vis member
        $testTabs = $layout->getVisibleTabs(XG_Tab::TAB_VISIBILITY_MEMBER);
        $this->assertEqual(count($testTabs), 4);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,foo2,foobar');

        // check vis admin
        $testTabs = $layout->getVisibleTabs(XG_Tab::TAB_VISIBILITY_ADMIN);
        $this->assertEqual(count($testTabs), 8);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,foo2,foo3,bar,bar1,bar2,foobar');

        // check vis admin, onlyTopLevel==true
        $testTabs = $layout->getVisibleTabs(XG_Tab::TAB_VISIBILITY_ADMIN, true);
        $this->assertEqual(count($testTabs), 3);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,bar,foobar');

        // check vis admin, maxSubTabsPerTab
        $testTabs = $layout->getVisibleTabs(XG_Tab::TAB_VISIBILITY_ADMIN, false, 1);
        $this->assertEqual(count($testTabs), 5);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,bar,bar1,foobar');
    }

    public function testGetTabs() {
        $tabs = array('foo' => new XG_Tab('foo', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, false, null),
                      'foo1' => new XG_Tab('foo1', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, true, null),
                      'foo2' => new XG_Tab('foo2', '', '', XG_Tab::TAB_VISIBILITY_MEMBER, null, true, null),
                      'foo3' => new XG_Tab('foo3', '', '', XG_Tab::TAB_VISIBILITY_ADMIN, null, true, null),
                      'bar' => new XG_Tab('bar', '', '', XG_Tab::TAB_VISIBILITY_ADMIN, null, false, null),
                      'bar1' => new XG_Tab('bar1', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, true, null),
                      'bar2' => new XG_Tab('bar2', '', '', XG_Tab::TAB_VISIBILITY_MEMBER, null, true, null),
                      'foobar' => new XG_Tab('foobar', '', '', XG_Tab::TAB_VISIBILITY_ALL, null, false, null));

        $layout = new XG_TabLayout($tabs, array());

        // test user visibility == all
        $testTabs = $layout->getTabs(XG_Tab::TAB_VISIBILITY_ALL);
        $this->assertEqual(count($testTabs), 3);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,foobar');

        // test user visibility == member
        $testTabs = $layout->getTabs(XG_Tab::TAB_VISIBILITY_MEMBER);
        $this->assertEqual(count($testTabs), 4);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,foo2,foobar');

        // test user visibility == admin
        $testTabs = $layout->getTabs(XG_Tab::TAB_VISIBILITY_ADMIN);
        $this->assertEqual(count($testTabs), 8);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,foo2,foo3,bar,bar1,bar2,foobar');

        // test max top level tabs
        $testTabs = $layout->getTabs(XG_Tab::TAB_VISIBILITY_ADMIN, 1);
        $this->assertEqual(count($testTabs), 4);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,foo2,foo3');

        // test max subtabs
        $testTabs = $layout->getTabs(XG_Tab::TAB_VISIBILITY_ADMIN, null, 1);
        $this->assertEqual(count($testTabs), 5);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,bar,bar1,foobar');

        // test fixedTabsTop and maxtabs
        $testTabs = $layout->getTabs(XG_Tab::TAB_VISIBILITY_ADMIN, 2, null, array('foobar'));
        $this->assertEqual(count($testTabs), 5);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foobar,foo,foo1,foo2,foo3');

        // test fixedTabsBottom and maxtabs
        $testTabs = $layout->getTabs(XG_Tab::TAB_VISIBILITY_ADMIN, 2, null, array(), array('bar'));
        $this->assertEqual(count($testTabs), 7);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foo,foo1,foo2,foo3,bar,bar1,bar2');

        // test with fixedTabsTop/Bottom, and maxtabs
        $testTabs = $layout->getTabs(XG_Tab::TAB_VISIBILITY_ADMIN, 2, 1, array('foobar'), array('bar'));
        $this->assertEqual(count($testTabs), 3);
        $this->assertEqual(implode(',', array_keys($testTabs)), 'foobar,bar,bar1');
    }

    public function testSetSubTabColors() {
        $layout = new XG_TabLayout(array(), array());
        $this->assertEqual($layout->getSubTabColors(), array());
        $subTabColors = array('textColor' => '#FF0000', 'textColorHover' => '#00FF00', 'backgroundColor' => '#0000FF', 'backgroundColorHover' => '#000000');
        $layout->setSubTabColors($subTabColors);
        $this->assertEqual($layout->getSubTabColors(), $subTabColors);
    }

    public function testGetTab() {
        $layout = new XG_TabLayout(array(), array());
        $this->assertEqual($layout->getTab('foo'), null);
        $layout->addTab('foo', '', '');
        $this->assertEqual($layout->getTab('foo')->tabKey, 'foo');
    }

    public function testGetNextNumericTabKey() {
        $layout = new XG_TabLayout(array(), array());
        $layout->addTab('xn62', '/foo', 'Foo');
        $this->assertEqual($layout->getNextNumericTabKey(), 63);
    }

    public function testGetTabsByPageId() {
        $layout = new XG_TabLayout(array(), array());
        $layout->addTab('foo', '/foo', 'Foo', XG_Tab::TAB_VISIBILITY_ALL, null, false, '123:Page:456');
        $this->assertEqual($layout->getTabsByPageId('123:Page:456'), array($layout->getTab('foo')));
        $this->assertEqual($layout->getTabsByPageId('123:Page:457'), array());
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
