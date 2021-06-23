<?php class L {
const payment_api_with_card = 'Payment API with card';
const checkout_title = 'Below is a PHP example to make paymets using NETOPIA Payments API.';
const tabs_MakePayment = 'Make Payment';
const tabs_PaymentStatus = 'Payment Status';
const tabs_ExpirePayment = 'Expire Payment';
const tabs_ConfirmURL = 'Confirm URL';
const tabs_ReturnURL = 'Return URL';
const tabs_APIDocument = 'API Document';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}