<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once './vendor/autoload.php';
require_once './clases/usuarioApi.php';

$config['displayErrorDetails']=true;
$config['addContentLengthHeader']=false;

$app = new \Slim\App(["settings" => $config]);

$app->group('', function ()
{
    $this->post('/cargarUsuario', \UsuarioApi::class . ':cargarUsuario' );

    $this->get('/login', \UsuarioApi::class . ':loginUsuario');

    $this->post('/modificarUsuario', \UsuarioApi::class . ':modificarUsuario');

    $this->get('/verUsuarios', \UsuarioApi::class . ':verUsuarios');

    $this->get('/verUsuario', \UsuarioApi::class . ':verUsuario');
    
    $this->get('/logs', \UsuarioApi::class . ':consultarLogs');
}

);

$app->run();

/*if(isset($_REQUEST["caso"])){
    Log::cargarLog($_REQUEST["caso"]);
}

if(isset($_POST["caso"]))
{
    $caso = $_POST["caso"];
    switch($caso)
    {
        case 'cargarUsuario':
            if(isset($_POST["legajo"],$_POST["nombre"],$_POST["clave"],$_POST["email"],$_FILES["fotoUno"],$_FILES["fotoDos"]))
            {
                $usuario = new Usuario($_POST["legajo"],$_POST["nombre"],$_POST["clave"],$_POST["email"],$_FILES["fotoUno"],$_FILES["fotoDos"]);
                $usuario->cargarUsuario("./usuarios.txt");
            }
            break;
    
        case 'modificarUsuario':
            if(isset($_POST["legajo"],$_POST["nombre"],$_POST["clave"],$_POST["email"],$_FILES["fotoUno"],$_FILES["fotoDos"]))
            {
                $usuario = new Usuario($_POST["legajo"],$_POST["nombre"],$_POST["clave"],$_POST["email"],$_FILES["fotoUno"],$_FILES["fotoDos"]);
                $usuario->modificarUsuario("./usuarios.txt"); 
            }
         break; 
         default:
            $respuesta = [ 'respuesta' => 'Error! Ingrese caso correcto'];
            echo json_encode($respuesta);
    }
}
elseif (isset($_GET["caso"])) 
{
    $caso = $_GET["caso"];

    switch($caso)
    {
        case 'login';
        if(isset($_GET["legajo"],$_GET["clave"]))
        {
            Usuario::validarUsuarios($_GET["legajo"],$_GET["clave"],"./usuarios.txt");
        }
        break;
    
        case 'verUsuarios':
        Usuario:: mostrarUsuariosSinClave("./usuarios.txt");
        break;
    
        case 'verUsuario':
        if(isset($_GET["legajo"]))
        {
             Usuario:: mostrarUsuarios("./usuarios.txt",$_GET["legajo"]);
        }
        break;
    
        case 'logs':
        if(isset($_GET["hora"])){
            $logs = Log::consultarLogs("info.log");
            Log::mostrarLogs($logs, $_GET["hora"]);
        }
        else{
            $respuesta = [ 'respuesta' => 'No existe hora'];
            echo json_encode($respuesta);
        }
        break;
        default:
            $respuesta = [ 'respuesta' => 'Error! Ingrese caso correcto'];
            echo json_encode($respuesta);
    }

}*/


?>