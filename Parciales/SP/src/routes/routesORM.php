<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\ORM\user;
use App\Models\ORM\userControler;



include_once __DIR__ . '/../../src/app/modelORM/user.php';
include_once __DIR__ . '/../../src/app/modelORM/userControler.php';
include_once __DIR__ . '/../../src/middlewares/middlewaresRoutes.php';


return function (App $app) {
    $container = $app->getContainer();

     $app->group('/appORM', function () {   

        $this->post('/users', userControler::class . ':CargarUno');

        $this->post('/login', userControler::class . ':loginUsuario');

        $this->put('/ingreso', userControler::class . ':IngresoUsuario')->add(Middleware::class . ':validarRuta');

        $this->put('/egreso', userControler::class . ':EgresoUsuario')->add(Middleware::class . ':validarRuta');

        $this->get('/ingreso', userControler::class . ':MostrarIngreso')->add(Middleware::class . ':validarRuta');

        $this->get('/ultimoingreso', userControler::class . ':MostrarUltimoIngreso')->add(Middleware::class . ':validarRuta');
     
    });

};