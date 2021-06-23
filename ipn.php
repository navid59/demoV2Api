<?php
session_start();
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


// Error code defination
const E_VERIFICATION_FAILED_GENERAL			= 0x10000101;
const E_VERIFICATION_FAILED_SIGNATURE		= 0x10000102;
const E_VERIFICATION_FAILED_NBF_IAT			= 0x10000103;
const E_VERIFICATION_FAILED_EXPIRED			= 0x10000104;
const E_VERIFICATION_FAILED_AUDIENCE		= 0x10000105;
const E_VERIFICATION_FAILED_TAINTED_PAYLOAD	= 0x10000106;
const E_VERIFICATION_FAILED_PAYLOAD_FORMAT	= 0x10000107;

const ERROR_TYPE_NONE 		= 0x00;
const ERROR_TYPE_TEMPORARY 	= 0x01;
const ERROR_TYPE_PERMANENT 	= 0x02;

/**
 * available statuses for the purchase class (prcStatus)
 */
const STATUS_NEW 									= 1;	//0x01; //new purchase status
const STATUS_OPENED 								= 2;	//OK //0x02; // specific to Model_Purchase_Card purchases (after preauthorization) and Model_Purchase_Cash
const STATUS_PAID 									= 3;	//OK //0x03; // capturate (card)
const STATUS_CANCELED 								= 4;	//0x04; // void
const STATUS_CONFIRMED 								= 5;	//OK //0x05; //confirmed status (after IPN)
const STATUS_PENDING 								= 6;	//0x06; //pending status
const STATUS_SCHEDULED 								= 7;	//0x07; //scheduled status, specific to Model_Purchase_Sms_Online / Model_Purchase_Sms_Offline
const STATUS_CREDIT 								= 8;	//0x08; //specific status to a capture & refund state
const STATUS_CHARGEBACK_INIT 						= 9;	//0x09; //status specific to chargeback initialization
const STATUS_CHARGEBACK_ACCEPT 						= 10;	//0x0a; //status specific when chargeback has been accepted
const STATUS_ERROR 									= 11;	//0x0b; // error status
const STATUS_DECLINED 								= 12;	//0x0c; // declined status
const STATUS_FRAUD 									= 13;	//0x0d; // fraud status
const STATUS_PENDING_AUTH 							= 14;	//0x0e; //specific status to authorization pending, awaiting acceptance (verify)
const STATUS_3D_AUTH 								= 15;	//0x0f; //3D authorized status, speficic to Model_Purchase_Card
const STATUS_CHARGEBACK_REPRESENTMENT 				= 16;	//0x10;
const STATUS_REVERSED 								= 17;	//0x11; //reversed status
const STATUS_PENDING_ANY 							= 18;	//0x12; //dummy status
const STATUS_PROGRAMMED_RECURRENT_PAYMENT 			= 19;	//0x13; //specific to recurrent card purchases
const STATUS_CANCELED_PROGRAMMED_RECURRENT_PAYMENT 	= 20;	//0x14; //specific to cancelled recurrent card purchases
const STATUS_TRIAL_PENDING							= 21;	//0x15; //specific to Model_Purchase_Sms_Online; wait for ACTON_TRIAL IPN to start trial period
const STATUS_TRIAL									= 22;	//0x16; //specific to Model_Purchase_Sms_Online; trial period has started
const STATUS_EXPIRED								= 23;	//0x17; //cancel a not payed purchase 

// Log
$setRealTimeLog = ["IPN"    =>  "IPN Is hitting"];
log::setRealTimeLog($setRealTimeLog);

/**
 * get defined keys
 */
$ipn = new ipn(); // New IPN OBJ
$ntpSetting = $ipn->getSetting();
/**
 *  Fetch all HTTP request headers
 */
$aHeaders = apache_request_headers();

// Log
log::setIpnLog($aHeaders);
log::setIpnLog($_REQUEST);

/**
 *  check if header exist in HTTP request 
 */
if(!is_array($aHeaders))
{
    $setRealTimeLog['missingHeader'] = "headers are missing";
    echo $setRealTimeLog['missingHeader'] . PHP_EOL;
    log::setRealTimeLog($setRealTimeLog);
	exit;
}

/**
 *  fetch Verification-token from HTTP header 
 */
$verificationToken = null;
foreach($aHeaders as $headerName=>$headerValue)
{
	if(strcasecmp('Verification-token', $headerName) == 0)
	{
        $verificationToken = $headerValue;
		break;
	}
}

/**
 *  check if Verification-token exist / not null 
 */
if($verificationToken === null)
{
    $setRealTimeLog['missingVerificationToken'] = "Verification-token is missing";
    echo $setRealTimeLog['missingVerificationToken'] . PHP_EOL;
    log::setRealTimeLog($setRealTimeLog);
	exit;
}

/////////////////// ---  PROBLEM START ///////////////////////////////////////////
// Maybe be change to read as string or get option to read from File or from String
//////////////////////////////////////////////////////////////////////////////////

// $publickKeyFilePath = 'certificates/live.LXTP-3WDM-WVXL-GC8B-Y5DA.public.cer';
$publickKeyFilePath = 'certificates/'.$ntpSetting['activeKey'].'.public.cer';
if (file_exists($publickKeyFilePath)) {
    $publicKey = openssl_pkey_get_public('file://' . $publickKeyFilePath);
    if($publicKey === false)
    {
        $setRealTimeLog['notValidPublicKey'] = '`' . $publickKeyFilePath . '` is not a valid public key'; 
        echo $setRealTimeLog['notValidPublicKey'] . PHP_EOL;
        log::setRealTimeLog($setRealTimeLog);
        exit;
    }
    /**
     * Default IPN response, 
     * will change if there is any problem
     */
    $outputData = array(
        'errorType'		=> ERROR_TYPE_NONE,
        'errorCode' 	=> null,
        'errorMessage'	=> ''
    );
} else {
    $setRealTimeLog['missingPublicKey'] = "The public key $publickKeyFilePath does not exist"; 
    echo $setRealTimeLog['missingPublicKey'] . PHP_EOL; // IPN Response
    
    // Log
    log::setRealTimeLog($setRealTimeLog);
    exit;
}
/////////////////// --- PROBLEM END ///////////////////////////////////////////

$HTTP_RAW_POST_DATA = file_get_contents('php://input');
// $input = json_decode($HTTP_RAW_POST_DATA); // can be get all recived data from $input



 /**
  * Analising verification token
  * Just to make sure if Type is JWT & Use right encoding/decoding algorithm 
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
   * The name of the alg defined in header of JWT
   * Just in case we set the default algorithm
   * Default alg is RS512
   */
  if(!isset($ntpSetting['alg']) || $ntpSetting['alg']==null){
    throw new \Exception('IDS_Service_IpnController__INVALID_JWT_ALG');
    exit;
  }
  $jwtAlgorithm = !is_null($jwtHeader->alg) ? $jwtHeader->alg : $ntpSetting['alg'] ;

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
    if(empty($objJwt->aud) || $objJwt->aud != $ntpSetting['activeKey']){
        throw new \Exception('IDS_Service_IpnController__INVALID_SIGNATURE');
        exit;
    }

    if(!in_array($objJwt->aud,$ntpSetting['keySet'],true)) {
        throw new \Exception('IDS_Service_IpnController__INVALID_SIGNATURE_SET');
        exit;
    }
    
    if(!isset($ntpSetting['hashMethod']) || $ntpSetting['hashMethod']==null){
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
    $payloadHash = base64_encode(hash ($ntpSetting['hashMethod'], $payload, true ));
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
	case STATUS_NEW:
	case STATUS_CHARGEBACK_INIT: // chargeback initiat
	case STATUS_CHARGEBACK_ACCEPT: // chargeback acceptat
	case STATUS_SCHEDULED:
	case STATUS_3D_AUTH:
	case STATUS_CHARGEBACK_REPRESENTMENT:
	case STATUS_REVERSED:
	case STATUS_PENDING_ANY:
	case STATUS_PROGRAMMED_RECURRENT_PAYMENT:
	case STATUS_CANCELED_PROGRAMMED_RECURRENT_PAYMENT:
	case STATUS_TRIAL_PENDING: //specific to Model_Purchase_Sms_Online; wait for ACTON_TRIAL IPN to start trial period
	case STATUS_TRIAL: //specific to Model_Purchase_Sms_Online; trial period has started
	case STATUS_EXPIRED: //cancel a not payed purchase 
	case STATUS_OPENED: // preauthorizate (card)
	case STATUS_PENDING:
	case STATUS_ERROR: // error
	case STATUS_DECLINED: // declined
    case STATUS_FRAUD: // fraud
        /**
		 * payment status is in fraud, reviw the payment
		 */
        $orderLog = 'payment in reviwing';
        log::setRealTimeLog($orderLog);
    break;
	case STATUS_PENDING_AUTH: // in asteptare de verificare pentru tranzactii autorizate
		/**
		 * update payment status, last modified date&time in your system
		 */
        $orderLog = 'update payment status, last modified date&time in your system';
        log::setRealTimeLog($orderLog);
    break;
    
	case STATUS_PAID: // capturate (card)
	case STATUS_CONFIRMED:
		/**
		 * payment was confirmed; deliver goods
		 */
        $orderLog = 'payment was confirmed; deliver goods';
        log::setRealTimeLog($orderLog);
    break;
    
	case STATUS_CREDIT: // capturate si apoi refund
		/**
		 * a previously confirmed payment eas refinded; cancel goods delivery
		 */
        $orderLog = 'a previously confirmed payment eas refinded; cancel goods delivery';
        log::setRealTimeLog($orderLog);
    break;
    
	case STATUS_CANCELED: // void
		/**
		 * payment was cancelled; do not deliver goods
		 */
        $orderLog = 'payment was cancelled; do not deliver goods';
        log::setRealTimeLog($orderLog);
	break;
	}
    
} catch(\Exception $e)
{
	$outputData['errorType']	= ERROR_TYPE_PERMANENT;
	$outputData['errorCode']	= ($e->getCode() != 0) ? $e->getCode() : E_VERIFICATION_FAILED_GENERAL;
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

