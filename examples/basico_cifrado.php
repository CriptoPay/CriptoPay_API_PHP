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

/**
 * EJEMPLO COMPLETO PROCESAR UN PAGO
 */

//Creamos los parametros para el pago
$pago = array(
    "total" => (float)12.36,
    "divisa" => "EUR",
    "concepto" => "Ejemplo básico de pago",
    "URL_OK" => "https://miweb.com/pago.php?estado=ok",
    "URL_KO" => "https://miweb.com/pago.php?estado=ko",
    "IPN" => "https://miweb.com/pago.php?estado=ipn"
);

//Agregamos los parámetros a la consulta
$CRIPTOPAY->Set($pago);

//Ejecutamos la función en sí.
$respuesta = $CRIPTOPAY->Get("PAGO","GENERAR");

//Verificamos que la función no haya retornado un error
if(isset($respuesta['idpago'])){
    //Si el pago está complto mandamos al usuario a la página donde realizará el pago
    header("Location: https://cripto-pay.com/pago/".$respuesta['idpago']);
    //UNA VEZ PROCESADO EL PAGO EL USUARIO SERÁ RETORNADO A URL_OK SI TODO HA IDO BIEN
    //EL USUARIO SERÁ ENVIADO A URL_KO SI PASAN MAS DE X MINUTOS SIN REALIZAR EL PAGO O PULSA EN CANCELAR.
    
    //EL IPN SERÁ EJECUTADO UNA VEZ EL SERVIDOR VALIDE INTERNAMENTE EL PAGO COMPLEMTAMETNE.
    
}else{
    echo "Ups! Algo ha ido mal al generar el pago";
}

/**
 * EJEMPLO COMPLETO VERIFICAR UN PAGO CONCRETO
 */
$CRIPTOPAY->Set("idpago",$respuesta['idpago']);
$EstadoPago = $CRIPTOPAY->Get("PAGO", "ESTADO");
var_dump($EstadoPago);

/**
 * CONSULTA ESTANDAR
 */
$resultado = $CRIPTOPAY->Get("AMBITO","FUNCION");
var_dump($resultado);

/**
 * RESPUESTA ESTANDAR
 * 
 * $respuesta = Array(
 *      "nonce"=>NONCE NUMERICO,
 *      "error"=>NUMERO ERROR,
 *      "respuesta"=> Array(
 *          PARAMETRO=>VALOR
 *      )
 * )
 */