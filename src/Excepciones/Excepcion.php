<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */
namespace CriptoPayApiRest\src\Excepciones;
use CriptoPayApiRest\src\Comun;
/**
 * 
 * Excepciones para la API REST de CriptoPay
 * 
 * @package CriptoPayApiRest
 * @version 2.2
 */

class Excepcion extends \Exception{
    public function __construct($message, $code=null, $previous=null) {
        Comun\Log::Critical($message);
        parent::__construct($message, $code, $previous);
    }
    public function __toString() {
        return $this->getMessage();
    }
    
}