<?php
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;
    require 'vendor/autoload.php';
    require_once "./clases/pizzeriaApi.php";
    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;
    $app = new \Slim\App(["settings" => $config]);
    $app->group('', function()
    {
        
        $this->post('/pizzas', \PizzeriaApi::class . ':cargarPizza');
        $this->get('/pizzas', \PizzeriaApi::class . ':consultarPizza');
        $this->post('/ventas', \PizzeriaApi::class . ':ventasPizza');
        $this->post('/pizza', \PizzeriaApi::class . ':modificarPizza');
        $this->get('/ventas', \PizzeriaAPI::class . ':consultarVenta');
    });
    $app->run();
?>