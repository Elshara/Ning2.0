<?php

// Utility script to set all my->memberStatus attributes of all Users to null.  To be called in the browser.

define('NF_BASE_URL', '');

function main() {
    $max = 50;
    if (isset($_GET['go'])) {
        reset_next($max);
    } else {
        show_link($max);
    }
}

function show_link($n) {
    echo '<p><a href="?go=1">Reset next ' . $n . '</a></p>';
}

function reset_next($n) {
    $query = XN_Query::create('Content');
    $query->filter('owner')->filter('type', '=', 'User')->filter('my->memberStatus', '<>', null)->begin(0)->end($n)->alwaysReturnTotalCount(true);
    foreach ($query->execute() as $user) {
        $user->my->memberStatus = null;
        $user->save();
    }
    if ($query->getTotalCount() > $n) {
        show_link($n);
    } else {
        echo "<p>Done</p>";
    }
}

main();
