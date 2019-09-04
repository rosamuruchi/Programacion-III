<?php
require_once 'persona.php';

 class alumno extends persona
{
    public $legajo;
    public $cuatrimestre;


 public function __construct($nombre = "" , $dni= 0, $legajo=0 , $cuatrimestre="")
{
    parent:: __construct($nombre, $dni);

    $this->legajo=$legajo;
    $this->cuatrimestre=$cuatrimestre;   

}

public function Saludar()
{
    return parent::Saludar()."y mi legajo: ".$this->legajo."</br";
}

}
?>