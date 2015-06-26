<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */

/**
 * 
 * Versión para el trabajo con el cifrado adicional
 * 
 * @package CriptoPay_PHP
 * @version 2.1
 */

if(!defined('DEBUG')){
    define('DEBUG',true);
}

if(!defined('CIFRADO')){
    define('CIFRADO',true);
}

require_once('../src/autoload.php');

$CP_ApiId = "xxxxxxxxxxxxxx";
$CP_ApiPassword = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$CP_ApiCertificados = "/ruta/hasta/el/certificado/";


$CRIPTOPAY = new CRIPTOPAY_CLIENTE_API($CP_ApiId,$CP_ApiPassword,$CP_ApiCertificados);

$resumen = $CRIPTOPAY->Get("AMBITO","FUNCION");

var_dump($resumen);