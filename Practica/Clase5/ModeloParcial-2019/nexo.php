<?php
$metodo = $_GET["caso"];
switch($metodo)
{
    case 'cargarAlumno':
    include_once "cargarAlumno.php";//andaa
    break;
    case 'consultarAlumno':
    include_once "consultarAlumno.php";//anda
    break;
    case 'cargarMateria':
    include_once "cargarMateria.php";//anda
    break;
    case 'inscribirAlumno'://falta
    include_once "inscribirAlumno.php";
    break;
    case 'inscripciones':
    include_once "inscripciones.php";
    break;
    case 'modificarAlumno':
    include_once "modificarAlumno.php";
    break;
    case 'alumnos':
    include_once "alumnos.php";
    break;
    case '':
    include_once "";
    break;
}
?>