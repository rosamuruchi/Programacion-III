<?php

class Usuario
{
    public $legajo;
    public $email;
    public $nombre;
    public $clave;
    public $fotoUno;
    public $fotoDos;

    public function __construct($legajo,$nombre, $clave, $email,$fotoUno,$fotoDos)
    {
        $this->legajo=$legajo;
        $this->nombre = $nombre;
        $this->clave= $clave;
        $this->email = $email;
        $this->fotoUno=$fotoUno;
        $this->fotoDos=$fotoDos;
    }

    public function cargarArchivo($dirFile)
    {
        if(file_exists($dirFile))
        {
            $resource = fopen($dirFile,"a");
            if(file_get_contents($dirFile) != "")
            {
                fwrite($resource, "\r\n"."$this->legajo".","."$this->nombre".","."$this->clave".","."$this->email".","."$this->fotoUno".",".trim($this->fotoDos,"\r\n"));
            }
            else
            {
                fwrite($resource, "$this->legajo".","."$this->nombre".","."$this->clave".","."$this->email".","."$this->fotoUno".",".trim($this->fotoDos,"\r\n"));
            }
        }
        else
        {
            $resource = fopen($dirFile,"w");
            fwrite($resource, "$this->legajo".","."$this->nombre".","."$this->clave".","."$this->email".","."$this->fotoUno".",".trim($this->fotoDos,"\r\n"));
        }
        fclose($resource);
    }

    public static function LeerArchivo($dirFile)
    {
        if(file_exists($dirFile))
        {
            $resource = fopen($dirFile,"r");
            $vectorArchivo = array();
            do
            {
                array_push($vectorArchivo,fgets($resource));
            }while(!(feof($resource)));
            return $vectorArchivo;
        }
    }

    private static function VaciarArchivo($dirFile)
    {
        if(file_exists($dirFile))
        {
            $resource = fopen($dirFile,"w");
            fclose($resource);
        }
    }     

    public static function GuardarFoto($origen,$destino,$legajo)
    {

            $extension=pathinfo($destino, PATHINFO_EXTENSION);
            $nombreArchivo = "./img/fotos/".$legajo . "." . $extension;
            if(file_exists($nombreArchivo))
            {
                $nombreArchivo =  "./img/fotos/".$legajo . "(2)." . $extension;
                move_uploaded_file($origen, $nombreArchivo);
            }
            else
            {
                move_uploaded_file($origen, $nombreArchivo);
            }
            
            return $nombreArchivo;
        
    }

    public static function modificarArchivo($origen, $destino, $legajo){
        $extension = pathinfo($destino, PATHINFO_EXTENSION);
        $nombreFinal = "./img/fotos/".$legajo . "." . $extension;
        $nombreFinal2= "./img/fotos/".$legajo . "(2)." . $extension;

        if(file_exists($nombreFinal) && file_exists($nombreFinal2))
        {
            copy($nombreFinal, "./img/backup/".$legajo."_".date("Ymd"). ".".$extension);
            copy($nombreFinal2, "./img/backup/".$legajo."_(2)".date("Ymd"). ".".$extension);
            unlink($nombreFinal);
            unlink($nombreFinal2);
        }
        
        $archivoModificado=Usuario::GuardarFoto($origen,$destino,$legajo);
        /*move_uploaded_file($origen, $nombreFinal);
        move_uploaded_file($origen, $nombreFinal2);*/
        return $archivoModificado;
    }

    public function cargarUsuario($file)
    {
        if((Usuario::ValidarLegajo($_POST["legajo"],$file))!=-1)
        {
            $origenUno=$_FILES["fotoUno"]["tmp_name"];
            $destinoUno=$_FILES["fotoUno"]["name"];

            $origenDos=$_FILES["fotoDos"]["tmp_name"];
            $destinoDos=$_FILES["fotoDos"]["name"];

            $fotoUno=Usuario::GuardarFoto($origenUno,$destinoUno,$_POST["legajo"]);
            $fotoDos=Usuario::GuardarFoto($origenDos,$destinoDos,$_POST["legajo"]);

            $usuario = new Usuario($_POST["legajo"],$_POST["nombre"],$_POST["clave"],$_POST["email"],$fotoUno,$fotoDos);
            $usuario->cargarArchivo($file);

            $respuesta = [ 'respuesta' => 'El Usuario se cargo exitosamente'];
        }
        else
        {
            $respuesta = [ 'respuesta' => 'El Usuario ya se encuentra cargado'];
        }
        echo json_encode($respuesta);
    }

    public static function ValidarLegajo($legajo, $dirFile)
    {
        $Usuarios = Usuario::ConstruirUsuarios($dirFile);
        if($Usuarios !=NULL)
        {
            foreach($Usuarios as $Usuario)
            {
                if($Usuario->legajo == $legajo)
                {
                  return -1;
                }
            }
        }
        return 0;
    }

    public static function ConstruirUsuarios($dirFile)
    {
        $lineas = Usuario::LeerArchivo($dirFile);
        $Usuarios = array();
        if($lineas !=NULL)
        {
            foreach($lineas as $linea)
            {
                $datos = explode(",",$linea);
                $Usuario = new Usuario((int)$datos[0],$datos[1],$datos[2],$datos[3],$datos[4],$datos[5]);
                array_push($Usuarios,$Usuario);
            }
            return $Usuarios;
        }
    }

    public function guardarFotoBackUp($origen, $destino, $legajo)
    {
        $fecha = new DateTime();//timestamp para no repetir nombre
        $fecha = $fecha->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
        $fecha = $fecha->format("d-m-Y-Hi");
            
            $extension = pathinfo($destino, PATHINFO_EXTENSION);
            $nombreBackupUno = "./img/backup/backupImgUno$legajo-$fecha.$extension";
            $nombreBackupDos = "./img/backup/backupImgDos$legajo-$fecha.$extension";
            //guardo la foto en la carpeta de backup:
            if(file_exists($nombreBackupUno)&& file_exists($nombreBackupDos))
            {
                copy($elementoAModificar->fotoUno, $nombreBackupUno);
                copy($elementoAModificar->fotoDos, $nombreBackupDos);
            }
            
            unlink($elementoAModificar->fotoUno);
            unlink($elementoAModificar->fotoDos);
    }

    public static function validarUsuarios($legajo,$clave, $file)
    {
        $usuarios = Usuario::ConstruirUsuarios($file);
        $flag = false;

        if($usuarios!=null)
        {
            foreach($usuarios as $usuario)
            {
                if(!strcasecmp($usuario->legajo,$legajo) && !strcasecmp($usuario->clave,$clave))
                {
                    return $usuario;
                    $flag=true;
                }
            }
            if($flag == false)
            {
                $respuesta = [ 'respuesta' => 'No existe usuario con ese legajo y clave'];
                echo json_encode($respuesta);
            }
        }   
    }

     function modificarUsuario($file)
    {
        $usuarios= Usuario::ConstruirUsuarios($file);
        $index= Usuario::BuscarIndiceArray($usuarios,$this->legajo);

        $origenUno=$_FILES["fotoUno"]["tmp_name"];
        $destinoUno=$_FILES["fotoUno"]["name"];

        $origenDos=$_FILES["fotoDos"]["tmp_name"];
        $destinoDos=$_FILES["fotoDos"]["name"];

        $fotoUno=Usuario::modificarArchivo($origenUno,$destinoUno,$_POST["legajo"]);
        $fotoDos=Usuario::modificarArchivo($origenDos,$destinoDos,$_POST["legajo"]);

        if($index!= -1)
        {
            $usuarios[$index]->nombre=$_POST["nombre"];
            $usuarios[$index]->email=$_POST["email"];
            $usuarios[$index]->clave=$_POST["clave"];
            $usuarios[$index]->fotoUno=$fotoUno;
            $usuarios[$index]->fotoDos=$fotoDos;
            Usuario:: VaciarArchivo($file);

            foreach($usuarios as $usuario)
            {
                $usuario->cargarArchivo($file);
            }
            $respuesta = [ 'respuesta' => 'Se Modifico el Usuario exitosamente.'];
        }
        else
        {
            $respuesta = [ 'respuesta' => 'No existe usuario con ese Legajo'];
        }
        echo json_encode($respuesta);
    }

    private static function BuscarIndiceArray($usuarios, $legajo)
{
    $indice = -1;
    for($i = 0; $i < count($usuarios); $i++)
    {    
        if($usuarios[$i]->legajo == $legajo)
        {
            $indice = $i; 
        }
    }
    return $indice;
}


public static function mostrarUsuariosSinClave($file)
{
    $usuarios= Usuario::ConstruirUsuarios($file);
    $arrayUsuarios = array();
        foreach($usuarios as $auxUsuario)
        {
            $usuario = array(
                "legajo"=> $auxUsuario->legajo,
                "nombre"=> $auxUsuario->nombre,
                "email"=> $auxUsuario->email,
                "fotoUno"=> $auxUsuario->fotoUno,
                "fotoDos"=> $auxUsuario->fotoDos);
            array_push($arrayUsuarios, $usuario);
        }
        echo json_encode($arrayUsuarios);
}

public static function mostrarUsuarios($file,$legajo)
{
    if(Usuario::ValidarLegajo($legajo,$file)==-1)
    {
        $usuarios= Usuario::ConstruirUsuarios($file);
        foreach($usuarios as $usuario)
        {
            if($usuario->legajo==$legajo)
            {
                echo json_encode($usuario);
            }
        }
    }
    else
    {
        $respuesta = [ 'respuesta' => 'No existe usuario con ese Legajo'];
        echo json_encode($respuesta);
    }
}


}
?>