<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */

/**
 * @package CriptoPay_PHP
 * @version 2.0
 */

class CRIPTOPAY_CLIENTE_API{
    
    private $ApiId,$ApiPassword,$ApiNonce;
    public static $SESSION = null;
    private static $NONCE = false;
    
    protected $Parametros;
    
    private $BBDD,$CLIENTE,$BD_SESSION,$BD_API;
    protected $idapi,$RESPUESTA = array();
    
    
    public function __construct($CP_ApiId,$CP_ApiPassword) {
        $this->ApiId = $CP_ApiId;
        $this->ApiPassword = $CP_ApiPassword;
    }
    
    /**
     * Adición de parametros a enviar
     * @param Array|String $clave Clave del array o array completo a agregar directamente
     * @param String $parametro Elementos a agregar con la clave enviada. No se tienen en cuenta si se pasa un Array en $Clave
     */
    public function Set($clave,$parametro=null){
        if(!is_array($clave)){
            $this->Parametros[$clave] = $parametro;
        }else{
            $this->Parametros = array_merge($this->Parametros,$clave);
        }
    }
    
    /**
     * Envío de peticiones al servidor de la API
     * @param String $ambito Ámbito sobre el que actuar
     * @param String $funcion Función a ejecutar
     */
    public function Get($ambito,$funcion){
        if(is_null(self::$SESSION)){
            $this->ObtenerSesion();
        }
        return $this->Enviar($ambito, $funcion, $this->Parametros);
    }
    
    private function ObtenerSesion(){
        $this->Enviar("session", "code");
    }
    
    
    protected function Enviar($ambito,$funcion){
        $ch = curl_init("http://api.cripto-pay.com/".$ambito."/".$funcion);
                
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25); //timeout in seconds
        
        if(is_null(self::$SESSION)){
            $peticion['ApiId']=$this->ApiId;
            $peticion['ApiPassword']=  hash_hmac('SHA512', $this->ApiPassword, $this->ApiId);
        }else{
            $peticion['session']=self::$SESSION;
            $peticion['datos']=json_encode($this->Parametros);;
            $peticion['nonce']= self::$NONCE;
        }
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $peticion);
        
        $respuesta_server = curl_exec($ch);
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }
        $estado_HTTP = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($estado_HTTP != 200 && $estado_HTTP != 500) {
            var_dump($respuesta_server);
            throw new Exception(sprintf('Curl response http error code "%s"', curl_getinfo($ch, CURLINFO_HTTP_CODE)));
        }
        if(is_null(self::$SESSION)){
            if(strlen($respuesta_server)==24){
                self::$SESSION = $respuesta_server;
            }else{
                var_dump($respuesta_server);
                throw new CriptoPay_Exception("Hay suplantación de sessión");
            }
            return true;
        }else{
            
            if(!$this->isJson($respuesta_server)){
                var_dump($respuesta_server);
                throw new CriptoPay_Exception("Datos mal formados");
            }else{
                $respuesta_server = json_decode($respuesta_server);
            }
            
            if($respuesta_server->nonce != self::$NONCE){
                throw new CriptoPay_Exception("Hay suplantación de identidad");
            }
        }
        curl_close($ch);
        return $respuesta_server;
    }
        
    public function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }    
    
    
}

class CriptoPay_Exception extends Exception{
    
}
