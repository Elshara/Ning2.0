<?php
$numEntries = count($tabs);
foreach ($tabs as $idx => $entry) {
    list($name, $link, $id) = $entry;
    echo '<li id="xg_tab_' . $id . '"';
    $classes = array();
    //if (0 == $idx) { $classes[] = 'first-child'; }
    //if (($numEntries - 1) == $idx) { $classes[] = 'last-child'; }
    if ($id == $this->navHighlight) {
        $classes[] = 'this';
    }
    if (count($classes) > 0) {
        echo " class='" . join(' ', $classes) . "'";
    }
    echo "><a href='" . xnhtmlentities($link) . "'>" . xnhtmlentities($name) . "</a></li>\n";
}