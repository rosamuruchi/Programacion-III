<?php

class Log
{
    public $caso;
    public $hora;
    public $ip;

    public function __construct($caso, $hora, $ip)
    {
        $this->caso = $caso;
        $this->hora = $hora;
        $this->ip = $ip;
    }

    public static function cargarLog($caso){
        $log = new Log($caso, date("h:i:sa"), $_SERVER['REMOTE_ADDR']);

        $archivo = fopen("info.log", "a");
        fwrite($archivo, json_encode($log).PHP_EOL);
        fclose($archivo);
    }

    public static function consultarLogs($ruta){
        $archivo = fopen($ruta, "r");
        $lista = array();

        while(!feof($archivo)){
            $objeto = json_decode(fgets($archivo));
            if($objeto != null)
            {
                array_push($lista, $objeto);
            }
        }
        fclose($archivo);
        return $lista;
    }

    public static function mostrarLogs($array, $dato){
        $logs = array();
        foreach($array as $valor){
            if($valor->hora < $dato)
            {
                $log = [ 'caso' => $valor->caso, 'hora' => $valor->hora, 'ip' => $valor->ip];
                array_push($logs, $log);
            }
            
        }
        echo json_encode($logs);
    }
}

?>