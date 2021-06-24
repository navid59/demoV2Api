<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('lib/log.php');
include_once('lib/request.php');

$request = new request();
$request->posSignature  = 'LXTP-3WDM-WVXL-GC8B-Y5DA';
$request->notifyUrl     = 'http://35.204.43.65/demoV2/ipn.php';
$request->redirectUrl   = '';
$request->apiKey        = 'Uxf3OY--rDK3Qae8CiJJUlAcuRJFp7tzGY4M8KocQaCGyfEqUGhGskv0';
$request->isLive        = false;

$request->setSetting();
/**
 * prepare json for start action
 */
$request->jsonRequest = $request->setRequest();
log::setRealTimeLog(array('StartAction' => getenv('LOG_TXT_START_JSON') ? getenv('LOG_TXT_START_JSON') : 'START Json created' ));

$startResult = $request->startPayment();
log::setRealTimeLog(array('StartAction' => getenv('LOG_TXT_START_REQUEST_SENT') ? getenv('LOG_TXT_START_REQUEST_SENT') : 'Json sent to START endpoit' ));

/**
 * display start action result in jason format
 * to be use in the UI, ...
 */
echo $startResult;


/**
 * Depend on status :
 *  - set 'authenticationToken' & 'ntpID' in session
 *  - set Log
 */
$resultObj = json_decode($startResult);
if($resultObj->status){
    switch ($resultObj->data->error->code) {
        case 100:
            /**
             * Set authenticationToken & ntpID to session
             * Session is already started at first step (in index page) 
             */
            $_SESSION['authenticationToken'] = $resultObj->data->customerAction->authenticationToken;
            $_SESSION['ntpID'] = $resultObj->data->payment->ntpID;

            /**
             * Log
             */
            log::setLog($resultObj->data->error->code, $resultObj->data->error, array('authenticationToken' => 'authenticationToken & ntpID are set'));
            $setRealTimeLog = 
                [
                    "startResultCode" =>  $resultObj->data->error->code,
                    "startResultMessage" => $resultObj->data->error->message
                ];
            log::setRealTimeLog($setRealTimeLog);
        break;
        case 56:
            /**
             * duplicated Order ID 
             */
        break;
        case 99:
            /**
             * There is another order with a different price
             */
        break;
        case 19:
            // Expire Card Error
        break;
        case 20:
            // Founduri Error
        break;
        case 21:
            // CVV Error
        break;
        case 22:
            // CVV Error
        break;
        case 34:
            // Card Tranzactie nepermisa Error
        break;
        case 0:
            /**
             * Card has no 3DS
             */
        break;
        default:
            log::setLog($resultObj->data->error->code ." -> ".$resultObj->data->error->message);
    }
}else {
    /**
     * There is an error / problem
     * the message error is handeling in UI, by bootstrap Alert
     */
}
?>