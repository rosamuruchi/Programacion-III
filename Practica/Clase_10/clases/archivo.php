<?php
    class Archivo{
        // Guarda un objeto en el archivo
        public static function guardarUno($ruta, $objeto){
            $archivo = fopen($ruta, 'a+');
            fwrite($archivo, $objeto->toJSON() . PHP_EOL);
            fclose($archivo);
        }
        
        // Guarda la lista de objetos en el archivo
        public static function guardarTodos($ruta, $lista){
            $archivo = fopen($ruta, 'w');
            foreach($lista as $item){
                fwrite($archivo, $item->toJSON() . PHP_EOL);
            }
            fclose($archivo);
        }
        // Retorna en el de objetos stdClass
        public static function leerArchivo($ruta){
            $lista = array();
            $archivo = fopen($ruta, 'r');
            do{
                $item = trim(fgets($archivo));
                if ($item != ''){
                    $objeto = json_decode($item);
                    array_push($lista, $objeto);
                }
            }while(!feof($archivo));
            fclose($archivo); 
            return $lista;   
        }
    }
?>