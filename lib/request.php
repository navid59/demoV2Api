<?php 
include_once('lib/start.php');
include_once('lib/bank.php');

class request extends start{
    public $authenticationToken;
    public $jsonRequest;

    function __construct(){
        parent::__construct();
    }

    protected function setConfig() {
        $config = array(
            'emailTemplate' => $_POST['emailTemplate'],
            'notifyUrl'     => $this->notifyUrl,
            'redirectUrl'   => $this->redirectUrl,
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

    /**
     * Set the order
     */
    protected function setOrder() {
        $order = array(
            'ntpID'         => isset($_POST['ntpID']) ? $_POST['ntpID'] : null,
            'posSignature'  => (string) $this->posSignature,
            'dateTime'      => (string) date("c", strtotime(date("Y-m-d H:i:s"))),
            'description'   => (string) "DEMO API FROM WEB - V2",
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

    /**
     * Set the Product Items
     */
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

    /**
     * Set the request to payment
     * @output json
     */
    public function setRequest() {
        $startArr = array(
          'config'  => $this->setConfig(),
          'payment' => $this->setPayment(),
          'order'   => $this->setOrder()
      );
      
      // make json Data 
      return json_encode($startArr);
    }

    public function startPayment(){
      $result = $this->sendRequest($this->jsonRequest);
      return($result);
    }    
}