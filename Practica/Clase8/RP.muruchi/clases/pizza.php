<?php

require_once "./clases/archivo.php";

class Pizza
{
    public $id;
    public $precio;
    public $tipo;
    public $cantidad;
    public $sabor;
    public $fotoUno;
    public $fotoDos;

    public function __construct($precio, $tipo, $cantidad, $sabor, $fotos)
    {
        $this->id = Pizza::CrearIdAutoincremental();
        $this->precio = $precio;
        $this->tipo = $tipo;
        $this->cantidad = $cantidad;
        $this->sabor = $sabor;
        $this->fotoUno = Archivo::GuardarArchivoTemporal($fotos["fotoUno"], "./images/pizzas/foto1", $tipo, $sabor);
        $this->fotoDos = Archivo::GuardarArchivoTemporal($fotos["fotoDos"], "./images/pizzas/foto2", $tipo, $sabor);
    }
    public static function CrearIdAutoincremental()
    {
        $listaPizzas = Pizza::TraerPizzas();
        if($listaPizzas != null)
        {
            $id = count($listaPizzas) + 1;
        }
        else
        {
            $id = 1;
        }
        return $id;
    }
    public static function GuardarPizza($pizza)
    {
        $ruta = "./Pizza.txt";
        $pizzaGuardada = false;       
        Archivo::GuardarUno($ruta, $pizza);
        $guardo = true;               
        return $guardo;
    }

    public static function TraerPizzas()
    {
        $ruta = "./Pizza.txt";
        
        $listaPizzas = Archivo::LeerArchivo($ruta);
        
        return $listaPizzas;
    }
    public static function ValidarTipo($tipo)
    {
        $validado = false;
        if(strcasecmp($tipo, "molde") == 0 || strcasecmp($tipo, "piedra") == 0)
        {
            $validado = true;
        }
        return $validado;
    }
    public static function ValidarSabor($sabor)
    {
        $validar = false;
        if(strcasecmp($sabor, "muzza") == 0 || strcasecmp($sabor, "jamon") == 0 || strcasecmp($sabor, "especial") == 0)
        {
            $validar = true;
        }
        return $validar;
    }
    public static function ValidarCombinacion($tipo, $sabor)
    {
        $pizzaRepetida = false;
        $listaPizzas = Pizza::TraerPizzas();
        
        foreach($listaPizzas as $auxPizza)
        {
            //tipo-sabor única:
            if(strcasecmp($auxPizza->tipo, $tipo) == 0 && strcasecmp($auxPizza->sabor, $sabor) == 0)
            {
                $pizzaRepetida = true;
                break;
            }
        }
        return $pizzaRepetida;
    }

    public static function ModificarPizza($elementoModificado, $ruta)
    {
        //$ruta = "./Pizza.txt";
        $listaPizzas = Pizza::TraerPizzas();
        for($i= 0 ; $i < count($listaPizzas); $i++)
        {
            $pizzaAux = $listaPizzas[$i];
            if($pizzaAux->id == $elementoModificado->id)
            {
                $listaPizzas[$i] = $elementoModificado;
                Archivo::GuardarTodos($ruta, $listaPizzas);
                break;
            }
        }
    }

    public static function ModificarPizzaConFoto($elementoModificado)
    {
        $ruta = "./Pizza.txt";
        $listaPizzas = Pizza::TraerPizzas();
        for($i= 0 ; $i < count($listaPizzas); $i++)
        {
            $pizzaAux = $listaPizzas[$i];
            if($pizzaAux->id == $elementoModificado->id)
            {
                Archivo::HacerBackup($ruta, $pizzaAux);
                $listaPizzas[$i] = $elementoModificado;
                Archivo::GuardarTodos($ruta, $listaPizzas);
                break;
            }
        }
    }
    
    
}
?>