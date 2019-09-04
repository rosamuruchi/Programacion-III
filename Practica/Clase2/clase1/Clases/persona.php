<?php
//include './funciones.php';


class Persona
{
    public $nombre;
    public $dni;


public function __construct($nombre, $dni)
{
    $this->nombre=$nombre;
    $this->dni=$dni;
}

public function saludar()
{
    echo "Hola", $this.saludar;
}
}
?>