<?php
//include 'funciones.php';
require_once  'Clases/alumno.php';
require_once 'Clases/persona.php';
require_once 'Clases/alumnoDAO.php';

/*function saludar($nombre)
{
    echo "Hola $nombre";
}
saludar ('pepe');

$persona =new persona('pepe',21);
$persona->saludar();

$alumno =new alumno ('juan',30);
$alumno->saludar();*/

$alumno =array();

if($_SERVER['REQUEST_METHOD']== "GET") //listar
{
    if(isset($_GET['nombre'], $_GET['dni'],$_GET['legajo']))
    {
        $alumno=$_GET;
        var_dump($alumno);
    }
}

if($_SERVER['REQUEST_METHOD']== "POST") //guardar
{
    if(isset($_POST['nombre'], $_POST['dni'],$_POST['legajo']))
    {
        $alumno=$_POST;
        var_dump($alumno);
        alumnoDAO:: Guardar($alumno);
    }
}

if($_SERVER['REQUEST_METHOD']== "PUT") //modificar
{
    if(isset($_PUT['nombre'], $_PUT['dni'],$_PUT['legajo']))
    {
        $alumno=$_PUT;
        var_dump($alumno);
        alumnoDAO:: Modificar($alumno);
    }
}




?>