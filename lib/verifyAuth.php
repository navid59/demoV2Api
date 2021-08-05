<?php
include_once('classes/log.php');
include_once('../lib/request.php');


class VerifyAuth extends Request {
    public $paRes;
    public function __construct(){
        parent::__construct();
    }

    public function setVerifyAuth() {
        $paymentCartVerifyAuthParam = [
            "authenticationToken" => (string) $this->authenticationToken,
            "ntpID" => (string) $this->ntpID,
            "formData" => [
                "paRes" => (string) $this->paRes
            ]
        ];

        return (json_encode($paymentCartVerifyAuthParam));
    }

    // Send request to /payment/card/verify-auth
    public function sendRequestVerifyAuth($jsonStr) {  
        $url = $this->isLive ? 'https://secure.netopia-payments.com/payment/card/verify-auth' : 'https://secure.sandbox.netopia-payments.com/payment/card/verify-auth';
        $ch = curl_init($url);
    
        $payload = $jsonStr; // json DATA
    
        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    
        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type : application/json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization : $this->apiKey"));
    
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
                            "Message" => "Request sent successfully for /payment/card/verify-auth."
                        ];
                        log::setLog(200, null ,$setRealTimeLog);    
    
                        $arr = array(
                            'status'  => 1,
                            'code'    => $http_code,
                            'message' => "Successfully verify authentication ",
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
                            'message' => "You send verify-auth request to wrong URL",
                            'data'    => json_decode($result)
                        );
                        break;
                    case 400:  # Bad Request
                        $setRealTimeLog = 
                        [
                            "Code" =>  400,
                            "Message" => "You send Bad Request to verify-auth."
                        ];
                        log::setLog(404, null ,$setRealTimeLog);
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "You send Bad Request to verify-auth",
                            'data'    => json_decode($result)
                        );
                        break;
                    case 405:  # Method Not Allowed
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "Your method of sending data to verify-auth are Not Allowed",
                            'data'    => json_decode($result)
                        );
                        break;
                    default:
                        $setRealTimeLog = 
                        [
                            "Message" => "Opps! Something is wrong, can not send data to verify-auth."
                        ];
                        log::setLog("xx", null ,$setRealTimeLog);
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "Opps! Something is wrong, verify how you send data to verify-auth & try again!!!",
                            'data'    => json_decode($result)
                        );
                }
            } else {
                $arr = array(
                    'status'  => 0,
                    'code'    => 0,
                    'message' => "Opps! There is some problem, you are not able to send data to verify-auth!!!"
                );
            }
        
        // Close cURL resource
        curl_close($ch);
        
        $finalResult = json_encode($arr, JSON_FORCE_OBJECT);
        return $finalResult;
    }
}