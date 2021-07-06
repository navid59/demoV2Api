<?php 
class Authorize extends Start{
    public $backUrl;
    public $paReq;
    
    function __construct(){
        parent::__construct();
    }

    public function validateParam() {
        if(!isset($this->apiKey) || empty($this->apiKey)){
            throw new \Exception('apiKey is not defined');
            exit;
        }

        if(!isset($this->paReq) || empty($this->paReq)){
            throw new \Exception('paReq Url is not defined');
            exit;
        }

        if(!isset($this->backUrl) || empty($this->backUrl)){
            throw new \Exception('back Url is not defined');
            exit;
        }

        if(!isset($this->bankUrl) || empty($this->bankUrl)){
            throw new \Exception('Bank Url is not defined for authorizing');
            exit;
        }

        $this->bankUrl = $this->validateBankUrl($this->bankUrl);
        
    }

    /**
    * to Verify bank URL
    * if is content "localhost" will replace with defulte Sandbox url
    * localhost:8080 / localhost:8088 
    */
    public function validateBankUrl($bankUrl) {
        if (strpos($bankUrl, 'localhost') !== false) {
            return ("https://secure.sandbox.netopia-payments.com/sandbox/authorize");
        }
        return $bankUrl;
    }
}