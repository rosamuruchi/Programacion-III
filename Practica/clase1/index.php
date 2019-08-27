<?php
include 'funciones.php';
include 'alumno.php';
include 'Clases/persona.php';

function saludar($nombre)
{
    echo "Hola $nombre";
}
saludar ('pepe');

$persona =new persona('pepe',21);
$persona->saludar();

$alumno =new alumno ('juan',30);
$alumno->saludar();

?>