<?php
require_once "./clases/archivo.php";
    class Log{
        public $ruta;
        public $metodo;
        public $hora;
        public $ip;

        public function __construct($ruta, $metodo, $hora,$ip){
            $this->ruta = $ruta;
            $this->metodo = $metodo;
            $this->hora = $hora;
            $this->ip= $ip;
        }
        public function toJSON(){
            return json_encode($this);
        }
        public static function alta($objeto){
            Archivo::guardarUno('./info.log', $objeto);
        }
    }
?>