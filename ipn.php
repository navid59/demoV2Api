<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('lib/log.php');
include_once('lib/ipn.php');

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;

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


/**
 * Default IPN response, 
 * will change if there is any problem
 */
$outputData = array(
    'errorType'		=> ipn::ERROR_TYPE_NONE,
    'errorCode' 	=> null,
    'errorMessage'	=> ''
);

/**
 *  Fetch all HTTP request headers
 */
$aHeaders = $ntpIpn->getApacheHeader();
if(!$ntpIpn->validHeader($aHeaders)) {
    echo 'IPN__header is not an valid HTTP HEADER' . PHP_EOL;
    exit;
} 

/**
 *  fetch Verification-token from HTTP header 
 */
$verificationToken = $ntpIpn->getVerificationToken($aHeaders);
if($verificationToken === null)
    {
    echo 'IPN__Verification-token is missing in HTTP HEADER' . PHP_EOL;
    exit;
    }

 /**
  * Analising verification token
  * Just to make sure if Type is JWT & Use right encoding/decoding algorithm 
  * Assign following var 
  *  - $headb64, 
  *  - $bodyb64,
  *  - $cryptob64
  */
  $tks = \explode('.', $verificationToken);
  if (\count($tks) != 3) {
    throw new \Exception('Wrong_Verification_Token');
    exit;
  }
  list($headb64, $bodyb64, $cryptob64) = $tks;
  $jwtHeader = json_decode(base64_decode(\strtr($headb64, '-_', '+/')));
  
  if($jwtHeader->typ !== 'JWT') {
    throw new \Exception('Wrong_Token_Type');
    exit; 
  }

/**
 * check if publicKeyStr is defined
 */
if(isset($ntpIpn->publicKeyStr) && !is_null($ntpIpn->publicKeyStr)){
    $publicKey = openssl_pkey_get_public($ntpIpn->publicKeyStr);
    if($publicKey === false) {
        echo 'IPN__public key is not a valid public key' . PHP_EOL; 
        exit;
    }
} else {
    echo "IPN__Public key missing" . PHP_EOL; 
    exit;
}
    
/**
 * Get raw data
 */
$HTTP_RAW_POST_DATA = file_get_contents('php://input');


// $input = json_decode($HTTP_RAW_POST_DATA); // can be get all recived data from $input
// die($input);



  /**
   * The name of the alg defined in header of JWT
   * Just in case we set the default algorithm
   * Default alg is RS512
   */
  if(!isset($ntpIpn->alg) || $ntpIpn->alg==null){
    throw new \Exception('IDS_Service_IpnController__INVALID_JWT_ALG');
    exit;
  }
  $jwtAlgorithm = !is_null($jwtHeader->alg) ? $jwtHeader->alg : $ntpIpn->alg ; // ???? May need to Compare with Verification-token header

try {
    JWT::$timestamp = time() * 1000;

   /**
    * Decode from JWT
    */
    $objJwt = JWT::decode($verificationToken, $publicKey, array($jwtAlgorithm));

    if(strcmp($objJwt->iss, 'NETOPIA Payments') != 0)
	    {
        throw new \Exception('IDS_Service_IpnController__E_VERIFICATION_FAILED_GENERAL');
        exit;
        }
    
    /**
     * check active posSignature 
     * check if is in set of signature too
     */
    if(empty($objJwt->aud) || $objJwt->aud != $ntpIpn->activeKey){
        throw new \Exception('IDS_Service_IpnController__INVALID_SIGNATURE');
        exit;
    }

    if(!in_array($objJwt->aud, $ntpIpn->posSignatureSet,true)) {
        throw new \Exception('IDS_Service_IpnController__INVALID_SIGNATURE_SET');
        exit;
    }
    
    if(!isset($ntpIpn->hashMethod) || $ntpIpn->hashMethod==null){
        throw new \Exception('IDS_Service_IpnController__INVALID_HASH_METHOD');
        exit;
    }
    
    /**
     * GET HTTP HEADER
     */
    $payload = $HTTP_RAW_POST_DATA;
    /**
	 * validate payload
     * sutable hash method is SHA512 
	 */
    $payloadHash = base64_encode(hash ($ntpIpn->hashMethod, $payload, true ));
    /**
	 * check IPN data integrity
	 */

	if(strcmp($payloadHash, $objJwt->sub) != 0)
	{
        throw new \Exception('IDS_Service_IpnController__E_VERIFICATION_FAILED_TAINTED_PAYLOAD', E_VERIFICATION_FAILED_TAINTED_PAYLOAD);
        print_r($payloadHash); // Temporay for Debuging
        exit;
    }

    try
	{
        $objIpn = json_decode($payload, false);
        log::setIpnLog($objIpn);
	}
	catch(\Exception $e)
	{
		throw new \Exception('IDS_Service_IpnController__E_VERIFICATION_FAILED_PAYLOAD_FORMAT', E_VERIFICATION_FAILED_PAYLOAD_FORMAT);
    }
    
    switch($objIpn->payment->status)
	{
	case ipn::STATUS_NEW:
	case ipn::STATUS_CHARGEBACK_INIT: // chargeback initiat
	case ipn::STATUS_CHARGEBACK_ACCEPT: // chargeback acceptat
	case ipn::STATUS_SCHEDULED:
	case ipn::STATUS_3D_AUTH:
	case ipn::STATUS_CHARGEBACK_REPRESENTMENT:
	case ipn::STATUS_REVERSED:
	case ipn::STATUS_PENDING_ANY:
	case ipn::STATUS_PROGRAMMED_RECURRENT_PAYMENT:
	case ipn::STATUS_CANCELED_PROGRAMMED_RECURRENT_PAYMENT:
	case ipn::STATUS_TRIAL_PENDING: //specific to Model_Purchase_Sms_Online; wait for ACTON_TRIAL IPN to start trial period
	case ipn::STATUS_TRIAL: //specific to Model_Purchase_Sms_Online; trial period has started
	case ipn::STATUS_EXPIRED: //cancel a not payed purchase 
	case ipn::STATUS_OPENED: // preauthorizate (card)
	case ipn::STATUS_PENDING:
	case ipn::STATUS_ERROR: // error
	case ipn::STATUS_DECLINED: // declined
    case ipn::STATUS_FRAUD: // fraud
        /**
		 * payment status is in fraud, reviw the payment
		 */
        $orderLog = 'payment in reviwing';
        log::setRealTimeLog($orderLog);
    break;
	case ipn::STATUS_PENDING_AUTH: // in asteptare de verificare pentru tranzactii autorizate
		/**
		 * update payment status, last modified date&time in your system
		 */
        $orderLog = 'update payment status, last modified date&time in your system';
        log::setRealTimeLog($orderLog);
    break;
    
	case ipn::STATUS_PAID: // capturate (card)
	case ipn::STATUS_CONFIRMED:
		/**
		 * payment was confirmed; deliver goods
		 */
        $orderLog = 'payment was confirmed; deliver goods';
        log::setRealTimeLog($orderLog);
    break;
    
	case ipn::STATUS_CREDIT: // capturate si apoi refund
		/**
		 * a previously confirmed payment eas refinded; cancel goods delivery
		 */
        $orderLog = 'a previously confirmed payment eas refinded; cancel goods delivery';
        log::setRealTimeLog($orderLog);
    break;
    
	case ipn::STATUS_CANCELED: // void
		/**
		 * payment was cancelled; do not deliver goods
		 */
        $orderLog = 'payment was cancelled; do not deliver goods';
        log::setRealTimeLog($orderLog);
	break;
	}
    
} catch(\Exception $e)
{
	$outputData['errorType']	= ipn::ERROR_TYPE_PERMANENT;
	$outputData['errorCode']	= ($e->getCode() != 0) ? $e->getCode() : ipn::E_VERIFICATION_FAILED_GENERAL;
    $outputData['errorMessage']	= $e->getMessage();
    
    $setRealTimeLog = [
                    "IPN - Error"  =>  "Hash Data is not matched with subject",
                    "ipnMsgError"  => 'ERROR_TYPE_PERMANENT -> E_VERIFICATION_FAILED_GENERAL'
                    ];
    log::setIpnLog($setRealTimeLog['ipnMsgError']);
    log::setRealTimeLog($setRealTimeLog);
}

/**
 * IPN Output
 */
echo json_encode($outputData);

