<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 *
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 */

/**
 * Description of CriptoPay
 *
 * @author criptopay
 * @version v0.1
 */


class CriptoPay {
    public static $version = "v0.1";
    public static $path;
    
    protected $usuario;
    protected $apiKey;
    protected $apiUsuario;
    
    private $ch;
    

    public function __construct($apiUsuario,$apiKey){
        self::$path = dirname(__FILE__);
        
        if(!isset($apiUsuario) && !isset($apiKey)){
            throw new CP_Excepciones("El usuario y la api Key no han sido definidos");
        }else{
            $this->apiKey = $apiKey;
            $this->apiUsuario = $apiUsuario;
        }
        
        if(file_exists(__DIR__."/configuracion.php")){
            require_once __DIR__.'/configuracion.php';
        }else{
            throw new CP_Excepciones("Falta el fichero de configuraci�n");
        }
        global $CriptoPay_Configuracion, $CriptoPay_Divisas;
        $this->configuracion = $CriptoPay_Configuracion;
        
        $this->InicializaCurl();
    }
    
    protected function InicializaCurl(){
         $this->ch = curl_init();
         curl_setopt($this->ch,CURLOPT_URL,$this->configuracion['url']); 
         curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);
    }
    
    protected function Enviar($funcion,$datosJSON=null){
        $url = $this->configuracion['url'].'/'.self::$version.'/'.$funcion;
        $firma = hash_hmac('sha512', $url, $this->apiKey);
        curl_setopt($this->ch,CURLOPT_URL,$url); 
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array('apiUsuario:'.$this->apiUsuario,'firma:'.$firma));
        // if(isset($datosJSON)){ 
             curl_setopt($this->ch,CURLOPT_POST,true);
             curl_setopt($this->ch,CURLOPT_POSTFIELDS,$datosJSON); 
        // }
        return curl_exec($this->ch);
    }
    
    public function API($funcion,$datos=array()){
        if(is_array($datos) && count($datos)>=1){
            $data = json_encode($datos);
        }elseif(is_string($datos)){
            $data = json_encode(array("datos"=>$datos));
        }else{
            $data = null;
        }
        $respuesta = $this->Enviar($funcion,$data);

        $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        if($http_code != 200){
            die("ERROR AL CONECTAR A CRIPTOPAY ".$http_code);
        }
        return $respuesta;
    }
    
    public function __destruct() {
        curl_close($this->ch);
    }
    
}
