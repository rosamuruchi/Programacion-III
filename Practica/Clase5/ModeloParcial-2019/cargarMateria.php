<?php
include_once "./clases/materia.php";
$materia = new Materia($_POST["nombre"],(int)$_POST["codigoDeMateria"],(int)$_POST["cupoDeAlumnos"],(int)$_POST["aula"]);

if((Materia::ValidarCodigoMateria($_POST["codigoDeMateria"],"./materias.txt"))!=-1)
{
    $materia->cargarMateria("./materias.txt");
    echo "<br>La materia se cargo exitosamente";
}
else
{
    echo "<br>La Materia ya se encuentra en la base de datos";
}

?>