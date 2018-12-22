<?php
XG_App::includeFileOnce('/lib/XG_Layout.php');
/**
 * Dispatches requests pertaining to "embeds", which are reusable
 * page components.
 */
class Events_EmbedController extends W_Controller {

    /** Prefix for URL parameters. */
    public $prefix          = 'xg_module_events';

    /**
     * Constructor.
     *
     * @param   $widget     W_BaseWidget    The Events widget
     */
    public function __construct(W_BaseWidget $widget) {
        parent::__construct($widget);
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_TemplateHelper.php');
        W_Cache::getWidget('events')->includeFileOnce('/lib/helpers/Events_SecurityHelper.php');
        EventWidget::init();
    }

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
     * Updates the embed module footer and body - only called for Frink drop updates
     * The new HTML will be in the moduleBodyAndFooter property of the JSON output.
     * xn_out must be set to 'json'
     *
     * Expected GET parameters:
     *     id - The embed instance ID, used to retrieve the module data
     *
     * Expected POST parameters:
     *     columnCount - The number of columns that the module spans
     *
     * @return string       JSON string containing new module footer and body
     */
    public function action_updateEmbed() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        XG_HttpHelper::trimGetAndPostValues();
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser() && !XG_SecurityHelper::userIsAdmin()) { throw new Exception('Not embed owner.'); }
        $columnCount    = $_POST['columnCount'];
        $this->isOwner  = $embed->isOwnedByCurrentUser();
        $this->profileName = ($embed->getType() == 'homepage') ? NULL : $embed->getOwnerName();
        $this->settings = $this->profileName ? array('display' => 'list', 'from' => 'attending', 'count' => 4,) : array('display' => 'list', 'from' => 'upcoming', 'count' => 6,);

        foreach(array('display','from','count') as $k) {
            if (NULL !== ($v = $embed->get($k))) {
                $this->settings[$k] = $v;
            }
        }
        $this->_fetchEvents();

        ob_start();
        $this->renderPartial('fragment_block','embed', array(
            'events'    => $this->events,
            'settings'  => $this->settings,
            'calendar'  => $this->calendar,
            'columns'   => $columnCount,
            'profileName'=> $this->profileName,
            'viewAllUrl' => $this->viewAllUrl,
            'embed'      => $embed,
        ));
        $this->moduleBodyAndFooter = trim(ob_get_clean());
    }

    /**
     * Configures the embed module
     * The new HTML will be in the moduleBodyAndFooter property of the JSON output.
     * xn_out must be set to 'json'
     *
     * Expected GET parameters:
     *     id - The embed instance ID, used to retrieve the module data
     *
     * Expected POST parameters:
     *     columnCount - The number of columns that the module spans
     *     xg_module_events_display
     *     xg_module_events_from
     *     xg_module_events_count
     *
     * @return string       JSON string containing new module footer and body
     */
    public function action_setValues() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        XG_HttpHelper::trimGetAndPostValues();
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser() && !XG_SecurityHelper::userIsAdmin()) { throw new Exception('Not embed owner.'); }
        $columnCount = XG_Embed::getValueFromPostGet('columnCount');
        $this->isOwner  = $embed->isOwnedByCurrentUser();
        $this->profileName = ($embed->getType() == 'homepage') ? NULL : $embed->getOwnerName();

        $this->settings = array(
            'display'   => $_POST["{$this->prefix}_display"],
            'from'      => $_POST["{$this->prefix}_from"],
            'count'     => $_POST["{$this->prefix}_count"],
        );
        $this->_fetchEvents();

        foreach ($this->settings as $k=>$v) {
            $embed->set($k,$v);
        }

        ob_start();
        $this->renderPartial('fragment_block','embed', array(
            'events'    => $this->events,
            'settings'  => $this->settings,
            'calendar'  => $this->calendar,
            'columns'   => $columnCount,
            'profileName'=> $this->profileName,
            'viewAllUrl' => $this->viewAllUrl,
            'embed'      => $embed,
        ));
        $this->moduleBodyAndFooter = trim(ob_get_clean());

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }
//** Implementation
    /**
     * Displays a module that spans the given number of columns.
     *
     * @param $embed XG_Embed  Stores the module data.
     * @param $columnCount integer  The number of columns that the module will span
     */
    protected function renderEmbed($embed, $columnCount) {
        $this->embed    = $embed;
        $this->isOwner  = $embed->isOwnedByCurrentUser();
        $this->columns  = $columnCount;
        $this->profileName = ($embed->getType() == 'homepage') ? NULL : $embed->getOwnerName();
        $this->settings = $this->profileName ? array('display' => 'list', 'from' => 'attending', 'count' => 4,) : array('display' => 'list', 'from' => 'upcoming', 'count' => 6,);

        foreach(array('display','from','count') as $k) {
            if (NULL !== ($v = $embed->get($k))) {
                $this->settings[$k] = $v;
            }
        }
        $this->_fetchEvents();

        if ($this->isOwner) {
            XG_App::includeFileOnce('/lib/XG_Form.php');
            $values = array();
            foreach ($this->settings as $k=>$v) {
                $values["{$this->prefix}_{$k}"] = $v;
            }
            $this->form         = new XG_Form($values);
            $this->setValuesUrl = $this->_buildUrl('embed', 'setValues', array('id' => $embed->getLocator(), 'xn_out' => 'json', 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
            $this->updateEmbedUrl = $this->_buildUrl('embed', 'updateEmbed', array('id' => $embed->getLocator(), 'xn_out' => 'json'));
        }
        $this->render('embed');
    }

    /**
     * Initializes $this->events and other instance variables,
     * according to the current settings.
     */
    protected function _fetchEvents() { # XG_PagingList<Event>
        $cnt = max(0, intval($this->settings['count']));

		// BAZ-7609 (and BAZ-6992): for the profile page we need to show "event" embed only if user has events
		// so we fetch one extra event to make the block appear even for count=0
		$fix = false;
		if ($this->profileName && $this->profileName == XN_Profile::current()->screenName) {
			$cnt++;
			$fix = true;
		}
        if ( !$cnt || ( $this->profileName && !Events_SecurityHelper::currentUserCanSeeUserEvents($this->profileName) ) ) {
            $this->calendar = array();
            $this->events = new XG_PagingList(0,'');
            return;
        }
        switch ($this->settings['display']) {
            case 'detail':
            case 'list':
                break;
            case 'calendar':
                $ym = xg_date('Y-m');
                $this->calendar = $this->profileName ? EventAttendee::getCalendar($this->profileName,$ym,$ym) : EventCalendar::getCalendar($ym,$ym);
                break;
        }
        if ($this->profileName) {
            $this->viewAllUrl = $this->_buildUrl('event','listUserEvents',array('user'=>$this->profileName));
            $this->events = ($this->settings['from'] == 'all')
                ? EventAttendee::getUpcomingEvents($this->profileName, $cnt, true)
                : EventAttendee::getAttendingEvents($this->profileName, $cnt, true);
        } else {
            $this->viewAllUrl = $this->_buildUrl('event','listUpcoming');
            $this->events = ($this->settings['from'] == 'featured')
                ? Event::getFeaturedEvents($cnt, true)
                : Event::getUpcomingEvents($cnt);
        }

        if ($fix && count($this->events) == $cnt) {
			$this->events = $this->events->getList(); 		// transform to array
			$this->hasEvents = array_pop($this->events); 	// pop the last event
			if (!$this->events) {
				$this->calendar = array();
			}
		} else {
			$this->hasEvents = (bool)count($this->events);
		}
    }
}
?>
