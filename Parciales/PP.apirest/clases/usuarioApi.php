<?php
require_once "./clases/usuario.php";

class UsuarioApi
{
    public static function cargarUsuario($request, $response)
    {
        $datos = $request->getParsedBody();
        $fotos = $request->getUploadedFiles();

        $usuario = new Usuario($datos["legajo"], $datos["email"], $datos["nombre"], $datos["clave"],$fotos["fotoUno"], $fotos["fotoDos"]);
        $rta=$usuario->cargarUsuario("./usuarios.txt",$response);
        return $rta;
    }

    public static function loginUsuario($request, $response, $args)
    {
        $legajo = $request->getParam("legajo");
        $clave = $request->getParam("clave");

        $rta= Usuario::validarUsuarios($legajo,$clave,"./usuarios.txt",$response);
        return $rta;
    }
    public static function modificarUsuario($request, $response)
    {
        $datos = $request->getParsedBody();
        $fotos = $request->getUploadedFiles();
        $usuario = new Usuario($datos["legajo"], $datos["email"], $datos["nombre"], $datos["clave"],$fotos["fotoUno"], $fotos["fotoDos"]);
        $rta=$usuario->modificarUsuario("./usuarios.txt",$response);
        return $rta;
    }

    public static function verUsuarios($request, $response, $args)
    {
        $legajo = $request->getParam("legajo");
        return Usuario:: mostrarUsuariosSinClave("./usuarios.txt",$response);
    }
    public static function verUsuario($request, $response, $args)
    {
        $legajo = $request->getParam("legajo");
        return Usuario:: mostrarUsuarios("./usuarios.txt",$legajo,$response);

    }

    public static function consultarLogs($request, $response, $args)
    {
        
    }

}
?>