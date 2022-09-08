<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('classes/log.php');
include_once('../lib/ipn.php');

require_once 'vendor/autoload.php';


// Log
$setRealTimeLog = ["IPN"    =>  "IPN Is hitting"];
log::setRealTimeLog($setRealTimeLog);
log::logHeader();

/**
 * get defined keys
 */
$ntpIpn = new IPN();

$ntpIpn->activeKey         = 'AAAA-BBBB-CCCC-DDDD-EEEE'; // activeKey or posSignature
$ntpIpn->posSignatureSet[] = 'AAAA-BBBB-CCCC-DDDD-FFFF'; // The active key should be in posSignatureSet as well
$ntpIpn->posSignatureSet[] = 'AAAA-BBBB-CCCC-DDDD-GGGG'; 
$ntpIpn->posSignatureSet[] = 'AAAA-BBBB-CCCC-DDDD-HHHH'; 
$ntpIpn->posSignatureSet[] = 'AAAA-BBBB-CCCC-DDDD-IIII';
$ntpIpn->hashMethod        = 'SHA512';
$ntpIpn->alg               = 'RS512';

$ntpIpn->publicKeyStr = "-----BEGIN PUBLIC KEY-----\nYourPublicKeyIsHere-----END PUBLIC KEY-----\n";

$ipnResponse = $ntpIpn->verifyIPN();


/**
 * IPN Output
 */
echo json_encode($ipnResponse);

