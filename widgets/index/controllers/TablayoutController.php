<?php

/**
 * Dispatches requests pertaining to the Tab Manager.
 */
class Index_TablayoutController extends W_Controller {

    protected function _before() {
        XG_SecurityHelper::redirectIfNotAdmin();
        XG_App::includeFileOnce('/lib/XG_TabLayout.php');
        $this->_widget->includeFileOnce('/lib/helpers/Index_TablayoutHelper.php');
    }

    /** public actions */
    public function action_index() {
        $this->redirectTo('edit');
    }

    /**
     * Shows the tab manager form for editing site tab layouts
     *
     * GET parameters:
     * - success integer  if 1, displays success banner for saving changes
     */
    public function action_edit() {
        $this->tabLayout = XG_TabLayout::loadOrCreate();
        $this->subTabColors = $this->tabLayout->getSubTabColors();
        $this->restrictedTabKeys = array_merge(Index_TablayoutHelper::$fixedTabsTop, Index_TablayoutHelper::$fixedTabsBottom);
        $this->maxTabs = Index_TablayoutHelper::MAX_TABS;
        $this->maxSubTabsPerTab = Index_TablayoutHelper::MAX_SUBTABS_PER_TAB;
        $this->maxTabLength = Index_TablayoutHelper::MAX_TABNAME_LENGTH;
        $this->reset = $_GET['reset'];
    }

    /**
     * Creates a new page. Returns $this->url with URL to a new page and $this->pageId
     * with the id of the new page.
     *
     * Expected GET parameters:
     *  @param      xn_out   string             Must be 'json'
     *
     * Expected POST parameters:
     *  @param      $title   string		Page title
     *
     *  @return     void
     */
    public function action_createPage() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') return;
        $this->pageId = Index_TablayoutHelper::instance()->createPage($_POST['title']);
		$this->editUrl = W_Cache::getWidget(Index_TablayoutHelper::NEW_PAGE_INSTANCE)->buildUrl('page', 'edit', array('id' => $this->pageId));
		$this->url = W_Cache::getWidget(Index_TablayoutHelper::NEW_PAGE_INSTANCE)->buildUrl('page', 'show', array('id' => $this->pageId));
    }

    /**
     * Updates the site tab structure
     *
     * POST parameters:
     * - layoutJson string  The new layout structure: [{url,label,isSubTab,tabKey,windowTarget,visibility}]
     * - textColor string  html representation of color, i.e. #ff0000, for drop down text
     * - textColorHover string  html representation of color for down down text on hover
     * - backgroundColor string  html representation of background color for the drop down menu
     * - backgroundColorHover string  html representation of background color on hover for the drop down menu
     */
    public function action_update() {
	if ($_SERVER['REQUEST_METHOD'] != 'POST') return;
        $restrictedTabKeys = array_merge(Index_TablayoutHelper::$fixedTabsTop, Index_TablayoutHelper::$fixedTabsBottom);
        $tabLayout = XG_TabLayout::loadOrCreate();

        $tabData = array_values(json_decode($_POST['layoutJson'], true));
        $numTabs = count($tabData);
        for ($i = 0; $i < $numTabs; $i++) {
            $label = trim($tabData[$i]['label']);
            // truncate labels to MAX_TABNAME_LENGTH
            if (mb_strlen($label) > Index_TablayoutHelper::MAX_TABNAME_LENGTH) {
                $label = mb_substr($label, 0, Index_TablayoutHelper::MAX_TABNAME_LENGTH);
            }
            $tabData[$i]['label'] = $label;

            // restricted tabs can only change the label.. this is to prevent hacks
            $tabKey = $tabData[$i]['tabKey'];
            if (strlen($tabKey) && in_array($tabKey, $restrictedTabKeys)) {     /** @non-mb */
                $tab = $tabLayout->getTab($tabKey);
                if (! is_null($tab)) {
                    // preserve original values
                    $tabData[$i]['url'] = $tab->url;
                    $tabData[$i]['visibility'] = $tab->visibility;
                    $tabData[$i]['windowTarget'] = $tab->windowTarget;
                    $tabData[$i]['isSubTab'] = $tab->isSubTab;
                }
            } else {
                // some tabs may be sent with createPage === true; override the url as it will be blank
                if (array_key_exists('createPage', $tabData[$i]) && $tabData[$i]['createPage']) {
                    $tabData[$i]['tabPageId'] = Index_TablayoutHelper::instance()->createPage($tabData[$i]['label']);
		    $tabData[$i]['url'] = W_Cache::getWidget(Index_TablayoutHelper::NEW_PAGE_INSTANCE)->buildUrl('page', 'show', array('id' => $tabData[$i]['tabPageId']));
                }
            }
        }

        $tabLayout->updateFromArray($tabData);
        $tabLayout->setSubTabColors(array(
                        'textColor' => $_POST['textColor'],
                        'textColorHover' => $_POST['textColorHover'],
                        'backgroundColor' => $_POST['backgroundColor'],
                        'backgroundColorHover' => $_POST['backgroundColorHover']));
        $tabLayout->save();
        $this->redirectTo('edit', 'tablayout', array('saved' => 1));
    }

    /**
     * reset tab layout to default based on enabled modules
     */
    public function action_reset() {
        XG_TabLayout::createDefaultLayoutObject()->save();
        $this->redirectTo('edit', 'tablayout', array('reset' => 1));
    }

    /**
	 *	Returns colors matching the current theme:
	 *		textColor
	 *		textColorHover
	 *		backgroundColor
	 *		backgroundColorHover
     */
    public function action_getColors() {
    	$this->_widget->includeFileOnce('/lib/helpers/Index_TablayoutColorHelper.php');
    	foreach (Index_TablayoutColorHelper::getDefaultColors() as $k=>$v) {
    		$this->$k = $v;
		}
    }
}

?>
