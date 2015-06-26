<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */

/**
 * @package CriptoPay_PHP
 * @version 2.0
 */
if(!defined('DEBUG')){
    define('DEBUG',true);
}

if(!defined('CIFRADO')){
    define('CIFRADO',false);
}

require_once('../src/autoload.php');
    
$CP_ApiId = "xxxxxxxxxxxxxx";
$CP_ApiPassword = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";


$CRIPTOPAY = new CRIPTOPAY_CLIENTE_API($CP_ApiId,$CP_ApiPassword);

$resumen = $CRIPTOPAY->Get("wallet","resumen21345");

var_dump($resumen);