<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('classes/log.php');
include_once('../lib/request.php');

$request = new Request();
$request->posSignature  = 'LXTP-3WDM-WVXL-GC8B-Y5DA';                                 // Your signiture ID hear
$request->apiKey        = 'Uxf3OY--rDK3Qae8CiJJUlAcuRJFp7tzGY4M8KocQaCGyfEqUGhGskv0'; // Your API key hear // Valid Api cod Oct-2021
$request->isLive        = false;
$request->notifyUrl     = 'http://35.204.43.65/demoV2/example/ipn.php';               // Your IPN URL
$request->redirectUrl   = 'http://35.204.43.65/demoV2/example/backUrl.php';           // Your backURL


/**
 * Prepare json for start action
 */

 /** - Config section  */
 $configData = [
    'emailTemplate' => $_POST['emailTemplate'],
    'notifyUrl'     => $request->notifyUrl,
    'redirectUrl'   => $request->redirectUrl,
    'language'      => $_POST['language']
    ];
 
 /** - Payment section  */
 $cardData = [
    'account'       => $_POST['account'],
    'expMonth'      => $_POST['expMonth'],
    'expYear'       => $_POST['expYear'],
    'secretCode'    => $_POST['secretCode']
 ]; 

 /** - 3DS section  */
 $threeDSecusreData =  $_POST['clientInfo']; 

 /** - Order section  */
$orderData = new \StdClass();
 
$orderData->description             = isset($_POST['description']) ?  $_POST['description'] :  "DEMO API FROM WEB - V2";
$orderData->orderID                 = $_POST['orderID'];
$orderData->amount                  = $_POST['amount'];
$orderData->currency                = $_POST['currency'];

$orderData->billing                 = new \StdClass();
$orderData->billing->email          = $_POST['billingEmail'];
$orderData->billing->phone          = $_POST['billingPhone'];
$orderData->billing->firstName      = $_POST['billingFirstName'];
$orderData->billing->lastName       = $_POST['billingLastName'];
$orderData->billing->city           = $_POST['billingCity'];
$orderData->billing->country        = $_POST['billingCountry'];
$orderData->billing->state          = $_POST['billingState'];
$orderData->billing->postalCode     = $_POST['billingZip'];
$orderData->billing->details        = isset($_POST['details']) ?  $_POST['details'] : "Fara Detalie";

$orderData->shipping                = new \StdClass();
$orderData->shipping->email         = $_POST['shippingEmail'];
$orderData->shipping->phone         = $_POST['shippingPhone'];
$orderData->shipping->firstName     = $_POST['shippingFirstName'];
$orderData->shipping->lastName      = $_POST['shippingLastName'];
$orderData->shipping->city          = $_POST['shippingCity'];
$orderData->shipping->country       = $_POST['shippingCountry'];
$orderData->shipping->state         = $_POST['shippingState'];
$orderData->shipping->postalCode    = $_POST['shippingZip'];
$orderData->shipping->details       = isset($_POST['details']) ?  $_POST['details'] : "Fara Detalie";

$orderData->products                = setProducts($_POST['products']);

/**
 * Assign values and generate Json
 */
$request->jsonRequest = $request->setRequest($configData, $cardData, $orderData, $threeDSecusreData);

/**
 * Send Json to Start action 
 */
$startResult = $request->startPayment();

/**
 * display result of start action in jason format
 * to be use in the UI, ...
 */
echo $startResult;


/**
 * Depend on status :
 *  - set 'authenticationToken' , 'ntpID' & 'authorizeUrl' in session
 */
$resultObj = json_decode($startResult);
// print_r($resultObj);

if($resultObj->status){
    switch ($resultObj->data->error->code) {
        case 100:
            /**
             * Set authenticationToken & ntpID to session 
             */
            if($resultObj->data->customerAction->type == "Authentication3D") {
                $_SESSION['authorizeUrl'] = $resultObj->data->customerAction->url;
            }

            $_SESSION['authenticationToken'] = $resultObj->data->customerAction->authenticationToken;
            $_SESSION['ntpID'] = $resultObj->data->payment->ntpID;
        break;
        case 0:
            /**
             * Card has no 3DS
             */
            $_SESSION['ntpID']   = $resultObj->data->payment->ntpID;
            $_SESSION['token']   = $resultObj->data->payment->token;
            $_SESSION['orderID'] = $orderData->orderID;
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
        default:
            //
    }
}else {
    /**
     * There is an error / problem
     * the message error is handeling in UI, by bootstrap Alert
     */
}

/**
 * Set the Product Items
 */
function setProducts($productList)
    {
        foreach ($productList as $productItem) {
            $proArr[] = [
                'name'     => (string) $productItem['pName'],
                'code'     => (string) $productItem['pCode'],
                'category' => (string) $productItem['pCategory'],
                'price'    => (int) $productItem['pPrice'],
                'vat'      => (int) $productItem['pVat']
            ];
        }
        return $proArr;
    }

?>