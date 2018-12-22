<?php

//TODO actually, there's no reason why this can't live in tests as it only takes a few seconds

define('NF_BASE_URL', '');

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

XG_SecurityHelper::redirectIfNotOwner();

function main() {
    if (! isset($_GET['run'])) {
        show();
    } else {
        run($_GET['stage']);
    }
}

function show() {

    ?>
    
    <p>This script runs a test of GroupMembership::scheduleDenormalizeFullName which uses asynchronous jobs.  
        Because it potentially takes a long time to run it is outside the normal unit testing framework.</p>
    
    <p><a href="?run=1">Start</a></p>
    
    <?php
    
}

function run($stage) {
    if (! $stage) {
        print "<p>Created " . createTestObjects() . " test objects</p>";
        print "<p>" . checkObjects() . " objects to go</p>";
        nextStage('schedule');
    } else if ($stage === 'schedule') {
        XG_TestHelper::setCurrentWidget('groups');
        GroupMembership::scheduleDenormalizeFullName();
        nextStage('check');
    } else if ($stage === 'check') {
        $count = checkObjects();
        print "<p>$count objects to go.</p>";
        if ($count > 0) {
            nextStage('check');
        } else {
            nextStage('delete');
        }
    } else if ($stage === 'delete') {
        while (checkObjects()) { XG_TestHelper::deleteTestObjects(); }
        print "<p>Test objects deleted</p>";
    } else {
        throw new Exception("Unrecognized stage: '$stage'");
    }
}

function nextStage($stage) {
    print "<p><a href=\"?run=1&amp;stage=$stage\">Next</a> - $stage</p>";
}

function createTestObjects() {
    $users = getNetworkUsers(15);
    $groups = createTestGroups(11);
    $o = 0;
    foreach ($users as $user) {
        foreach ($groups as $group) {
            $gm = GroupMembership::create($group, $user->title);
            $gm->my->fullName = null;
            $gm->save();
            $o++;
        }
    }
    return $o;
}

function getNetworkUsers($max) {
    $query = XN_Query::create('Content')->filter('owner')->begin(0)->end($max);
    $query->filter('type', '=', 'User');
    return $query->execute();
}

function createTestGroups($n) {
    $groups = array();
    for ($i = 0; $i < $n; $i++) {
        $group = Group::create('TestGroupForScript' . $i);
        $group->save();
        $groups[] = $group;
    }
    return $groups;
}

function checkObjects() {
    $query = XN_Query::create('Content')->filter('owner')->begin(0)->end(50);
    $query->filter('type', '=', 'GroupMembership');
    $query->filter('my->fullName', '=', null);
    $query->filter('my->test', '=', 'Y');
    $query->alwaysReturnTotalCount(true);
    $query->execute();
    return $query->getTotalCount();
}

main();

