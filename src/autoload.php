<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */

/**
 * @package CriptoPay_PHP
 * @version 2.1
 */



if(DEBUG){
    define("SERVIDOR","http://sandbox.cripto-pay.com/");
    ini_set('display_errors', '1');
    error_reporting(-1);    
}else{
    define("SERVIDOR","https://api.cripto-pay.com/");
}


if(CIFRADO){
    require_once 'CriptoPay_Cliente_API_Cifrado.php';
}else{
    require_once 'CriptoPay_Cliente_API.php';
}