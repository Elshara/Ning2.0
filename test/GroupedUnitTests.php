<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/WebTestGroupRunner.php';

class TestGroups {
    public $groups = array(
                        'XG_Cache' => 'XG/XG_Cache',
                        'XG_Query' => 'XG/XG_Query',
                        'Invitation' => 'main/Index_Invitation',
                        );


    public $subdirs = array();

    public function __construct() {
        $entries = glob(dirname(__FILE__).'/*');
        foreach ($entries as $entry) {
            if (is_dir($entry)) {
                $this->subdirs[basename($entry)] = basename($entry);
            }
        }
    }

    public function go() {
        $groupsToRun = $subdirsToRun = null;
        if (is_array($_GET['group']) && count($_GET['group'])) {
            $groupsToRun = $_GET['group'];
        }
        if (is_array($_GET['subdir']) && count($_GET['subdir'])) {
            $subdirsToRun = $_GET['subdir'];
        }
        if (! ($groupsToRun || $subdirsToRun)) {
            $this->displayForm();
        } else {
            $files = array();
            if ($subdirsToRun) {
                foreach ($subdirsToRun as $subdir) {
                    foreach (glob("./$subdir/*Test.php") as $file) {
                        $files[$file] = true;
                    }
                }
            }
            if($groupsToRun) {
                foreach ($groupsToRun as $group) {
                    foreach (glob("./{$this->groups[$group]}*Test.php") as $file) {
                        $files[$file] = true;
                    }
                }
            }
            if (count($files)) {
                $tgr = new WebTestGroupRunner(array_keys($files));
                $tgr->run();
            } else {
                print "No files found.";
                $this->displayForm();
            }
        }
    }

    protected function displayForm() {
?><form method="get" action="<%= htmlentities($_SERVER['REQUEST_URL']) %>">
<p>Choose a test group:<p>
<ul>
<?php foreach ($this->groups as $k => $v) { ?>
    <li><input type="checkbox" name="group[]" value="<%= htmlentities($k) %>" /> <%= htmlentities($k) %></li>
<?php } ?>
</ul>
<p>Or choose a test subdirectory:</p>
<ul>
<?php foreach ($this->subdirs as $k => $v) { ?>
    <li><input type="checkbox" name="subdir[]" value="<%= htmlentities($k) %>" /> <%= htmlentities($k) %></li>
<?php } ?>
</ul>

<input type="submit" value="Run"/>
</form>
    <?php }
}

$tg = new TestGroups();
$tg->go();

