<?php
require_once "./clases/proveedor.php";
require_once "./clases/pedido.php";

class ProveedorApi
{
    public static function cargarProveedor($request, $response)
    {
        $datos = $request->getParsedBody();
        $fotos = $request->getUploadedFiles();

        $proveedor = new Proveedor($datos["id"], $datos["email"], $datos["nombre"],$fotos["foto"]);
        $rta=$proveedor->cargarProveedor("./proveedores.txt",$response);
        return $rta;
    }
    public static function consultarProveedor($request, $response, $args)
    {
        $nombre = $request->getParam("nombre");

        $rta= Proveedor::validarProveedor($nombre,"./proveedores.txt",$response);
        return $rta;
    }
    public static function proveedores($request, $response, $args)
    {
        return Proveedor:: mostrarProveedores("./proveedores.txt",$response);
    }
    public static function hacerPedido($request, $response)
    {
        $datos = $request->getParsedBody();

        $pedidos = new Pedido($datos["cantidad"], $datos["idProveedor"], $datos["producto"]);
        $rta=$pedidos->cargarPedido("./pedidos.txt","./proveedores.txt",$response);
        return $rta;
    }
    public static function listarPedidos($request, $response, $args)
    {
        $rta= Pedido:: mostrarPedidos("./pedidos.txt","./proveedores.txt",$response);
        return $rta;
    }

}

?>