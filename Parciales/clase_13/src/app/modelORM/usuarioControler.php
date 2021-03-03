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


class usuarioControler implements IApiControler 
{

    private static $claveSecreta = 'calveSecreta';
 	  
     public function TraerTodos($request, $response, $args) {
       	
      
    }

    public function TraerUno($request, $response, $args) {
        
    
    }
   
    public function CargarUno($request, $response, $args) {
         
        $datos = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();

        if(isset($datos['email'], $datos['clave'], $datos['tipo']))
        {
            $usuario = new usuario();
            $usuario->email = $datos['email'];
            $usuario->clave = crypt($datos['clave'], self::$claveSecreta); //llamo a la variable estática
                
            if(strcasecmp($datos['tipo'],'alumno') == 0 ||
                strcasecmp($datos['tipo'],'admin') == 0 ||
                strcasecmp($datos['tipo'],'profesor') == 0)
            {   
                $usuario->tipo = $datos['tipo'];
            }

            if($archivos != null)
            {
                $usuario->foto = usuarioControler::GuardarArchivoTemporal($archivos['foto'], __DIR__ . "../../../../img/",
                    $usuario->legajo."_".$usuario->tipo);    
            }
            

            $usuario->save();
            
            $newResponse = $response->withJson("Usuario cargado", 200);  
        }
        else
        {
            $newResponse = $response->withJson("Falta dato", 200);  
        }
             
        return $newResponse;
    }

    public function BorrarUno($request, $response, $args) {
        
        
    }
     
    public function ModificarUno($request, $response, $args) {

        $usuarioAModificar = null;
        $noEsAdmin = false;

        $datosModificados = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();

         $legajo = $request->getParam('legajo'); //legajo que le paso por param
        //$legajo = $request->getAttribute('legajo');//si se lo paso por url

        $token = $request->getHeader('token');
        $datosToken = AutentificadorJWT::ObtenerData($token[0]);
        $legajoRequest = $datosToken->legajo;//legajo del token
        
        //Compruebo el tipo de usuario
        $usuario = usuario::where('legajo', $datosToken->legajo)->first();

        //Para que un profesor o alumno no pueda modificar a otro
        if((strcasecmp($usuario->tipo,'profesor') == 0 || strcasecmp($usuario->tipo,'alumno') == 0) && $usuario->legajo == $legajo)
        {
            $usuarioAModificar =  usuario::where('legajo', $legajo)->first();
        }
        else if($usuario->tipo == 'admin')//si es admin puede modificar
        {
            $usuarioAModificar =  usuario::where('legajo', $legajo)->first();
        }
        else
        {
            $noEsAdmin = true;
        }

        if($usuarioAModificar != null)
        {

            
            switch($usuarioAModificar->tipo)
            {
                case 'alumno':
                $usuarioAModificar->email = $datosModificados['email'];
                usuarioControler::HacerBackup(__DIR__ . "../../../../img/", $usuarioAModificar);
                $usuarioAModificar->foto = usuarioControler::GuardarArchivoTemporal($archivos['foto'], __DIR__ . "../../../../img/",
                    $usuarioAModificar->legajo.$usuarioAModificar->tipo);
                    $usuarioAModificar->save();
                break;

                case 'profesor':
                    $usuarioAModificar->email = $datosModificados['email'];
                    profesor_materia::where('id_profesor', $usuarioAModificar->legajo)->delete();//borro todas las materias del profesor
                    if(is_array($datosModificados['materiasDictadas']))
                    {
                        $length = count($datosModificados['materiasDictadas']);
                    }
                    else
                    {
                        $length = 1;
                    }

                    for($i=0; $i < $length; $i++)
                    {
                        $idMateria = $datosModificados['materiasDictadas'][$i];
                        $profesorMateria = new profesor_materia();
                        $profesorMateria->id_profesor = $usuarioAModificar->legajo;
                        $profesorMateria->id_materia = $idMateria;
                        $profesorMateria->save();
                    }      
                    $usuarioAModificar->save();
                    break;

            }

            $newResponse = $response->withJson("Usuario modificado", 200);
      
        }
        else
        {
            $newResponse = $response->withJson("No existe el usuario", 200);
        }

        if($noEsAdmin == true)
        {
            $newResponse = $response->withJson("No es admin", 200);
        }
        

		return 	$newResponse;
    }

    public function loginUsuario($request, $response, $args)
    {
        $datos = $request->getParsedBody();

        if(isset($datos["legajo"], $datos["clave"]))
        {
            $clave = $datos["clave"];
            $legajo = $datos["legajo"];

            $usuario = usuario::where('legajo', $legajo)->first();

            if($usuario != null)
            {
                if(hash_equals($usuario->clave, crypt($clave, self::$claveSecreta)))
                {
                    $datosUsuario = array(
                        'email' => $usuario->email,
                        'legajo' => $usuario->legajo,
                        'tipo' => $usuario->tipo
                    );

                    $token = AutentificadorJWT::CrearToken($datosUsuario);

                    $newResponse = $response->withJson($token, 200);
                }
                else
                {
                    $newResponse = $response->withJson('Clave incorrecta', 200);
                }
            }
            else
            {
                $newResponse = $response->withJson('No se encontró al usuario', 200);
            }
        }
        else
        {
            $newResponse = $response->withJson('Faltan datos', 200);
        }

        return $newResponse;

    }

    public function InscribirAlumno($request, $response, $args)
    {
        $token = $request->getHeader('token');
        $datosToken = AutentificadorJWT::ObtenerData($token[0]);
        $alumno = usuario::where('legajo', $datosToken->legajo)->first();

        if(strcasecmp($alumno->tipo, 'alumno') == 0)
        {
            $idMateria = $request->getParam('idMateria');

            $materia = materia::where('id', $idMateria)->first();

            //Me fijo si no está repetido:
            $alumnoRepetido = alumno_materia::where([
                ['id_alumno', $alumno->legajo],
                 ['id_materia', $materia->id]])->exists();

            if($alumnoRepetido == false)
            {
                if($materia->cupos > 0)
                {
                    //Anoto al alumno
                    $alumnoMateria = new alumno_materia();
                    $alumnoMateria->id_alumno = $alumno->legajo;
                    $alumnoMateria->id_materia = $materia->id;
                    $alumnoMateria->save();

                    //Descuento los cupos
                    $materia->cupos--;
                    $materia->save();

                    $newResponse = $response->withJson("Inscripción exitosa", 200);
                }
                else
                {
                    $newResponse = $response->withJson("No hay cupos", 200);
                }    

            }
            else
            {
                $newResponse = $response->withJson("Ya está anotado", 200);
            }
            

            
        }
        else
        {
            $newResponse = $response->withJson("No es alumno", 200);
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

?>