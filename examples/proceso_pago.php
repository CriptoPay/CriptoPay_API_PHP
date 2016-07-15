<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */

/**
 * 
 * Ejemplo funcional de la generación de un nuevo pago, así como su verificación básica.
 * 
 * @package CriptoPayApiRest
 * @subpackage Ejemplos
 * @version 2.2
 */
namespace CriptoPayApiRest\src;
use CriptoPayApiRest\src\Comun;


if(!defined('DEBUG')){
    define('DEBUG',true);
    ////Pone el sistema a trabajar en modo desarrollo(true) o producción(false)
    //En modo producción no saltan excepciones que no sean graves.
}

//Carga los ficheros necesarios y realiza comprobaciones
require_once('../src/bootstrap.php');

$CP_ApiId = ""; //Usuario de la API
$CP_ApiPassword = ""; //Pasword del Usuario de la API
$CP_ApiCertificados = __DIR__."/../cert/";  //Ruta hacia los certificados descargados

Comun\LOG::Iniciar(LOG_DEBUG,LOG_INFO,"logCriptoPayApiRest.csv");

//Instancia del Objeto para realizar la acciones
$CRIPTOPAY = new Comun\CriptoPayApiRest($CP_ApiId,$CP_ApiPassword,$CP_ApiCertificados);

//Creamos los parametros para el pago a generar
$pago = array(
    "total" => (float)12.36, // Obligatorio
    "divisa" => "EUR",      //Obligatorio
    "concepto" => "Ejemplo básico de pago", //Obligatorio
    "URL_OK" => "https://miweb.com/pago.php?estado=ok", //Opcionales
    "URL_KO" => "https://miweb.com/pago.php?estado=ko", //Opcionales
    "IPN" => "https://miweb.com/pago.php?estado=ipn"    //Opcionales
);
//En la parte de docs existe un excel con todos los parámetros disponibles.

//Agregamos los parámetros a la consulta
$CRIPTOPAY->Set($pago);

//Ejecutamos la función en sí.
$respuesta = $CRIPTOPAY->Get("PAGO","GENERAR");
var_dump($respuesta); //En este caso la respuesta siempre será el id

//Verificamos que el id exista
if(isset($respuesta->idpago)){
    //Si el pago está complto mandamos al usuario a la página donde realizará el pago
    //header("Location: http://sandbox.cripto-pay.com/pago/".$respuesta->idpago); //DEBUG y pagos autovalidados
    //header("Location: https://cripto-pay.com/pago/".$respuesta->idpago); // PRODUCCION
    //UNA VEZ PROCESADO EL PAGO EL USUARIO SERÁ RETORNADO A URL_OK SI TODO HA IDO BIEN
    //EL USUARIO SERÁ ENVIADO A URL_KO SI PASAN MAS DE X MINUTOS SIN REALIZAR EL PAGO O PULSA EN CANCELAR.
    
    //EL IPN SERÁ EJECUTADO UNA VEZ EL SERVIDOR VALIDE INTERNAMENTE EL PAGO COMPLEMTAMETNE.
    
}else{
    echo "Ups! Algo ha ido mal al generar el pago";
}

//Verificamos ahora el estado actual del pago
//Se puede mantener este canal abierto con consultas recurrentes pero ocupará muchos recursos.
//Recomendamos encarecidamente el uso de IPN -> Instant Payment Notification.
$CRIPTOPAY->Set("idpago",$respuesta->idpago);
$EstadoPago = $CRIPTOPAY->Get("PAGO", "ESTADO");
var_dump($EstadoPago);