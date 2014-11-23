<?php

global $CriptoPay_Configuracion,$CriptoPay_Divisas;

$CriptoPay_Configuracion = array(
    //"url"=>"https://cripto-pay.com/api"
    "url"=>"http://sandbox.cripto-pay.com",
    "debug"=>false
);

$CriptoPay_Divisas = array(
    array("divisa"=>"bitcoin","abr"=>"btc"),
    array("divisa"=>"litecoin","abr"=>"ltc"),
    array("divisa"=>"pesetacoin","abr"=>"ptc"),
    array("divisa"=>"dogecoin","abr"=>"dgc"),
    array("divisa"=>"spaincoin","abr"=>"spa"),
);