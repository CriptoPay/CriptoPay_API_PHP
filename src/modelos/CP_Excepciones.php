<?php

class CP_Excepciones extends Exception{
    
    public function __toString() {
        return $this->getMessage();
    }
    
}