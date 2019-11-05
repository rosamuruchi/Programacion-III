<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT as JWT;

require_once './vendor/autoload.php';
require_once './clases/log.php';

$config['displayErrorDetails']=true;
$config['addContentLengthHeader']=false;

$app = new \Slim\App(["settings" => $config]);

$mwLog = function ($request, $response, $next){

    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $hora = date("h:i:s");
    $metodo=$request->getMethod();
    $ruta= $request->getUri();
    $ip= $_SERVER['SERVER_ADDR'];
    
    $log = new Log((string)$ruta, $metodo, $hora, $ip);
    Log::Alta($log);
    $response->getBody()->write("Se ejecuto Log ");
    return $response;
};

$app->add($mwLog);






$app->run();
?>
