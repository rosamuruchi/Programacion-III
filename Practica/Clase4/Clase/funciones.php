<?php

class Archivo{

    public static function Leer ($ruta)
    {
        $archivo= fopen( "archivo.txt","r");
        $lista=array();
        
        while(!feof ($archivo))
        {
            $objeto= json_decode(fgets($archivo));

            if($objeto !=null)
            {
                array_push($lista,$archivo);
            }  
        }
        fclose($archivo);
        return $lista;
    }
    public static function Guardar($ruta, $datos)
    {
        $archivo = fopen($ruta, "a");
    
            fwrite($archivo, json_encode($datos).PHP_EOL);
    
            fclose($archivo);
    }

    public static function Mostrar($datos)
    {
        $datos= Leer("objetos.json");

        foreach($datos[$i] as $clave=> $valor)
        {
            echo "clave". ":" . "valor"."<br>";
        }
    }

    public static function GuardarArchivoTmp($archivo, $destino)
    {
        $archivoTmp= $archivo["tmp_name"];
        $fecha = new DateTime();

        $extension = pathinfo($_FILES["archivo"]["name"], PATHINFO_EXTENSION);

        echo $extension;

        $destino = $destino . "\\archivo" . $fecha->getTimeStamp() . "." . $extension;

        move_uploaded_file($archivo, $destino);
    }
}

?>