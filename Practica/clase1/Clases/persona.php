<?php
include 'funciones.php';


class Persona
{
    public $nombre;
    public $dni;
}

function __construct($nombre, $dni)
{
    $this->nombre=$nombre;
    $this->dni=$dni;
}

function saludar()
{
    echo "Hola", $this.saludar;
}

?>