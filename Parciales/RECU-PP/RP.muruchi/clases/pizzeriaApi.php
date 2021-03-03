<?php
require_once "./clases/archivo.php";
require_once "./clases/log.php";
require_once "./clases/pizza.php";
require_once "./clases/venta.php";


class PizzeriaApi
{
    public static function cargarPizza($request, $response)
    {
        $ruta = "./Pizza.txt";
        $args = $request->getParsedBody();
        $fotos = $request->getUploadedFiles();  
        $pizzaRepetida = false;
        $tipoSaborValido = false;
        //validaciones
        if(Pizza::ValidarTipo($args["tipo"]) == true
            && Pizza::ValidarSabor($args["sabor"]) == true)
        {
            $tipoSaborValido = true;
            $pizzaRepetida = Pizza::ValidarCombinacion($args["tipo"], $args["sabor"]);
        }
        if($tipoSaborValido == true && $pizzaRepetida == false)
        {
            $pizza = new Pizza($args["precio"], $args["tipo"], $args["cantidad"], $args["sabor"], $fotos);
            Pizza::GuardarPizza($pizza);
            
            $listaPizzas = Pizza::TraerPizzas();
            $newResponse = $response->withJson($listaPizzas, 200);   
            
        }
        else if($pizzaRepetida == true)
        {
            $newResponse = $response->withJson("Pizza repetida", 404);
        }
        else if($tipoSaborValido == false)
        {
            $newResponse = $response->withJson("Tipo o sabor inválido", 404);
        }
        PizzeriaApi::HacerLog("POST", $request);         
        return $newResponse;
    }

    public static function consultarPizza($request, $response, $args)
    {
        $tipo = $request->getParam("tipo");
        $sabor = $request->getParam("sabor");
        $muestroPizza = false;
        $existeDato = false;
        $arrayPizzas = array();
        $listaPizzas = Pizza::TraerPizzas();
      
        foreach($listaPizzas as $auxPizza)
        {
            if(strcasecmp($auxPizza->tipo, $tipo) == 0 && strcasecmp($auxPizza->sabor, $sabor) == 0)       
            {               
                $existeDato = true;
                array_push($arrayPizzas, $auxPizza);
            }
        }
        if($existeDato == false)
        {
            $arrayPizzas = "No existe $tipo-$sabor de la Pizza.";
        }
        $newResponse = $response->withJson($arrayPizzas, 200);
        //Log
        PizzeriaApi::HacerLog("GET", $request);
        return $newResponse;
    }

    public static function ventasPizza($request, $response)
    {
        $ruta = "./Pizza.txt";
        $args = $request->getParsedBody();
        
        $existePizza = false;
        $cantidadSuficiente = false;
        $pizzaVenta;
        $venta;
        $mensaje = "Error";
        $listaPizzas = Pizza::TraerPizzas();
        
        for($i=0; $i < sizeof($listaPizzas); $i++)
        {
            $auxPizza = $listaPizzas[$i];
            if(strcasecmp($auxPizza->tipo, $args["tipo"]) == 0 && strcasecmp($auxPizza->sabor, $args["sabor"]) == 0)
            {
                $existePizza = true;
                if($auxPizza->cantidad >= $args["cantidad"])
                {
                    $cantidadSuficiente = true;
                    $venta = new Venta($auxPizza->precio,$auxPizza->tipo, $args["cantidad"],$auxPizza->sabor, $args["emailUsuario"]);
                    Venta::GuardarVenta($venta);
                    //Descuento la cantidad:
                    $auxPizza->cantidad -= $args["cantidad"];
                    Pizza::ModificarPizza($auxPizza, $ruta);
                }
                break;
            }
        }  
        if($existePizza == false)
        {
            $mensaje = "No existe la pizza";
        }
        else if($cantidadSuficiente == false)
        {
            $mensaje = "No hay stock";
        }
        else
        {
            $mensaje = "se cargo la venta";//Venta::TraerVentas();
        }
        PizzeriaApi::HacerLog("POST", $request);
        return $response->withJson($mensaje, 200);
    }

    public static function ModificarPizza($request, $response, $args)
    {    
        $args = $request->getParsedBody();          
        $fotos = $request->getUploadedFiles();
        $listaPizzas;
        $pizzaRepetida = false;
        $tipoSaborValido = false;
        $listaPizzas = Pizza::TraerPizzas();
        if(Pizza::ValidarTipo($args["tipo"]) == true && Pizza::ValidarSabor($args["sabor"]) == true)
        {
            $tipoSaborValido = true;     
            foreach($listaPizzas as $auxPizza)
            {
                //Combinación tipo-sabor única + id:
                if((strcasecmp($auxPizza->tipo, $args["tipo"]) == 0 && strcasecmp($auxPizza->sabor, $args["sabor"]) == 0) && ($args["id"] != $auxPizza->id))
                {
                    $pizzaRepetida = true;
                    break;
                }
            } 
        }
        if($tipoSaborValido == true && $pizzaRepetida == false)
        {      
            foreach($listaPizzas as $auxPizza)
            {
                if(strcasecmp($auxPizza->id, $args["id"]) == 0)
                {
                    $pizza = new Pizza($args["precio"], $args["tipo"], $args["cantidad"], $args["sabor"], $fotos);
                    $pizza->id = $auxPizza->id;
            
                    Pizza::ModificarPizzaConFoto($pizza);
                }
            }
        }
        $listaPizzas = Pizza::TraerPizzas();
        $newResponse = $response->withJson($listaPizzas, 200);
        PizzeriaAPI::HacerLog("POST", $request);
        return $newResponse;  
    }

    public static function consultarVenta($request, $response, $args)
    {
        $flag = false;
        $datos = array();
        $tipo = $request->getParam("tipo");
        $sabor = $request->getParam("sabor");
        $listaVentas = Venta::TraerVentas();
      
        foreach($listaVentas as $auxVenta)
        {
            if(strcasecmp($auxVenta->tipo, $tipo) == 0 || strcasecmp($auxVenta->sabor, $sabor) == 0)       
            {               
                $flag = true;
                array_push($datos, $auxVenta);
            }
        }
        if($flag == false)
        {
            $datos = "No existe $tipo-$sabor";
        }
        $newResponse = $response->withJson($datos, 200);
        PizzeriaAPI::HacerLog("GET", $request);
        return $newResponse;
    }




    public static function HacerLog($caso, $request)
    {
        $uri = $request->getUri();
        $log = new Log($caso, (string)$uri);
        Log::GuardarLog($log);
    }

}


?>