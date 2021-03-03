<?php
namespace App\Models\ORM;
use App\Models\ORM\usuario;
use App\Models\ORM\profesor_materia;
use App\Models\ORM\alumno_materia;
use App\Models\IApiControler;
use App\Models\AutentificadorJWT;

include_once __DIR__ . '/usuario.php';
include_once __DIR__ . '/alumno_materia.php';
include_once __DIR__ . '../../modelAPI/IApiControler.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class materiaControler implements IApiControler 
{

    
  public function TraerTodos($request, $response, $args) {
     
    $token = $request->getHeader('token');
    $datosToken = AutentificadorJWT::ObtenerData($token[0]);
    $usuario = usuario::where('legajo', $datosToken->legajo)->first();

    switch($usuario->tipo)
    {
        case 'alumno':
            $datos = alumno_materia::where('id_alumno', $usuario->legajo)
                ->join('materias', 'alumno_materias.id_materia', '=', 'materias.id')
                ->select('materias.nombre', 'materias.cuatrimestre')
                ->get();
            $newResponse = $response->withJson($datos, 200);
            break;
        case 'profesor':
            $datos = profesor_materia::where('id_profesor', $usuario->legajo)
                ->join('materias', 'profesor_materias.id_materia', '=', 'materias.id')
                ->select('materias.nombre', 'materias.cuatrimestre')
                ->get();
            $newResponse = $response->withJson($datos, 200);
            break;
        case 'admin':
            $datos = materia::all();
            $newResponse = $response->withJson($datos, 200);
        break;
    }

    return $newResponse;
  }

    public function TraerUno($request, $response, $args) {
    
      $token = $request->getHeader('token');
      $datosToken = AutentificadorJWT::ObtenerData($token[0]);
      $usuario = usuario::where('legajo', $datosToken->legajo)->first();
      $idMateria = $request->getParam('id');

      switch($usuario->tipo)
      {
        case 'admin':
          $datos = alumno_materia::where('id_materia', $idMateria)
                ->join('usuarios', 'alumno_materias.id_alumno', '=', 'usuarios.legajo')
                ->select('usuarios.legajo', 'usuarios.email', 'usuarios.foto')
                ->get();
          $newResponse = $response->withJson($datos, 200);
          break;

        case 'profesor':
          $dictaLaMateria = false;
          $materiasDictadas = profesor_materia::where('id_profesor', $usuario->legajo)->get();
 
          foreach($materiasDictadas as $auxMateria)
          {
            if($auxMateria->id_materia == $idMateria)
            {
              $dictaLaMateria = true;
            }
          }

          if($dictaLaMateria == true)
          {
            $datos = alumno_materia::where('id_materia', $idMateria)
                ->join('usuarios', 'alumno_materias.id_alumno', '=', 'usuarios.legajo')
                ->select('usuarios.legajo', 'usuarios.email', 'usuarios.foto')
                ->get();
            $newResponse = $response->withJson($datos, 200);
          }
          else
          {
            $newResponse = $response->withJson('No dicta la materia', 200);
          }
          break;

          default:
            $newResponse = $response->withJson('No tienen permisos suficientes', 200);
            break;
      }
      
      return $newResponse;
    
    }
   
      public function CargarUno($request, $response, $args) {
        
        $datos = $request->getParsedBody();


       if(isset($datos["nombre"], $datos['cuatrimestre'], $datos["cupos"]))
       {
            $materia = new materia();

            $materia->nombre = $datos["nombre"];
            $materia->cuatrimestre = $datos['cuatrimestre'];
            $materia->cupos = $datos["cupos"];

            $materia->save();
            
            $newResponse = $response->withJson("Materia cargada", 200);  
       }
       else
       {
            $newResponse = $response->withJson("Faltan datos", 200);  
       }
                  
        return $newResponse;
        
    }
      public function BorrarUno($request, $response, $args) {
   
    }
     
     public function ModificarUno($request, $response, $args) {
     	 
     }


  
}