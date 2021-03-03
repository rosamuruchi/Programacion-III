<?php
namespace App\Models\ORM;

use Slim\App;
use App\Models\ORM\user;
use App\Models\ORM\log;
use App\Models\ORM\historial_ingreso;
use App\Models\ORM\egreso_usuario;
use App\Models\ORM\ingreso_usuario;
use App\Models\ORM\profesor_materia;
use App\Models\IApiControler;

include_once __DIR__ . '/user.php';
include_once __DIR__ . '/historial_ingreso.php';
include_once __DIR__ . '/egreso_usuario.php';
include_once __DIR__ . '/ingreso_usuario.php';
include_once __DIR__ . '/log.php';
include_once __DIR__ . '../../modelAPI/IApiControler.php';


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\AutentificadorJWT;
use \Exception;


class userControler implements IApiControler 
{

  private static $claveSecreta = 'claveSecreta1';
 
  public function TraerTodos($request, $response, $args) {
        
    $todosLosUsuarios = usuario::all();
    
    $newResponse = $response->withJson($todosLosUsuarios, 200);  

    return $newResponse;
  }

  public function TraerUno($request, $response, $args) {
      //complete el codigo
    $newResponse = $response->withJson("sin completar", 200);  
    return $newResponse;
  }
    
  public function CargarUno($request, $response, $args) {
        
    $datos = $request->getParsedBody();
    $archivos = $request->getUploadedFiles();
    
    //Agrego a la base de datos:
    $usuario = new user;
    
    if(isset($datos["email"], $datos["clave"], $datos["legajo"]))
    {
      if($datos["legajo"] > 0 && $datos["legajo"] <= 1000)
      {
        $usuario->email = $datos["email"];
        $usuario->clave = crypt($datos["clave"], self::$claveSecreta);
        $usuario->legajo = $datos["legajo"];

        $usuario->imagen_uno = userControler::GuardarArchivoTemporal($archivos['fotoUno'], __DIR__ . "../../../../images/users",
            $usuario->legajo."_"."fotoUno");    

          $usuario->imagen_dos = userControler::GuardarArchivoTemporal($archivos['fotoDos'], __DIR__ . "../../../../images/users",
          $usuario->legajo."_"."fotoDos"); 
    

          $log = new log();
          $log->ip = "1.0.0.1";
          $log->ruta = "/users";
          $log->metodo = "POST";
          $log->usuario = $usuario->legajo;
          $log->save();

        $usuario->save();

        $newResponse = $response->withJson("Usuario registrado", 200);  
    
      }
      else
      {

        $log = new log();
        $log->ip = "1.0.0.1";
        $log->ruta = "/users";
        $log->metodo = "POST";
        $log->save();

        $newResponse = $response->withJson("Legajo invalido", 200);  
      }
    }
    else
    {
      $log = new log();
      $log->ip = "1.0.0.1";
      $log->ruta = "/users";
      $log->metodo = "POST";
      $log->save();

      $newResponse = $response->withJson("Faltan datos", 200);  
    }
    return $newResponse;
  }

  public function BorrarUno($request, $response, $args) {
    //complete el codigo
    $newResponse = $response->withJson("sin completar", 200);  
    return $newResponse;
  }
      
  public function ModificarUno($request, $response, $args) {
    
   
    


    return 	$newResponse;
  }

  public function loginUsuario($request, $response, $args)
  {
    $datos = $request->getParsedBody();

    if(isset($datos['legajo'], $datos['clave']))
    {
      $legajo = $datos['legajo'];
      $clave = $datos['clave'];
      $email = $datos['email'];
      
      $usuario = user::where('legajo', $legajo)->first();

      if($usuario != null)
      {
        if(hash_equals($usuario->clave, crypt($clave, self::$claveSecreta)))
        {
          if(strcasecmp($email, $usuario->email) == 0)
          {
            $datosToken = array(
              'legajo' => $usuario->legajo,
              'email' => $usuario->email,
              'foto_uno' => $usuario->foto_uno,
              'foto_dos' => $usuario->foto_dos
            );
  
            $token = AutentificadorJWT::CrearToken($datosToken);
            
            $log = new log();
            $log->ip = "1.0.0.1";
            $log->ruta = "/login";
            $log->metodo = "POST";
            $log->usuario = $usuario->legajo;
            $log->save();
  
            $newResponse = $response->withJson($token, 200);  
          }
          else
          {

            $log = new log();
            $log->ip = "1.0.0.1";
            $log->ruta = "/login";
            $log->metodo = "POST";
            
            $log->save();

            $newResponse = $response->withJson("Email incorrecto", 200);  
          }
        }
        else
        {
          $log = new log();
          $log->ip = "1.0.0.1";
          $log->ruta = "/login";
          $log->metodo = "POST";
          
          $log->save();

          $newResponse = $response->withJson("Clave incorrecta", 200);  
        }
      
      }
      else
      {
        
        $log = new log();
        $log->ip = "1.0.0.1";
        $log->ruta = "/login";
        $log->metodo = "POST";
        
        $log->save();

        $newResponse = $response->withJson("No se encontro el legajo: $legajo", 200);
      }
    }
    else
    {
      $newResponse = $response->withJson("Faltan datos", 200);
    }
    
    return $newResponse;


  }

  public function IngresoUsuario($request, $response, $args)
  {
    $token = $request->getHeader('token');
    $token = $token[0];
    $datosToken = AutentificadorJWT::ObtenerData($token);
    
    $usuario = user::where('legajo', $datosToken->legajo)->first();

    $ingresoUsuario = ingreso_usuario::where('id_usuario', $usuario->legajo)->first();

    if($ingresoUsuario == null)
    {
      $ingresoUsuario = new ingreso_usuario();
      $ingresoUsuario->id_usuario = $usuario->legajo;
      $ingresoUsuario->save();

      $historialIngreso = new historial_ingreso();
      $historialIngreso->id_usuario = $usuario->legajo;
      $historialIngreso->save();
  
      $newResponse = $response->withJson("Ingreso", 200); 
    }
    else
    {
      $newResponse = $response->withJson("Ya esta ingresado", 200);
    }

    $log = new log();
    $log->ip = "1.0.0.1";
    $log->ruta = "/ingreso";
    $log->metodo = "PUT";
    $log->usuario = $usuario->legajo;
    $log->save();

    return $newResponse;

  }


  public function EgresoUsuario($request, $response, $args)
  {
    
    $token = $request->getHeader('token');
    $token = $token[0];
    $datosToken = AutentificadorJWT::ObtenerData($token);
    //$habiaIngresado = true;

    $usuario = user::where('legajo', $datosToken->legajo)->first();

    $ingresoUsuario = ingreso_usuario::where('id_usuario', $usuario->legajo)->first();

    if($ingresoUsuario != null)
    {
      $egresoUsuario = new egreso_usuario();
      $egresoUsuario->id_usuario = $usuario->legajo;
      $egresoUsuario->save();

      $ingresoUsuario = ingreso_usuario::where('id_usuario', $usuario->legajo);
      $ingresoUsuario->delete();
  
      $newResponse = $response->withJson("El usuario ha Egresado con exito", 200); 
    }
    else
    {
      $newResponse = $response->withJson("No esta ingresado", 200);
    }
    
    $log = new log();
    $log->ip = "1.0.0.1";
    $log->ruta = "/egreso";
    $log->metodo = "PUT";
    $log->usuario = $usuario->legajo;
    $log->save();

    return $newResponse;

  }

  public function MostrarIngreso($request, $response, $args)
  {
    $token = $request->getHeader('token');
    $token = $token[0];
    $datosToken = AutentificadorJWT::ObtenerData($token);

    $usuario = user::where('legajo', $datosToken->legajo)->first();

    if($usuario->legajo <= 100) //es Adm
    {

      $datos = ingreso_usuario::orderby('created_at', 'desc')->get();

      $newResponse = $response->withJson($datos, 200);
    }
    else
    {
      $datos2 = historial_ingreso::where('id_usuario', $usuario->legajo)->select('historial_ingresos.id_usuario','historial_ingresos.hora')->get();

      $newResponse = $response->withJson($datos2, 200);

    }

    $log = new log();
    $log->ip = "1.0.0.1";
    $log->ruta = "/ingreso";
    $log->metodo = "GET";
    $log->usuario = $usuario->legajo;
    $log->save();

    return $newResponse;
  }

  public function MostrarUltimoIngreso($request, $response, $args)
  {
    $token = $request->getHeader('token');
    $token = $token[0];
    $datosToken = AutentificadorJWT::ObtenerData($token);

    $usuario = user::where('legajo', $datosToken->legajo)->first();

    if($usuario->legajo <= 100)
    {
      
    }
    else
    {
      $newResponse = $response->withJson("Solo Admin puede Ingresar", 200); 
    }
    return $newResponse;
  }


  public static function GuardarArchivoTemporal($archivo, $destino, $nombre)
    {
      $origen = $archivo->getClientFileName();
        
      $fecha = new \DateTime();
      $fecha = $fecha->setTimezone(new \DateTimeZone('America/Argentina/Buenos_Aires'));
      $fecha = $fecha->format("d-m-Y-His");
      $extension = pathinfo($archivo->getClientFileName(), PATHINFO_EXTENSION);
      $destino = "$destino$nombre-$fecha.$extension";
      $archivo->moveTo($destino);
      return $destino;
    }

    public static function HacerBackup($ruta, $elementoAModificar)
        {
      
            $fecha = new \DateTime();//timestamp para no repetir nombre
            $fecha = $fecha->setTimezone(new \DateTimeZone('America/Argentina/Buenos_Aires'));
            $fecha = $fecha->format("d-m-Y-His");
            
            $extension = pathinfo($elementoAModificar->foto, PATHINFO_EXTENSION);
            //Donde guardo el backup:
            $nombreBackup =  __DIR__ . "../../../../img/backup/backup$elementoAModificar->legajo-$fecha.$extension";
            //Muevo el backup de la foto:
            rename($elementoAModificar->foto, $nombreBackup);
        }
  




  
}