<?php

//var_dump($_POST);
//var_dump($_FILES); //array de arrays

$archivoTmp= $_FILES["imagen"]["tmp_name"];


//echo "$archivoTmp";

$extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);

echo $extension;

$destino = "C:\\xampp\\htdocs\\Clase4\\foto".".".$extension;

move_uploaded_file($archivoTmp, $destino);

//$rta= move_uploaded_file($archivoTmp, "./foto.jpg");

?>