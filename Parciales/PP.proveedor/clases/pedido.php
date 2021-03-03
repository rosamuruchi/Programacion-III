<?php

require_once "./clases/proveedor.php";

class Pedido
{
    public $producto;
    public $cantidad;
    public $idProveedor;

    public function __construct($producto,$cantidad,$idProveedor)
    {
        $this->idProveedor=$idProveedor;
        $this->producto = $producto;
        $this->cantidad = $cantidad;
    }
    public static function LeerArchivoPedidos($dirFile)
    {
        if(file_exists($dirFile))
        {
            $resource = fopen($dirFile,"r");
            $vectorArchivo = array();
            do
            {
                array_push($vectorArchivo,fgets($resource));

            }while(!(feof($resource)));
            return $vectorArchivo;
        }
    }


    public static function ConstruirPedidos($dirFile)
    {
        $lineas = Pedido::LeerArchivoPedidos($dirFile);
        $Pedidos = array();
        if($lineas !=NULL)
        {
            foreach($lineas as $linea)
            {
                if(!empty($linea))
                {
                    $datos = explode(",",$linea);
                    $Pedido = new Pedido($datos[2],(int)$datos[0],$datos[1]);
                    array_push($Pedidos,$Pedido);
                }
            }
            return $Pedidos;
        }
    }
    public function cargarArchivoPedido($filePedido)
    {
            if(file_exists($filePedido))
            {
                $resource = fopen($filePedido,"a");
                if(file_get_contents($filePedido) != "")
                {
                    fwrite($resource, "\r\n"."$this->cantidad".","."$this->idProveedor".","."$this->producto");
                }
                else
                {
                    fwrite($resource, "$this->cantidad".","."$this->idProveedor".","."$this->producto");
                }
            }
            else
            {
                $resource = fopen($filePedido,"w");
                fwrite($resource, "$this->cantidad".","."$this->idProveedor".","."$this->producto");
            }
            fclose($resource);
    }
    public function cargarPedido($filePedido,$fileProveedor,$response)
    {
        if((Proveedor::ValidarId($_POST["idProveedor"],$fileProveedor)))
        {
            $Pedido = new Pedido($_POST["cantidad"],$_POST["idProveedor"],$_POST["producto"]);
            $Pedido->cargarArchivoPedido($filePedido);
            $newResponse = $response->withJson('El Pedido se cargo exitosamente', 200);
        }
        else
        {
            $newResponse = $response->withJson("El Id del Proveedor No se encuentra.", 200);
        }
        return $newResponse;
    }

    public static function mostrarPedidos($filePedido,$fileProveedor,$response)
    {
        $pedidos = Pedido::ConstruirPedidos($filePedido);
        $proveedores = Proveedor::ConstruirProveedores($fileProveedor);
        $flag=false;
        $arrayPedidos=array();

        if($pedidos!= null && $proveedores!=null)
        {
            foreach($pedidos as $pedido)
            {
                //echo "hola";
                foreach($proveedores as $proveedor)
                {
                    
                    if(($pedido->idProveedor) != ($proveedor->id) )
                    {
                        $suma = array(
                            "producto"=> $pedido->producto,
                            "cantidad"=> $pedido->cantidad,
                            "idProveedor"=> $pedido->idProveedor,
                            "nombreProveedor"=> $proveedor->nombre);
                            array_push($arrayPedidos,$suma);
                            $flag=true;
                            
                    }
                }
            }
            $newResponse = $response->withJson($arrayPedidos, 200);
        }
        
        
        if ($flag==true)
        {
            $newResponse = $response->withJson($arrayPedidos, 200);
        }
        else
        {
            $newResponse = $response->withJson("No hay Pedido con el id Proveedor");
        }
        
 
        return $newResponse;
    }


}

?>