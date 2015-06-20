<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */

/**
 * @package CriptoPay_PHP
 * @version 2.0
 */

define('DEBUG',true);
define('CIFRADO',false);

require_once('../src/autoload.php');
    
$CP_ApiId = "5575d5c6a5d92f6a3d8b4567";
$CP_ApiPassword = "CPouNCnjWrKoCKwiLYARr5Am55UwhCVC";


$CRIPTOPAY = new CRIPTOPAY_CLIENTE_API($CP_ApiId,$CP_ApiPassword);

$resumen = $CRIPTOPAY->Get("wallet","resumen21345");

var_dump($resumen);