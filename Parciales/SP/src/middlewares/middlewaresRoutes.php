<?php

use App\Models\ORM\user;//ruta completa del namespace de la clase
use App\Models\AutentificadorJWT;

include_once __DIR__ . '/../../src/app/modelAPI/AutentificadorJWT.php';

class Middleware
{
    public function validarRuta($request, $response, $next)
    {
        $esValido = false;

        $token = $request->getHeader('token');

        $token = $token[0];

        try
        {
            AutentificadorJWT::VerificarToken($token);

            $esValido = true;
        }
        catch(\Exception $e)
        {
            $newResponse = $response->withJson("Token invalido. Error: " . $e->getMessage(), 200);
        }

        if($esValido)
        {
            $newResponse = $next($request, $response);
        }
      
        

        return $newResponse;
    }


   
}
    
?>