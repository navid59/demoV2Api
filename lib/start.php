<?php 
class start {
    public $posSignature;
    // public $posSignatureSet;
    public $notifyUrl;
    public $redirectUrl;
    public $apiKey;
    
    public $isLive;
    // public $hashMethod;
    // public $alg;

    
    function __construct(){
        //
    }

        // Send request json
        protected function sendRequest($jsonStr) {
            if(!isset($this->apiKey) || is_null($this->apiKey)) {
                throw new \Exception('INVALID_APIKEY');
                exit;
            }

            $url = $this->isLive ? 'https://secure.netopia-payments.com/payment/card/start' : 'https://secure.sandbox.netopia-payments.com/payment/card/start';
            $ch = curl_init($url);
            
            $payload = $jsonStr; // json DATA
      
      
            // Attach encoded JSON string to the POST fields
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      
            // Set the content type to application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type : application/json'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization : '.$this->apiKey));
      
            // Return response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
            // Execute the POST request
            $result = curl_exec($ch);
            
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
                    break;
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

    /**
     * Set the setting in json file
     */
    public function setSetting() {
        $fileTmpKey = 'certificates/setting.json';
        try {
            $keyArr = array(
                // 'activeKey' => $this->posSignature,
                // 'keySet'    => $this->posSignatureSet,
                // 'isLive'    => $this->isLive,
                // 'hashMethod'=> $this->hashMethod,
                // 'alg'       => $this->alg
            );

            file_put_contents($fileTmpKey, json_encode($keyArr));
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    /**
     * get setting value from json file
     */
    public function getSetting() {
        $jsonSetting = file_get_contents('certificates/setting.json');
        $arrSetting  = json_decode($jsonSetting, true);
        return($arrSetting);
    }
}