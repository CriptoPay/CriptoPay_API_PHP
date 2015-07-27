<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */

/*
 * Bootstrap CriptoPay V2.2
 */

namespace CriptoPayApiRest\src;
use CriptoPayApiRest\src\Comun;

require_once __DIR__ . '/Comun/AutoLoader.php';

define('DOMINIO','criptopay_api_v2');
define('LANG_DEFECTO','es_ES.utf8');

putenv("LANG=".LANG_DEFECTO); 
setlocale(LC_ALL, LANG_DEFECTO);

bindtextdomain(DOMINIO, '/locale'); 
textdomain(DOMINIO);

$autoloader = new Comun\AutoLoader(__NAMESPACE__, dirname(__DIR__));
$autoloader->register();