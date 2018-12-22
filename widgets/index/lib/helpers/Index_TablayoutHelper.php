<?php

/**
 * Useful functions for working with navigation tabs.
 */
class Index_TablayoutHelper {

    /** tab implementation constants */
    const MAX_TABS = 14;
    const MAX_SUBTABS_PER_TAB = 10;
    const MAX_TABNAME_LENGTH = 12;

    public static $fixedTabsTop = array('main', 'invite', 'profile');
    public static $fixedTabsBottom = array('manage');

    /** Widget instance name for new pages. */
    const NEW_PAGE_INSTANCE = 'page';

    /** Singleton instance of this helper. */
    protected static $instance = NULL;

    /**
     * Returns the singleton instance of this helper.
     *
     * @return Index_TablayoutHelper  the singleton
     */
    public static function instance() {
        if (is_null(self::$instance)) { self::$instance = new Index_TablayoutHelper(); }
        return self::$instance;
    }

    /**
     * Format a navigation link for display
     *
     * @param       $tab        array       TabLayout Tab array
     * @param       $subTab     boolean     is the tab a subtab?
     * @return                  string      text of anchor link
     */
    public static function formatTabAnchor($tab, $subTab=false){
        $anchor .= '<a href="' . (mb_strlen($tab['url']) > 0 ? $tab['url'] : '#') . '"';
        if($tab['windowTarget']){
            $anchor .= ' target="' . $tab['windowTarget'] . '"';
        }
        if($subTab){
            $anchor .= ' style="float:none;"';
        }
        $anchor .= ">" . $tab['label'] . '</a>';
        return $anchor;
    }

    /**
     * Creates a Page within the "page" widget instance.
     *
     * @return string  the id of the newly created page
     */
    public function createPage($title) { // Instance method rather than static, for easier unit testing [Jon Aquino 2008-08-30]
        $page = $this->createPageProper($title);
        $page->save();
        return $page->id;
    }

    /**
     * Creates a Page within the "page" widget instance.
     *
     * @return Page  the unsaved page object
     */
    protected function createPageProper($title) { // Instance method rather than static, for easier unit testing [Jon Aquino 2008-08-30]
        $page = Page::create(Page::cleanTitle($title), '');
        $page->my->mozzle = self::NEW_PAGE_INSTANCE;
        return $page;
    }

    /**
     * Removes all tabs that reference the specified page id
     *
     * @param pageId string  the page id to search for
     */
    public static function removeTabsByPageId($pageId) {
        XG_App::includeFileOnce('/lib/XG_TabLayout.php');
        $layout = XG_TabLayout::loadOrCreate(false);
        if ($layout) {
            $numRemoved = 0;
            foreach ($layout->getTabsByPageId($pageId) as $tab) {
                // remove associated sub-tabs as well (BAZ-9506) [ywh 2008-09-01]
                $layout->removeTab($tab->tabKey, true);
                $numRemoved++;
            }
            if ($numRemoved > 0) {
                $layout->save();
            }
        }
    }

    /**
     * Creates a <style>...</style> block for the sub-tab colors.
     *
     * @param $subTabColors Array  Hex representations of the sub-tab colors (e.g. #ff0000). Keys are textColor, textColorHover, backgroundColor, and backgroundColorHover.
     * @return string  the HTML for the <style> block
     */
    public static function createInternalStyleSheet($subTabColors) {
        return '
<style type="text/css" media="screen,projection">
#xg_navigation ul div.xg_subtab ul li a {
    color:' . $subTabColors['textColor'] . ';
    background:' . $subTabColors['backgroundColor'] . ';
}
#xg_navigation ul div.xg_subtab ul li a:hover {
    color:' . $subTabColors['textColorHover'] . ';
    background:' . $subTabColors['backgroundColorHover'] . ';
}
</style>';
    }

}
