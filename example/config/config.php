<?php
$products = array(
    array(
        'id' => 1,
        'pName'     => 'T-shirt Alfa',
        'pDetail'   => 'De Vara',
        'pPrice'    => rand(10, 50),
        'pCode'     =>  str_shuffle('CODE1234567890'),
        'pCategory' => "Fashion",
        "pVat"      => 0
    ),
    array(
        'id' => 2,
        'pName' => 'T-shirt Beta',
        'pDetail' => 'De Iarna',
        'pPrice' => rand(10, 50),
        'pCode'     =>  str_shuffle('CODE1234567890'),
        'pCategory' => "Fashion",
        "pVat"      => 0
    ),
    array(
        'id' => 3,
        'pName' => 'T-shirt Gamma',
        'pDetail' => 'De Dama',
        'pPrice' => mt_rand(50, 1000) / 10,
        'pCode'     =>  str_shuffle('CODE1234567890'),
        'pCategory' => "Fashion",
        "pVat"      => 0
    ),
    array(
        'id' => 4,
        'pName' => 'T-shirt Delta',
        'pDetail' => 'De Copi',
        'pPrice' => rand(10, 50),
        'pCode'     =>  str_shuffle('CODE1234567890'),
        'pCategory' => "Fashion",
        "pVat"      => 0
    )
);


$billingShippingInfo = array(
    array(
        'id'         => 1,
        'firstName'  => 'Client prenume '.str_shuffle('rand123'),
        'lastName'   => 'Client nume '.str_shuffle('rand123'),
        'email'      => 'clientemail'.rand(1,100).'@'.str_shuffle('domain345').'.com',
        'phone'      => str_shuffle('1234567890'),
        'address'    => 'Sos '.rand(1, 30).' Dec , Strada Ferdinand, nr '.rand(1, 50),
        'address2'   => 'Ap '.rand(1, 80),
        'zip'        => str_shuffle('123456'),
    ),
);

$colorRange = array("Red", "Green", "Blue", "Black", "Yellow", "grey", "beige", "silver", "indigo", "orange" );
