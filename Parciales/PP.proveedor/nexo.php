<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once './vendor/autoload.php';
require_once './clases/proveedorApi.php';

$config['displayErrorDetails']=true;
$config['addContentLengthHeader']=false;

$app = new \Slim\App(["settings" => $config]);

$app->group('', function ()
{
    $this->post('/cargarProveedor', \ProveedorApi::class . ':cargarProveedor' );

    $this->get('/consultarProveedor', \ProveedorApi::class . ':consultarProveedor');

    $this->get('/proveedores', \ProveedorApi::class . ':proveedores');

    $this->post('/hacerPedido', \ProveedorApi::class . ':hacerPedido');

    $this->get('/listarPedidos', \ProveedorApi::class . ':listarPedidos');

    /*$this->get('/verUsuario', \ProveedorApi::class . ':verUsuario');
    
    $this->get('/logs', \ProveedorApi::class . ':consultarLogs');*/
});

$app->run();

?>