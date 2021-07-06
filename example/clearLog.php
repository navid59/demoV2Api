<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('classes/log.php');

$log = new log();
switch($_POST['logType']){
    case 1:
        $logType = 'realtimeLog';
    break;
    case 2:
        $logType = 'ipnLog';
    break;
    default:
        # nothing to clean
}

if($log->cleanLogFile($logType)){
    echo(json_encode([
        'status' => 1,
        'msg'   => 'the previous logs are removed'
    ]));
}else {
    echo(json_encode([
        'status' => 0,
        'msg'   => 'logs are not removed, try again'
    ]));
}