<?php
require_once "funciones.php";


$opcion = $_POST["opcion"];

switch($opcion)
{
    case "guardar":
    if(isset($_POST["nombre"],$_POST["apellido"], $_POST["legajo"]))
    {
        $objeto = array("nombre" => $_POST["nombre"],"apellido" => $_POST["apellido"] ,"legajo" => $_POST["legajo"]);
        Guardar("objetos.json", $objeto);
    }
    break;
    case "":
    break;
}
//$datos = Leer("objetos.json");
//Mostrar($datos);

?>