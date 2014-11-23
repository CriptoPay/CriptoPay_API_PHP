<?php

    require_once('../src/autoload.php');
    
    $pago = array(
        "total"=>20,
        "divisa"=>"EUR",
        "elementos"=>array(
            array("ID_DEL_ELEMENTO","NOMBRE_DESCRIPCION","CANTIDAD","PRECIO_UNITARIO","DIVISA_DEL_PRECIO")
        )
    );
    
    $CriptoPay = new CriptoPay($USUARIO, $APIKEY);
    
    $CriptoPay->API('nuevo_pago',array());
    
    /**
     * return de la función
     * array(
     *  "ID_DEL_PAGO",
     *  "URL_DEL_PAGO"
     * )
     */
    
