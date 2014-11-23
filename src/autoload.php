<?php

$ficheros = array(
    "modelos/CP_Excepciones.php",
    "configuracion.php",
    "CriptoPay.php"
);

foreach ($ficheros as $fichero){
   if(file_exists(__DIR__.'/'.$fichero)){
       include_once __DIR__.'/'.$fichero;
   } else {
       throw new CP_Excepciones("CriptoPay PHP API: Falta el fichero ".$fichero);
   } 
}