<?php

namespace App\Models\ORM;

use App\Models\ORM\usuario;//ruta completa de la clase
use App\Models\AutentificadorJWT;

include_once __DIR__ . '../../modelAPI/AutentificadorJWT.php';


class Middleware
{
    public static function validarUsuarioAdmin($request, $response, $next)
    {

        $token = $request->getHeader('token');

        $datos = AutentificadorJWT::ObtenerData($token[0]);


        $legajo = $datos->legajo;
        
        $usuario = usuario::where('legajo', $legajo)->first();

        if($usuario != null)
        {
            //Compruebo la clave:
            if(strcasecmp($usuario->tipo, 'admin') == 0)
            {
               

                try
                {
                  AutentificadorJWT::VerificarToken($token[0]);
                  $esValido = true;
                }
                catch(Exception $e)
                {
                  $newResponse = $response->withJson($e->getMessage(), 200);
                }

                if($esValido)
                {
                    $newResponse = $next($request, $response); 
                }

                
            }
            else
            {
                $newResponse = $response->withJson("No es de tipo valido", 200);
            }
        }
        else
        {
            $newResponse = $response->withJson("No existe el usuario", 200);
        }
       
        return $newResponse;
    }


    public static function validarRuta($request, $response, $next)
    {

        $token = $request->getHeader('token');
        $esValido = false;  

        try
        {
            AutentificadorJWT::VerificarToken($token[0]);
            $esValido = true;
        }
        catch(Exception $e)
        {
            $newResponse = $response->withJson($e->getMessage(), 200);
        }

        if($esValido)
        {
            $newResponse = $next($request, $response); 
        }
       
        return $newResponse;
    }
}
    
?>