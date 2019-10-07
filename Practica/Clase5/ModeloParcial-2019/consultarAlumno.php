<?php
include_once "./clases/alumno.php";
Alumno::ConsultarAlumno("./alumnos.txt",$_GET["apellido"]);
?>