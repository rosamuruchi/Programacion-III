<?php
require_once "./Clase/funciones.php";

    
$archivo = $_FILES["archivo"];
$destino = "C:\\xampp\\htdocs\\Clase4";
Archivo::GuardarArchivoTmp($archivo, $destino);

?>