<?php
namespace App\Models\ORM;

use Slim\App;
use App\Models\ORM\usuario;
use App\Models\ORM\materia;
use App\Models\ORM\alumno_materia;
use App\Models\IApiControler;

include_once __DIR__ . '/materia.php';
include_once __DIR__ . '/usuario.php';
include_once __DIR__ . '/alumno_materia.php';
include_once __DIR__ . '../../modelAPI/IApiControler.php';


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\AutentificadorJWT;
use \Exception;




class materiaControler implements IApiControler 
{

 
 
  public function TraerTodos($request, $response, $args) {
        

    return $newResponse;
  }

  public function TraerUno($request, $response, $args) {

    $token = $request->getHeader('token');
    $token = $token[0];
    $datosToken = AutentificadorJWT::ObtenerData($token);

    $usuario = usuario::where('legajo', $datosToken->legajo)->first();

    $idMateria = $request->getAttribute('idMateria');

    switch($usuario->tipo)
    {
        case 'profesor':
            $profesorMateria = profesor_materia::where([
                ['id_profesor', $usuario->legajo],
                ['id_materia', $idMateria]])->first();

            if($profesorMateria != null)
            {
                $datos = alumno_materia::where('id_materia', $idMateria)
                ->join('usuarios', 'alumno_materias.id_alumno', '=', 'usuarios.legajo')
                ->select('usuarios.legajo', 'usuarios.email', 'usuarios.foto')
                ->get();
                
                
                $datos->toArray();

                $tabla = materiaControler::CrearTablaAlumnos($datos); 

                $newResponse = $response->write($tabla, 200); 
            }
            else
            {
                $newResponse = $response->withJSon('No está a cargo', 200); 
            }


            
        break;
        case 'admin':
            $datos = alumno_materia::where('id_materia', $idMateria)
            ->join('usuarios', 'alumno_materias.id_alumno', '=', 'usuarios.legajo')
            ->select('usuarios.legajo','usuarios.email', 'usuarios.foto')
            ->get();

            $datos->toArray();

            $tabla = materiaControler::CrearTablaAlumnos($datos); 

            $newResponse = $response->write($tabla, 200);
             
            

        break;
    }

    

    

    return $newResponse;
  }
    
  public function CargarUno($request, $response, $args) {
        
    $datos = $request->getParsedBody();
    
    
        if(isset($datos["nombre"], $datos["cuatrimestre"], $datos["cupos"] ))
        {

            //Agrego a la base de datos:
            $materia = new materia;
        
        
            $materia->nombre = $datos["nombre"];
            $materia->cuatrimestre =$datos["cuatrimestre"];
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
    //complete el codigo
    $newResponse = $response->withJson("sin completar", 200);  
    return $newResponse;
  }
      
  public function ModificarUno($request, $response, $args) {
    //complete el codigo
    $newResponse = $response->withJson("sin completar", 200);  
    return 	$newResponse;
  }
  public function InscribirAlumno($request, $response, $args) {
      
    $token = $request->getHeader('token');
    $token = $token[0];
    $datosToken = AutentificadorJWT::ObtenerData($token);

    $usuario = usuario::where('legajo', $datosToken->legajo)->first();
    
    if(strcasecmp($usuario->tipo, 'alumno') == 0)
    {
        $idMateria = $request->getAttribute('idMateria');
        $materia = materia::where('id', $idMateria)->first();

        if($materia != null)
        {
            $alumnoMateria = alumno_materia::where([
                ['id_alumno', $usuario->legajo],
                ['id_materia', $materia->id]])
                ->first();

               

            if($alumnoMateria == null)
            {
                if($materia->cupos > 0)
                {
                    $alumnoMateria = new alumno_materia();
                    $alumnoMateria->id_alumno = $usuario->legajo;
                    $alumnoMateria->id_materia = $materia->id;
                    $alumnoMateria->save();

                    $materia->cupos--;
                    $materia->save();

                    $newResponse = $response->withJson("Alumno inscripto", 200);
                }
                else
                {
                    $newResponse = $response->withJson("No hay cupos", 200);        
                }
            }
            else
            {
                $newResponse = $response->withJson("Ya está inscripto", 200);        
            }
                

        }
        else
        {
            $newResponse = $response->withJson("No existe la materia", 200);    
        }
    }
    else
    {
        $newResponse = $response->withJson("No es alumno", 200);
    }

    return $newResponse;
  }

  public function MostrarMaterias($request, $response, $args) {

    $token = $request->getHeader('token');
    $token = $token[0];
    $datosToken = AutentificadorJWT::ObtenerData($token);

    $usuario = usuario::where('legajo', $datosToken->legajo)->first();

    switch($usuario->tipo)
    {
        case 'alumno':
            $datos = alumno_materia::where('id_alumno', $usuario->legajo)
            ->join('materias', 'alumno_materias.id_materia', '=', 'materias.id')
            ->select('materias.nombre', 'materias.cuatrimestre')
            ->get();

            $datos->toArray();

            $tabla = materiaControler::CrearTablaMaterias($datos);   
        break;
        case 'profesor':
            $datos = profesor_materia::where('id_profesor', $usuario->legajo)
            ->join('materias', 'profesor_materias.id_materia', '=', 'materias.id')
            ->select('materias.nombre', 'materias.cuatrimestre')
            ->get();

            $datos->toArray();

            $tabla = materiaControler::CrearTablaMaterias($datos);  
        break;
        case 'admin':
            $datos = materia::all('materias.nombre', 'materias.cuatrimestre');
            $datos->toArray();

            $tabla = materiaControler::CrearTablaMaterias($datos);  
            

        break;
    }

    $newResponse = $response->write($tabla, 200);

    return $newResponse;
 
}

public static function CrearTablaMaterias($listaMaterias)
        {
            $tablaMaterias = "<table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cuatrimestre</th>
                                 
                                </tr>     
                            </thead>
                            <tbody>";
            foreach($listaMaterias as $materia)
            {
                $tablaMaterias .= "<tr>
                                    <td>" . $materia->nombre . "</td>
                                    <td> " . "&nbsp" . $materia->cuatrimestre . "</td>
                                      
                                </tr>";
            }
                                    
            $tablaMaterias .=  "</tbody></table>";
            
            return $tablaMaterias;
        }



        public static function CrearTablaAlumnos($listaAlumnos)
        {
            $tablaAlumnos = "<table>
                            <thead>
                                <tr>
                                    <th>Legajo</th>
                                    <th>Email</th>
                                    <th>Foto</th>
                                 
                                </tr>     
                            </thead>
                            <tbody>";
            foreach($listaAlumnos as $alumno)
            {
                $tablaAlumnos .= "<tr>
                                    <td>" . $alumno->legajo . "</td>
                                    <td> " . $alumno->email . "</td>
                                    <td> " . $alumno->foto . "</td>
                                      
                                </tr>";
            }
                                    
            $tablaAlumnos .=  "</tbody></table>";
            
            return $tablaAlumnos;
        }


}





?>
