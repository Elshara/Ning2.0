<?php

/**
 * Dispatches requests pertaining to the Page module, which appears
 * on the homepage and profile page.
 */
class Page_EmbedController extends W_Controller {

    /** Number of discussions to show on the module */
    const PAGE_COUNT = 3;

    /**
     * Displays a module that spans 1 column.
     *
     * @param $args array  Contains the object that stores the module data ('embed' => XG_Embed)
     */
    public function action_embed1($args) { $this->renderEmbed($args['embed'], 1); }

    /**
     * Displays a module that spans 2 columns.
     *
     * @param $args array  Contains the object that stores the module data ('embed' => XG_Embed)
     */
    public function action_embed2($args) { $this->renderEmbed($args['embed'], 2); }

    /**
     * Displays a module that spans 3 columns.
     *
     * @param $args array  Contains the object that stores the module data ('embed' => XG_Embed)
     */
    public function action_embed3($args) { $this->renderEmbed($args['embed'], 3); }

    /**
     * Displays a module that spans the given number of columns.
     *
     * @param $embed XG_Embed  Stores the module data.
     * @param $columnCount integer  The number of columns that the module will span
     */
    private function renderEmbed($embed, $columnCount) {
        $this->_widget->includeFileOnce('/lib/helpers/Page_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_Filter.php');
        XG_HttpHelper::trimGetAndPostValues();
        $this->columnCount = $columnCount;
        $this->setValuesUrl = $this->_buildUrl('embed', 'setValues', array('id' => $embed->getLocator(), 'xn_out' => 'json', 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        $this->options = array(array('label' => xg_text('MOST_RECENT'), 'value' => 'recent'), array('label' => xg_text('MOST_POPULAR'), 'value' => 'popular'));
        $embed->set('pageSet', $embed->get('pageSet') ? $embed->get('pageSet') : 'recent');
        $this->embed = $embed;
        $this->pagesAndComments = $this->pagesAndComments($embed);
        $this->pages = Page::pages($this->pagesAndComments);
        XG_Cache::profiles($this->pagesAndComments, $this->pages);
        if (count($this->pagesAndComments) || $embed->isOwnedByCurrentUser()) {
            $this->render('embed');
        }
    }

    /**
     * Retrieves the Page and Comment objects for the module.
     *
     * @param $embed XG_Embed  Stores the module data.
     * @return array  The Page and Comment content objects for the module
     */
    private function pagesAndComments($embed) {
        $pageSet = $embed->get('pageSet');
        if (! ($pageSet == 'popular' || $pageSet == 'recent')) { throw new Exception('Assertion failed'); }
        $query = XN_Query::create('Content')->end(self::PAGE_COUNT);
        if (($embed->getType() != 'profiles') || XG_Cache::cacheOrderN()) {
            $query = XG_Query::create($query);
        }
        return Page_Filter::get('most' . ucfirst($pageSet))->execute($query, $embed->getType() == 'profiles' ? $embed->getOwnerName() : NULL);
    }

    /**
     * Configures the module to display the pages and comments specified in $_POST['pageSet'] (popular or recent).
     * The new HTML will be in the moduleBodyAndFooterHtml property of the JSON output.
     *
     * Expected GET parameters:
     *     id - The embed instance ID, used to retrieve the module data
     *     columnCount - The number of columns that the module spans
     */
    public function action_setValues() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_HtmlHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_CommentHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_Filter.php');
        if (! ($_POST['pageSet'] == 'popular' || $_POST['pageSet'] == 'recent')) { throw new Exception('Assertion failed'); }
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owner.'); }
        $embed->set('pageSet', $_POST['pageSet']);
        $pagesAndComments = $this->pagesAndComments($embed);
        $pages = Page::pages($pagesAndComments);
        XG_Cache::profiles($pagesAndComments, $pages);
        ob_start();
        $this->renderPartial('fragment_moduleBodyAndFooter', array('pagesAndComments' => $pagesAndComments, 'pages' => $pages, 'columnCount' => $_GET['columnCount'], 'embed' => $embed, 'showContributorName' => $embed->getType() != 'profiles'));
        $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    public function action_error() {
        $this->render('blank');
    }

}
