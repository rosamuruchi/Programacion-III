<?php
include_once "./clases/alumno.php";
$alumno = new Alumno($_POST["nombre"],$_POST["apellido"],$_POST["email"]);
if((Alumno::ValidarEmail($_POST["email"],"./alumnos.txt"))!=-1)
{
    $foto = $alumno->GuardarFoto("./Fotos");
    $alumno->foto = $foto;
    $alumno->cargarAlumno("./alumnos.txt");
    echo "<br>El Alumno se cargo exitosamente";
}
else
{
    echo "<br>El Alumno ya se encuentra en la base de datos";
}
?>