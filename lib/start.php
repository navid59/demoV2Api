<?php 
class start {
    // protected $startJson;
    public $posSignature;
    public $posSignatureSet;
    public $notifyUrl;
    public $redirectUrl;
    public $apiKey;
    
    public $isLive;
    public $hashMethod;
    public $alg;

    
    function __construct(){
        //
    }

    protected function setConfig() {
        $config = array(
            'emailTemplate' => $_POST['emailTemplate'],
            'notifyUrl'     => $_POST['notifyUrl'],
            'redirectUrl'   => $_POST['redirectUrl'],
            'language'      => $_POST['language']
        );

        return $config;
    }

    protected function setPayment() {
        $payment = array(
            'options' => [
                'installments' => (int) 1,
                'bonus'        => (int) 0
            ],
            'instrument' => [
                'type'          => (string) "card",
                'account'       => (string) $_POST['account'],
                'expMonth'      => (int) $_POST['expMonth'],
                'expYear'       => (int) $_POST['expYear'],
                'secretCode'    => (string) $_POST['secretCode'],
                'token'         => null
            ],
            'data' => null
        );

        return $payment;
    }

    protected function setOrder() {
        $order = array(
            'ntpID'         => isset($_POST['ntpID']) ? $_POST['ntpID'] : null,
            'posSignature'  => (string) $_POST['posSignature'],
            'dateTime'      => (string) date("c", strtotime(date("Y-m-d H:i:s"))),
            'description'   => (string) "DEMO API FROM WEB",
            'orderID'       => (string) $_POST['orderID'],
            'amount'        => (float)  $_POST['amount'],
            'currency'      => (string) $_POST['currency'],
            'billing'       => [
                'email'         => (string) $_POST['billingEmail'],
                'phone'         => (string) $_POST['billingPhone'],
                'firstName'     => (string) $_POST['billingFirstName'],
                'lastName'      => (string) $_POST['billingLastName'],
                'city'          => (string) $_POST['billingCity'],
                'country'       => (string) $_POST['billingCountry'],
                'state'         => (string) $_POST['billingState'],
                'postalCode'    => (string) $_POST['billingZip'],
                'details'       => (string) "Fara Detalie"
            ],
            'shipping'      => [
                'email'         => (string) $_POST['shippingEmail'],
                'phone'         => (string) $_POST['shippingPhone'],
                'firstName'     => (string) $_POST['shippingFirstName'],
                'lastName'      => (String) $_POST['shippingLastName'],
                'city'          => (string) $_POST['shippingCity'],
                'country'       => (string) $_POST['shippingCountry'],
                'state'         => (string) $_POST['shippingState'],
                'postalCode'    => (string) $_POST['shippingZip'],
                'details'       => (string) "Fara Detalie"
            ],
            'products' => $this->setProducts(),
            'installments'  => array(
                                    'selected'  => (int) 0,
                                    'available' => [(int) 0]
                            ),
            'payload'       => null
        );

        return $order;
    }

    protected function setProducts()
    {
        foreach ($_POST['products'] as $productItem) {
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

    public function setSetting() {
        $fileTmpKey = 'certificates/setting.json';
        try {
            $keyArr = array(
                'activeKey' => $this->posSignature,
                'keySet'    => $this->posSignatureSet,
                'isLive'    => $this->isLive,
                'hashMethod'=> $this->hashMethod,
                'alg'       => $this->alg
            );

            file_put_contents($fileTmpKey, json_encode($keyArr));
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }


    public function getSetting() {
        $jsonSetting = file_get_contents('certificates/setting.json');
        $arrSetting  = json_decode($jsonSetting, true);
        return($arrSetting);
    }
}