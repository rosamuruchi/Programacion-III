<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Models\ORM\usuario;
use App\Models\ORM\usuarioControler;
use App\Models\ORM\materia;
use App\Models\ORM\materiaControler;
use App\Models\ORM\Middleware;

include_once __DIR__ . '/../../src/app/modelORM/usuario.php';
include_once __DIR__ . '/../../src/app/modelORM/usuarioControler.php';
include_once __DIR__ . '/../../src/app/modelORM/materia.php';
include_once __DIR__ . '/../../src/app/modelORM/materiaControler.php';
include_once __DIR__ . '/../../src/app/modelORM/profesor_materia.php';
include_once __DIR__ . '/../../src/app/middlewares/middlewaresRoutes.php';

return function (App $app) {

    $container = $app->getContainer();

     $app->group('/usuariosORM', function () {   
         
        $this->post('/usuario', usuarioControler::class . ':CargarUno');

        $this->post('/login', usuarioControler::class . ':loginUsuario');

        $this->post('/materia', materiaControler::class . ':CargarUno')->add(Middleware::class . ':validarUsuarioAdmin');

        //Si se lo pasa por url {}, no usar params
        $this->post('/usuario/{legajo}', usuarioControler::class . ':ModificarUno')->add(Middleware::class . ':validarRuta');

        //Si se lo paso por params
        $this->post('/inscripcion/', usuarioControler::class . ':InscribirAlumno')->add(Middleware::class . ':validarRuta');
        
        $this->get('/materias', usuarioControler::class . ':TraerTodos')->add(Middleware::class . ':validarRuta');

        $this->get('/materias/', materiaControler::class . ':TraerUno')->add(Middleware::class . ':validarRuta');
       
    });

};