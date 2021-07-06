<?php 
die('Access Denied');
/**
 * Return page is not need it, currently 
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('lib/log.php');

$setRealTimeLog = 
            [
                "ReturnUrl"    =>  "Success Page Is hitting",
            ];
log::setRealTimeLog($setRealTimeLog);

$setRealTimeLog = $_REQUEST;
log::setRealTimeLog($setRealTimeLog);


$HTTP_RAW_POST_DATA = file_get_contents('php://input');
$input = json_decode($HTTP_RAW_POST_DATA);

$returnLog = ["returnBody" => $HTTP_RAW_POST_DATA ];
log::setIpnLog($returnLog);