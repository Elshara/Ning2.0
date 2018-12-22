<?php
XG_App::includeFileOnce('/lib/XG_Layout.php');
/**
 * Dispatches requests pertaining to "embeds", which are reusable components.
 */
class Notes_EmbedController extends W_Controller {

    /** Prefix for URL parameters. */
    public $prefix          = 'xg_module_notes';

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
     * Configures the embed module
     * The new HTML will be in the moduleBody property of the JSON output.
     *
     * Expected GET parameters:
     *     id - The embed instance ID, used to retrieve the module data
     *     columnCount - The number of columns that the module spans
     */
    public function action_setValues() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        $embed = XG_Embed::load($_REQUEST['id']);
        if (!$embed->isOwnedByCurrentUser() && !XG_SecurityHelper::userIsAdmin()) { throw new Exception('Not embed owner.'); }

        $columnCount = $_REQUEST['columnCount'];
        if ($this->isHomepage = ($embed->getType() == 'homepage')) {
            $this->settings = array(
                'display' => $_REQUEST["{$this->prefix}_display_$columnCount"],
                'title' => $_REQUEST["{$this->prefix}_title_$columnCount"],
                'from' => $_REQUEST["{$this->prefix}_from_$columnCount"],
                'count' => $_REQUEST["{$this->prefix}_count_$columnCount"],
            );
        } else {
            $this->settings = array(
                'display' => $_REQUEST["{$this->prefix}_display_$columnCount"],
                'count' => $_REQUEST["{$this->prefix}_count_$columnCount"],
            );
        }

        foreach ($this->settings as $k=>$v) {
            $embed->set($k,$v);
        }

        $this->_fetchData($embed);

        ob_start();
        $this->renderPartial('fragment_block','embed', array(
            'content' => $this->content,
            'settings'=> $this->settings,
            'viewAllUrl'=>$this->viewAllUrl,
            'columns' => $columnCount,
        ));
        $this->moduleBodyAndFooter = ob_get_clean();
        unset($this->content, $this->hasContent, $this->settings);

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

//** Implementation
    /**
     * Displays a module that spans the given number of columns. Settings:
     * 		HOMEPAGE
     * 			display		details(default) | titles | note
     * 			if (display == note)
     * 				title		Title of the note to display
     * 			else
     *				from		featured | updated(default) | created
     *				count		int(default=10)
     * 		PROFILE
     * 			display		details(default) | titles
     * 			count		int (default=0)
     *
     * @param $embed XG_Embed  Stores the module data.
     * @param $columnCount integer  The number of columns that the module will span
     */
    protected function renderEmbed($embed, $columnCount) {
        $this->columns  = $columnCount;

        if ($this->isHomepage = ($embed->getType() == 'homepage')) {
            $this->settings = array('display' => 'details', 'title' => '', 'from' => 'updated', 'count' => 10,);
        } else {
            $this->settings = array('display' => 'details', 'count' => 0,);
        }

        foreach (array('display','title','from','count') as $k) {
            if (mb_strlen($v = $embed->get($k))) { $this->settings[$k] = $v; }
        }

        $this->_fetchData($embed);

        if ($this->isOwner = $embed->isOwnedByCurrentUser()) {
            XG_App::includeFileOnce('/lib/XG_Form.php');
            $values = array();
            foreach ($this->settings as $k=>$v) {
                $values["{$this->prefix}_{$k}_{$columnCount}"] = $v;
            }
            $this->form         = new XG_Form($values);
            $this->setValuesUrl = $this->_buildUrl('embed', 'setValues', array('id' => $embed->getLocator(), 'xn_out' => 'json', 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        }
        $this->render('embed');
    }

    /**
     * Initializes instance variables, according to the current settings:
     * 		content
     * 		hasContent
     */
    protected function _fetchData($embed) {
        if ($this->isHomepage) {
            if ($this->settings['display'] == 'note') {	// content of the single note
                if ($note = Note::byKey($this->settings['title'])) {
                    $this->content = $note;
                    $this->hasContent = true;
                } else {
					$this->content = NULL;
                    $this->hasContent = false;
                }
            } elseif (!$this->settings['count']) { // module is disabled
                $this->content = NULL;
                $this->hasContent = false;
                // nothing to do.
            } elseif ($this->settings['from'] == 'featured')  { // list of featured notes
                $this->content = Note::getFeaturedNotes($this->settings['count'], NULL, true);
                $this->hasContent = (bool)count($this->content);
                $this->viewAllUrl = Notes_UrlHelper::url('allNotes');
            } else { // list of recent notes
                $this->content = Note::getAllNotes($this->settings['count'], $this->settings['from'] == 'created' ? 'created' : 'updated', true);
                $this->hasContent = (bool)count($this->content);
                $this->viewAllUrl = Notes_UrlHelper::url('allNotes');
            }

        } else { // profile notes
			// do nothing.
        }
        return;
    }
}
?>
