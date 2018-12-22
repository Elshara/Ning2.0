<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/XG_CoverageTester.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/XG_FaultTolerantTask.php';

function echoFoo() {
    echo 'foo';
}

function triggerUserError() {
    trigger_error('Test error', E_USER_ERROR);
}

function sleepFourSeconds() {
    sleep(4);
}

function onError($operations) {
    $args = array();
    foreach ($operations as $operation) {
        $args[] = $operation->args[0];
    }
    ob_end_clean();
    echo implode(',', $args);
}

$task = new XG_FaultTolerantTask('onError');
$task->add('triggerUserError', array(1));
$task->add('echoFoo', array(2));
$task->add('echoFoo', array(3));
$task->add('sleepFourSeconds', array(4));
$task->add('echoFoo', array(5));
ob_start();
$task->execute(3);
echo 'end';
