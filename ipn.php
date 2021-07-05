<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('lib/log.php');
include_once('lib/ipn.php');

require_once 'vendor/autoload.php';


// Log
$setRealTimeLog = ["IPN"    =>  "IPN Is hitting"];
log::setRealTimeLog($setRealTimeLog);

/**
 * get defined keys
 */
$ntpIpn = new ipn(); // New IPN OBJ

$ntpIpn->activeKey         = 'LXTP-3WDM-WVXL-GC8B-Y5DA'; // activeKey or posSignature -> Ask Alex
$ntpIpn->posSignatureSet[] = 'LXTP-3WDM-WVXL-GC8B-Y5DA';
$ntpIpn->posSignatureSet[] = 'LXTP-3WDM-WVXL-GC8B-Y5DA_fake1'; 
$ntpIpn->posSignatureSet[] = 'LXTP-3WDM-WVXL-GC8B-Y5DA_fake2'; 
$ntpIpn->posSignatureSet[] = 'LXTP-3WDM-WVXL-GC8B-Y5DA_fake3';
$ntpIpn->hashMethod        = 'SHA512';
$ntpIpn->alg               = 'RS512';
$ntpIpn->publicKeyStr      = '-----BEGIN CERTIFICATE-----
MIIDKjCCApOgAwIBAgIBADANBgkqhkiG9w0BAQQFADCBsTELMAkGA1UEBhMCUk8x
EjAQBgNVBAgMCUJ1Y2hhcmVzdDESMBAGA1UEBwwJQnVjaGFyZXN0MRcwFQYDVQQK
DA5OIEUgVCBPIFAgSSBBIDEnMCUGA1UECwweTiBFIFQgTyBQIEkgQSBEZXZlbG9w
bWVudCBUZWFtMRQwEgYDVQQDDAttb2JpbHBheS5ybzEiMCAGCSqGSIb3DQEJARYT
c3VwcG9ydEBtb2JpbHBheS5ybzAeFw0yMTA0MjcxMTMxMDdaFw0yMjA0MjcxMTMx
MDdaMIGxMQswCQYDVQQGEwJSTzESMBAGA1UECAwJQnVjaGFyZXN0MRIwEAYDVQQH
DAlCdWNoYXJlc3QxFzAVBgNVBAoMDk4gRSBUIE8gUCBJIEEgMScwJQYDVQQLDB5O
IEUgVCBPIFAgSSBBIERldmVsb3BtZW50IFRlYW0xFDASBgNVBAMMC21vYmlscGF5
LnJvMSIwIAYJKoZIhvcNAQkBFhNzdXBwb3J0QG1vYmlscGF5LnJvMIGfMA0GCSqG
SIb3DQEBAQUAA4GNADCBiQKBgQC8IdPzYRKWRbir4IWfTe+Ql22tOTFjQoeNtpHH
xSm6j+WFYglAYNzHOWWHdXtF4vVItUCNmf4773Iaw2RkMI2qwKa90vW6MBxJGR/N
WaJTqDxwWW2KQNvASMh2EXGk14y7YgRr46cLs5Y5l3gaFS4pyGhNCFKTHp/TC1ht
nxjHXQIDAQABo1AwTjAdBgNVHQ4EFgQUPclwoTBsc1M0H5ZpF09aMiAaHrUwHwYD
VR0jBBgwFoAUPclwoTBsc1M0H5ZpF09aMiAaHrUwDAYDVR0TBAUwAwEB/zANBgkq
hkiG9w0BAQQFAAOBgQB5juqDH6s09OmEcRmfbspXGVyxIaFMMgAOP7P2YdezVKOv
UvuGPRvO8ZIUy9G/P87qNz9WIe5ryfAR9G/ZkA0u8dTWxBElkvJT01q4ej2Ldrpt
wvLWzfOJcGTsfYy0MnMHGiq/0JJ11foTA6ZofudJhJ8UjXQ7waKDOFbnqKPGFQ==
-----END CERTIFICATE-----';

$ipnResponse = $ntpIpn->verifyIPN();


/**
 * IPN Output
 */
echo json_encode($ipnResponse);

