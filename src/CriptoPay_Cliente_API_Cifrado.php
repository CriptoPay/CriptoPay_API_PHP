<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */

/**
 * 
 * Versión para el trabajo con datos cifrados en ambos sentidos.
 * 
 * @package CriptoPay_PHP
 * @version 2.1
 */

class CRIPTOPAY_CLIENTE_API{
    
    private $ApiId,$ApiPassword,$ApiCertificados,$ApiNonce,$ApiServidor;
    private static $SESSION = null;
    private static $KeyPublica = null;
    private static $KeyPrivada = null;
    private static $NONCE = false;
    
    protected $Parametros = array();
    
    private $Cert_CRT,$Cert_KEY,$Cert_PASS,$BBDD,$CLIENTE,$BD_SESSION,$BD_API;
    protected $idapi,$RESPUESTA = array();
    
    
    public function __construct($CP_ApiId,$CP_ApiPassword,$CP_ApiCertificados,$CP_ApiServidor=null) {
        $this->ApiId = $CP_ApiId;
        $this->ApiPassword = $CP_ApiPassword;       
        $this->ApiCertificados = $CP_ApiCertificados;
        if(DEBUG){
            $this->ApiServidor = "http://sandbox.cripto-pay.com";
        }elseif(!is_null($CP_ApiServidor)){
            $this->ApiServidor = $CP_ApiServidor;
        }else{
            $this->ApiServidor = "https://api.cripto-pay.com";
        }
        
        //Limpiamos el última slash para prevenir errores
        $this->ApiServidor = (substr($this->ApiServidor,-1)=="/")?substr($this->ApiServidor, 0,strlen($this->ApiServidor)-2):$this->ApiServidor;
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
        $ch = curl_init($this->ApiServidor.$ambito."/".$funcion);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CAINFO, "cert.crt");
        
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
            $peticion['datos']=$this->Encriptar();
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
            $claro = $this->Desencriptar($respuesta_server);
            if(strlen($claro)==24){
                self::$SESSION = $claro;
            }else{
                var_dump($claro);
                throw new CriptoPay_Exception("Hay suplantación de sessión");
            }
            return true;
        }else{
            $claro = $this->Desencriptar($respuesta_server);
            //$respuesta = json_decode($claro);
            if($claro->nonce != self::$NONCE){
                throw new CriptoPay_Exception("Hay suplantación de identidad");
            }
        }
        curl_close($ch);
        return $claro;
    }
    
    
    
    
    private function CargarKeys(){        
        
        if(!file_exists($this->ApiCertificados."CriptoPay_ApiCert_".$this->ApiId.".crt")){
            throw new CriptoPay_Exception("Falta el certificado público");
        }
        if(!file_exists($this->ApiCertificados."CriptoPay_ApiKey_".$this->ApiId.".key")){
            throw new CriptoPay_Exception("Falta el certificado privado");
        }
        
        $fp=fopen ($this->ApiCertificados."CriptoPay_ApiCert_".$this->ApiId.".crt","r");
        $pub_key=fread ($fp,8192);
        fclose($fp);
        self::$KeyPublica=openssl_get_publickey($pub_key);
        if(!self::$KeyPublica){
            throw new CriptoPay_Exception("El certificado cliente es inválido");
        }
        
        $fp=fopen ($this->ApiCertificados."CriptoPay_ApiKey_".$this->ApiId.".key","r");
        $priv_key=fread ($fp,8192);
        fclose($fp);
        self::$KeyPrivada = openssl_get_privatekey($priv_key,$this->ApiPassword);
        if(!self::$KeyPrivada){
            throw new CriptoPay_Exception("El certificado privado o la clave es inválido");
        }
    }
    
    public function __destruct() {
        openssl_free_key(self::$KeyPublica);
    }
    
    protected function Encriptar(){
        if(is_null(self::$KeyPublica)){
            $this->CargarKeys();
        }
        $claro = json_encode($this->Parametros);
        openssl_public_encrypt($claro,$finaltext,self::$KeyPublica);
        if (!empty($finaltext)) {
            return base64_encode($finaltext);
        }else{
            throw new CriptoPay_Exception("No se pueden Encriptar los datos");
            return false;
        }
    }

    protected function Desencriptar($Dcifrados){
        if(is_null(self::$KeyPrivada)){
            $this->CargarKeys();
        }
        
        $Crypted=openssl_private_decrypt(base64_decode($Dcifrados),$Dclaro,self::$KeyPrivada);
        if (!$Crypted) {
            echo "CLIENTE_DESENCRIPTAR";
            var_dump($Dcifrados);
            throw new CriptoPay_Exception("No se pueden Desencriptar los datos");
            return false;
        }else{
            $Dclaro = ($this->isJson($Dclaro))?json_decode($Dclaro):$Dclaro;
            return $Dclaro;
        }
    }
    public function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }    
    
    
}

class CriptoPay_Exception extends Exception{
    
}
