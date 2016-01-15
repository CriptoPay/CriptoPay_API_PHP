<?php
/**
 * Copyright (c) 2014-2016 CriptoPay
 */


namespace CriptoPayApiRest\src\Comun;

/**
 * 
 * LOG para el seguimiento de los diferentes eventos.
 * 
 * @package CriptoPayApiRest
 * @version 2.2
 */
class Log {
    const tabla = "log";
        
    private static $instancia = NULL;
    
    private $inicio;
    
    protected $datoslog,$contadores,$FICHERO;
        
    protected $NIVELREGISTRO = LOG_INFO;
    protected $NIVELMOSTRAR = LOG_INFO;
        
    const SEPARADOR = ",";
    
    protected function __construct($nivelmostrar,$nivelregistro,$fichero) {
        $this->inicio = microtime(true);
        $this->NIVELMOSTRAR = (is_null($nivelmostrar))? $this->NIVELMOSTRAR:$nivelmostrar;
        $this->NIVELREGISTRO = (is_null($nivelregistro))? $this->NIVELREGISTRO:$nivelregistro;
        $this->FICHERO = $fichero;
    }
    
    protected static function Instancia($nivelmostrar=null,$nivelregistro=null,$fichero="logCriptoPayApiRest.csv"){
            if(is_null(self::$instancia)){
                    self::$instancia = new Log($nivelmostrar,$nivelregistro,$fichero);
            }
            return self::$instancia;
    }
    
    public function Iniciar($nivelmostrar,$nivelregistro,$fichero){
        $LOG = self::Instancia($nivelmostrar,$nivelregistro,$fichero);
        $LOG->Add(LOG_INFO,"Inicio Script");
    }

    public static function C($dato,$n=1){
        $LOG = self::Instancia();
        $LOG->Contador($dato,$n);
    }
    
    
    public static function Info($mensaje,$tag=''){
        $LOG = self::Instancia();
        $LOG->Add(LOG_INFO,$mensaje,$tag);
    }
    
    public static function Debug($mensaje,$tag=''){
        $LOG = self::Instancia();
        $LOG->Add(LOG_DEBUG,$mensaje,$tag);
    }
    
    public static function Warning($mensaje,$tag=''){
        $LOG = self::Instancia();
        $LOG->Add(LOG_WARNING,$mensaje,$tag);
    }
    
    public static function Alert($mensaje,$tag=''){
        $LOG = self::Instancia();
        $LOG->Add(LOG_ALERT,$mensaje,$tag);
    }
    
    public static function Critical($mensaje,$tag=''){
        $LOG = self::Instancia();
        $LOG->Add(LOG_CRIT,$mensaje,$tag);
    }
    
    public static function Kernel($mensaje,$tag=''){
        $LOG = self::Instancia();
        $LOG->Add(LOG_KERN,$mensaje,$tag);
    }
    
    
    protected function Contador($dato,$n){
        if(isset($this->contadores[$dato])){
            $this->contadores[$dato]= $this->contadores[$dato]+$n;
        }else{
            $this->contadores[$dato]= $n;
        }
    }

    protected function Add($nivel,$mensaje,$tag=""){
        $debugBacktrace = debug_backtrace();
        $mensaje = preg_replace('/\s+/', ' ', trim($mensaje));
        $log=array(
            "time"=>time(),
            "date"=>date("d-m-Y H:i:s"),
            "nivel"=>$nivel,
            "tag"=>$tag,
            "mensaje"=>$mensaje,
            "fichero"=>@$debugBacktrace[1]['file'],
            "linea"=>@$debugBacktrace[1]['line']
        );
        if($nivel<=$this->NIVELMOSTRAR){
           echo '---LOG---'.$mensaje.PHP_EOL;
        }
         
        $this->datoslog[] = $log;
        
        if($nivel<=$this->NIVELREGISTRO){
            $this->AddFichero($nivel,$mensaje,$tag);
        }
    }
    
    protected function AddFichero($nivel,$mensaje,$tag='') {
        if (!file_exists($this->FICHERO)) {
                $headers = 'TIME' . self::SEPARADOR . 
			'DATE' . self::SEPARADOR .
			'NIVEL' . self::SEPARADOR .
			'TAG' . self::SEPARADOR .
			'MENSAJE' . self::SEPARADOR .
                        'FICHERO' . self::SEPARADOR .
			'LINEA'. "\n";
        }
        $fd = fopen($this->FICHERO, "a");
        if (@$headers) {
                fwrite($fd, $headers);
        }
        fputcsv($fd, $this->datoslog[count($this->datoslog)-1], self::SEPARADOR);
        fclose($fd);
    }
    
    public function __destruct() {
        $time = microtime(true)-$this->inicio;
        $this->Add(LOG_INFO, "Tiempo total: ".$time);
        if(LOG_DEBUG<=$this->NIVELMOSTRAR){
            echo '-------------RESUMEN---------------------'.PHP_EOL;
            echo 'Nº Eventos: '.count($this->datoslog).PHP_EOL;
            echo 'Tiempo total '.$time.PHP_EOL;
            if(count($this->contadores)>0){            
                echo '------------CONTADORES--------------------'.PHP_EOL;
                foreach($this->contadores as $contador=>$valor){
                    echo 'Recuento '.$contador.":".$valor.PHP_EOL;
                }
            }
            echo '-----------FIN RESUMEN-------------------'.PHP_EOL;
        }
    }
}

