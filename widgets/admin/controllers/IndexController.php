<?php

class Admin_IndexController extends W_Controller {

    protected function _before() {
        XG_SecurityHelper::redirectIfNotAdmin();
    }

    /**
     * Menu for (some) admin items.
     */
    public function action_index() {}

    /**
     * Content Manager
     */
    public function action_manage() {
        XG_App::includeFileOnce(XN_INCLUDE_PREFIX .'/XNC/ContentManager.php', false);
        $mgr = new XNC_ContentManager();
        ini_set('url_rewriter.tags', 'form=fakeentry'); // This line enables the next line [Jon Aquino 2008-09-19]
        output_add_rewrite_var ('xg_token', XG_SecurityHelper::getCsrfToken()); // BAZ-8314 [Jon Aquino 2008-09-10]
        echo '<html><head><ning:head/>' . XG_App::sectionMarker() . '</head>';
        echo '<body id="userContent">';
        $mgr->go();
    }

    public function action_clearLog() {
        $myFile = NF_ERROR_LOG;
        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh, '');
        fclose($fh);
        error_log('Log cleared');
          header('Location: /admin/index/log/');
          exit;
    }

    /**
     * Error Log viewer
     */
    public function action_log() {
        // Default to 10kb of the log file.
        $maxChunk = 1024 * 10;
        // Display the last $maxChunk of the file or what the chunk specifies
        // Chunk should be positive
        $chunk = min(0, isset($_GET['chunk']) ? intval($_GET['chunk']) : 0);
        // Chunk should be <= $maxChunk
        $chunk = max($chunk, $maxChunk);
        if (! is_readable(NF_ERROR_LOG)) {
            $this->error(xg_text('CANNOT_READ_ERROR_LOG'));
            return;
        }
        $fp = @fopen(NF_ERROR_LOG,'r');
        if (! $fp) {
             $this->error(xg_text('CANNOT_OPEN_ERROR_LOG'));
             return;
        }
        if (filesize(NF_ERROR_LOG) > $chunk) {
            if (fseek($fp,-$chunk,SEEK_END) == -1) {
                $this->error(xg_text('CANNOT_SCAN_ERROR_LOG'));
                return;
            }
        }
        // Advance to the next newline in the error log
        $partial_line = fgets($fp, $chunk);
        if ($partial_line === false) {
            $this->error(xg_text('CANNOT_READ_ERROR_LOG'));
            return;
        } elseif (mb_strlen($partial_line) == $chunk) {
            $buf = $partial_line;
        } else {
            $buf = fread($fp, $chunk);
            if ($buf === false) {
                $this->error(xg_text('CANNOT_READ_ERROR_LOG'));
                return;
            }
        }
        fclose($fp);
        // This produces an array in which elements alternate between log
        // message times and the messages themselves
        $this->parts = preg_split('/^(\[\d\d-...-\d\d\d\d \d\d:\d\d:\d\d\])/um',
            $buf, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
    } // action_log()

    public function action_testBaz5031() {
        $this->redirectTo('simulateBaz5031');
    }

    public function action_simulateBaz5031() {
        if (! mb_strlen($_GET['test_thumbnail_failure_probability'])) {
            echo '<form><i>This simulation calls XG_UserHelper::setThumbnailFromProfile($profile). If it encounters bad avatar data, it will try again, up to 3 times.</i> <br /><br />Probability of bad avatar data: <input name="test_thumbnail_failure_probability" value="100" size="3" /> %<br /><input type="submit" value="Submit"></form>';
            return;
        }
        XG_UserHelper::setThumbnailFromProfile($this->_user);
        User::load($this->_user)->save();
        echo 'Avatar:</br>';
        echo '<img src="' . xnhtmlentities(User::load($this->_user)->my->thumbnailUrl) . '" />';
    }

    /**
     * Displays a form for entering screen names of users to fix.
     *
     * Expected GET variables:
     *     screenNamesFixed - the usernames of users that were just fixed
     */
    public function action_fixAvatars() {
        $this->screenNamesFixed = $_GET['screenNamesFixed'];
    }

    /**
     * Processes the form for entering screen names of users to fix.
     *
     * Expected POST variables:
     *     screenNames - comma-delimited list of usernames
     */
    public function action_doFixAvatars() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { return $this->redirectTo('fixAvatars'); }
        foreach (explode(',', $_POST['screenNames']) as $screenName) {
            if (! XG_UserHelper::isThumbnailDataOk(User::load(trim($screenName)))) {
                XG_UserHelper::setThumbnailFromProfile(XN_Profile::load(trim($screenName)));
                User::load(trim($screenName))->save();
            }
        }
        $this->redirectTo('fixAvatars', 'index', array('screenNamesFixed' => $_POST['screenNames']));
    }

    public function action_listSets() {
        $sets = XN_ProfileSet::listSets();
        foreach ($sets as $setName) {
            $set = XN_ProfileSet::load($setName);
            $numMembers = $set->size;
            $members = array();
            $start = 0;
            while (count($members) < $numMembers) {
                $members = array_merge($members, $set->members($start, 100));
                $start += 100;
            }
            natcasesort($members);
            echo "$setName($numMembers): (" . implode($members, ', ') , ") <br />\n";
        }
    }

    public function action_checkSearchability() {
        XG_App::includeFileOnce('/lib/XG_ShapeHelper.php');
        $this->resetModels = XG_ShapeHelper::setStandardIndexingForSearchableModels();
    }

    /**
     * Clears feeds from the action cache
     */
    public function action_clearFeedActionCache() {
        XG_App::includeFileOnce('/lib/XG_FeedHelper.php');
        XN_Cache::invalidate(XG_FeedHelper::FEED_CACHE_LABEL);
        print "invalidated";
    }

    /**
     * Displays a table of messages, for testing.
     *
     * @param	$type[]		list		Selected items
     * @param	$opts[]		hash		Options.
     * @param	$display	bool		Display messages
     * @param	$send		bool		Send messages
     */
    public function action_testMessages() {
        W_Cache::getWidget('admin')->includeFileOnce('/lib/helpers/Admin_MessageHelper.php');
        $this->types = Admin_MessageHelper::getAllTypes();
        $this->selected = array_flip((array)$_REQUEST['type']);
        $this->opts = (array)$_REQUEST['opts'];
        $this->command = $_REQUEST['send'] ? 'send' : ($_REQUEST['display'] ? 'display' : '');
    }

    /** Call denormalizing function to make Members sortable by status and GroupMembership objects searchable. */
    public function action_manualSortAndSearchUpdate() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_MembershipHelper.php');
        $this->memberStatusRemaining = Index_MembershipHelper::addMemberStatus();
        $this->groupMemberSearchRemaining = GroupMembership::denormalizeFullName();
    }

    /**
     * Displays a form for editing the mapped domain to redirect to.
     *
     * Expected GET parameters:
     *     - saved - whether the save was successful
     *
     * @param $errors array  HTML error messages, keyed by field name
     * @see BAZ-7268
     */
    public function action_editDomainRedirection($errors = array()) {
        XG_SecurityHelper::redirectIfNotOwner();
        $this->form = new XNC_Form(array('domainName' => W_Cache::getWidget('main')->config['domainName']));
        $this->_widget->includeFileOnce('/lib/helpers/Admin_DomainRedirectionHelper.php');
        $this->domainNames = Admin_DomainRedirectionHelper::domainNames();
        $this->saved = $_GET['saved'];
        $this->errors = $errors;
    }

    /**
     * Processes the form for editing the mapped domain to redirect to.
     */
    public function action_updateDomainRedirection() {
        XG_SecurityHelper::redirectIfNotOwner();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (313764629)'); }
        $this->_widget->includeFileOnce('/lib/helpers/Admin_DomainRedirectionHelper.php');
        if (! in_array($_POST['domainName'], Admin_DomainRedirectionHelper::domainNames())) { throw new Exception('Invalid domain name (1007005300)'); }
        if (Admin_DomainRedirectionHelper::applicationId($_POST['domainName']) !== XN_Application::load()->relativeUrl) {
            return $this->forwardTo('editDomainRedirection', 'index', array(array('domainName' => xg_html('DOMAIN_DOES_NOT_MATCH'))));
        }
        W_Cache::getWidget('main')->config['domainName'] = $_POST['domainName'];
        W_Cache::getWidget('main')->saveConfig();
        Admin_DomainRedirectionHelper::disableRedirectionInIndexFile();
        $this->redirectTo('editDomainRedirection', 'index', array('saved' => 1));
    }

    /**
     * Resets the contents of the index.php file. Any user customizations will be removed.
     * The original file will be backed up to /xn_private/xn_volatile/backups.
     * @see BAZ-7268
     */
    public function action_resetIndexFile() {
        XG_SecurityHelper::redirectIfNotOwner();
        $this->_widget->includeFileOnce('/lib/helpers/Admin_DomainRedirectionHelper.php');
        Admin_DomainRedirectionHelper::resetIndexFile();
        echo 'index.php was successfully reset.';
    }

}
