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

/**
 * get defined keys
 */
$ntpIpn = new IPN();

$ntpIpn->activeKey         = 'XXXX-XXXX-XXXX-XXXX-XXXX'; // activeKey or posSignature
$ntpIpn->posSignatureSet[] = 'XXXX-XXXX-XXXX-XXXX-XXXX';
$ntpIpn->posSignatureSet[] = 'XXXX-ZZZZ-WWWW-KKKK-NNNN_fake1'; 
$ntpIpn->posSignatureSet[] = 'ZZZZ-XXXX-ZZZZ-GC8B-NNNN_fake2'; 
$ntpIpn->posSignatureSet[] = 'KKKK-3WDM-XXXX-KKKK-NNNN_fake3';
$ntpIpn->hashMethod        = 'SHA512';
$ntpIpn->alg               = 'RS512';
// $ntpIpn->publicKeyStr      = '-----BEGIN CERTIFICATE-----
// MIIDKLOREMNgAwIBAgIBADANBgkqhkiG9w0BAQQFADCBsTELMAkGA1UEBhMCUk8x
// EjAQBLOREMNLOREMN2hhcmVzdDESMBAGA1UEBwwJQnVjaGFyZXN0MRcwFQYDVQQK
// DA5OIEUgVCBLOREMNOREMNEnMCUGA1UECwweTiBFIFQgTyBQIEkgQSBEZXZlbG9w
// bWVudLOREMNtMRQwLOREMNOREMNtb2JpbHBheS5ybzEiMCAGCSqGSIb3DQEJARYT
// c3VwcG9ydEBLOREMNHBheLOREMNEMN0yMTA0MjcxMTMxMDdaFw0yMjA0MjcxMTMx
// MDdLOREMNQswCQYDLOREMNJSLOREMNREMNUECAwJQnVjaGFyZXN0MRIwEAYDVQQH
// DAlCdWNoYXJlc3QxFzAVBLOREMNMLOREMNREMN8gUCBJIEEgMScwJQYDVQQLDB5O
// IEUgVCBLOREMNSBBIERldmVsLOREMN50LOREMNREMNASBgNVBAMMC21vYmlscGF5
// LnJvMSIwIAYJKLOREMNNAQkBFhNzLOREMNJ0LOREMNOREMN5LnJvMIGfMA0GCSqG
// SIb3DQEBAQUAA4GNADCBiQKBgQC8IdPzLOREMNir4LOREMNOREMNOTFjQoeNtpHH
// xSm6j+WFYglAYNzHOWWHdXLOREMNtUCNmf47LOREMNRkMILOREMN0vW6MBxJGR/N
// WaJTqDxwWW2KQNvALOREMNGk14y7YgRr46cLs5Y5lLOREMNpyGhNLOREMN/TC1ht
// nxjHXQIDAQABLOREMNAdBgNVHQ4EFgQUPclwoTBsc1M0H5LOREMNLOREMNLOREMN
// VR0jBBgwFoAUPclwoTBsc1M0H5ZpF09aMiAaHrUwDAYDVR0TBAUwAwEB/zLOREMN
// hkiG9wLOREMNAAOBgQB5juqDH6s09OmEcRmfbspXGVyxIaFMMgAOLOREMNezVKOv
// LOREMNvO8ZIUy9G/P87qNz9WIe5ryfAR9G/ZkA0u8dTWxBElkvJT01q4ejLOREMN
// wvLWzfOJcGTsfYy0MnMHGiq/0JJ11foTA6ZofudJhJ8UjXQ7waKDOFbnqKPGFQ==
// -----END CERTIFICATE-----';



$ipnResponse = $ntpIpn->verifyIPN();


/**
 * IPN Output
 */
echo json_encode($ipnResponse);

