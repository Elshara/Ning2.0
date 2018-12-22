<?php
if (! XN_Profile::current()->isOwner()) { ?>
    <p>This page is available to the Network Creator only. If you are the Network Creator, <a href="<%= 'http://' . $_SERVER['HTTP_HOST'] %>/main/authorization/signIn?target=<%= xnhtmlentities(urlencode(currentUrl())) %>">sign in</a>.</p>
    <?php
    exit;
}
if ($_GET['locale']) {
    header('Content-type: text/plain');
    header('Content-disposition: attachment; filename=' . $_GET['locale'] . '.txt');
    foreach (globr($_SERVER['DOCUMENT_ROOT'], '*' . $_GET['locale'] . '.php') as $filename) {
        readfile($filename);
    }
    foreach (globr($_SERVER['DOCUMENT_ROOT'], '*' . $_GET['locale'] . '.js') as $filename) {
        readfile($filename);
    }
    exit;
} else {
    $locales = array();
    foreach (globr($_SERVER['DOCUMENT_ROOT'] . '/lib', 'XG_MessageCatalog*.php') as $filename) {
        if (preg_match('@XG_MessageCatalog_(.._..).php@u', $filename, $matches)) {
            $locales[] = $matches[1];
        }
    } ?>
    <h1>Translations</h1>
    <p><%= count($locales) %> translations found on this network.</p>
    <ul>
        <?php
        foreach ($locales as $locale) { ?>
            <li><a href="?locale=<%= xnhtmlentities($locale) %>"><%= xnhtmlentities($locale) %></a></li>
        <?php
        } ?>
    </ul>
    <?php
    exit;
}
/**
 * Recursive version of glob
 *
 * @param $sDir string      Directory to start with.
 * @param $sPattern string  Pattern to glob for.
 * @param $nFlags int      Flags sent to glob.
 * @return array containing all pattern-matched files.
 */
// From http://ca3.php.net/manual/en/function.glob.php#30238  [Jon Aquino 2007-01-17]
function globr($sDir, $sPattern, $nFlags = NULL) {
    $sDir = escapeshellcmd($sDir);

    // Get the list of all matching files currently in the
    // directory.
    $aFiles = glob("$sDir/$sPattern", $nFlags);

    // Then get a list of all directories in this directory, and
    // run ourselves on the resulting array.  This is the
    // recursion step, which will not execute if there are no
    // directories.
    foreach (glob("$sDir/*", GLOB_ONLYDIR) as $sSubDir) {
        $aSubFiles = globr($sSubDir, $sPattern, $nFlags);
        $aFiles = array_merge($aFiles, $aSubFiles);
    }

    // The array we return contains the files we found, and the
    // files all of our children found.
    return $aFiles;
}

function currentUrl() {
    return str_replace('/index.php', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}