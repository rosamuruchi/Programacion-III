<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once './vendor/autoload.php';

$config['displayErrorDetails']=true;
$config['addContentLengthHeader']=false;

$app = new \Slim\App(["settings" => $config]);

$app->group('/archivo', function(){

    $this->get('/', function ($request, $response, $args) {
        $response->getbody()->write("Apirest Get");
    });

    $this->post('/', function ($request, $response, $args) {
        $response->getbody()->write("Apirest Post");
    });

    $this->put('/', function ($request, $response, $args) {
        $response->getbody()->write("Apirest PUT");
    });

    $this->delete('/', function ($request, $response, $args) {
        $response->getbody()->write("Apirest DELETE");
    });

});

$app->run();
?>