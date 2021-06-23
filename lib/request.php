<?php 
include_once('lib/start.php');
include_once('lib/bank.php');
include_once('lib/log.php');

class request extends start{
    public $authenticationToken;

    function __construct(){
        parent::__construct();
    }

    public function doPayment(){
      $jsonRequest = $this->setRequest();
      $result = $this->sendRequest($jsonRequest);
      print_r($result);       

      $resultObj = json_decode($result);
      if($resultObj->status){
        switch ($resultObj->data->error->code) {
            case 100:
                log::setLog($resultObj->data->error->code, $resultObj->data->error, null);
                $setRealTimeLog = 
                    [
                        "Code" =>  $resultObj->data->error->code,
                        "Message" => $resultObj->data->error->message
                    ];
                log::setLog($resultObj->data->error->code, null ,$setRealTimeLog);
                
                /**
                 * Set authenticationToken & ntpID to session
                 * Session is already started at first step (in index page) 
                 */
                $_SESSION['authenticationToken'] = $resultObj->data->customerAction->authenticationToken;
                $_SESSION['ntpID'] = $resultObj->data->payment->ntpID;

            break;
            case 0:
                $this->setLog("Card has no 3DS");
            break;
            default:
                $this->setLog($resultObj->data->error->code ." -> ".$resultObj->data->error->message);
        }
      }else {
        /**
         * There is an error / problem
         * the message error is handeling in UI, by bootstrap Alert
         */
      }
    }

    protected function setRequest() {
      $startArr = array(
        'config'  => $this->setConfig(),
        'payment' => $this->setPayment(),
        'order'   => $this->setOrder()
    );
    
    // make json Data 
    return json_encode($startArr);
    } 


    // Send request json
    protected function sendRequest($jsonStr) {
      
      $url = 'https://secure.sandbox.netopia-payments.com/payment/card/start';
      $ch = curl_init($url);

      $payload = $jsonStr; // json DATA


      // Attach encoded JSON string to the POST fields
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

      // Set the content type to application/json
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type : application/json'));
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization : '.$_SESSION['apiKey']));

      // Return response instead of outputting
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      // Execute the POST request
      $result = curl_exec($ch);
    //   die(print_r($result));
      if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                    $setRealTimeLog = 
                    [
                        "Code" =>  200,
                        "Message" => "Request sent successfully."
                    ];
                    log::setLog(200, null ,$setRealTimeLog);    

                    $arr = array(
                        'status'  => 1,
                        'code'    => $http_code,
                        'message' => "You send your request, successfully",
                        'data'    => json_decode($result)
                    );
                    break;
                case 404:  # Not Found
                    $setRealTimeLog = 
                    [
                        "Code" =>  404,
                        "Message" => "You send request to wrong URL."
                    ];
                    log::setLog(404, null ,$setRealTimeLog);    
                    
                    $arr = array(
                        'status'  => 0,
                        'code'    => $http_code,
                        'message' => "You send request to wrong URL",
                        'data'    => json_decode($result)
                    );
                    break;
                case 400:  # Bad Request
                    $setRealTimeLog = 
                    [
                        "Code" =>  400,
                        "Message" => "You send Bad Request."
                    ];
                    log::setLog(404, null ,$setRealTimeLog);
                    $arr = array(
                        'status'  => 0,
                        'code'    => $http_code,
                        'message' => "You send Bad Request",
                        'data'    => json_decode($result)
                    );
                    break;
                case 405:  # Method Not Allowed
                    $arr = array(
                        'status'  => 0,
                        'code'    => $http_code,
                        'message' => "Your method of sending data are Not Allowed",
                        'data'    => json_decode($result)
                    );
                    break;
                default:
                    $setRealTimeLog = 
                    [
                        "Message" => "Opps! Something is wrong."
                    ];
                    log::setLog("xx", null ,$setRealTimeLog);
                    $arr = array(
                        'status'  => 0,
                        'code'    => $http_code,
                        'message' => "Opps! Something is wrong, verify how you send data & try again!!!",
                        'data'    => json_decode($result)
                    );
            }
        } else {
            $arr = array(
                'status'  => 0,
                'code'    => 0,
                'message' => "Opps! There is some problem, you are not able to send data!!!"
            );
        }
      
      // Close cURL resource
      curl_close($ch);
      
      $finalResult = json_encode($arr, JSON_FORCE_OBJECT);
      return $finalResult;
    }    
}