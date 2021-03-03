<?php
class Proveedor
{
    public $id;
    public $nombre;
    public $email;
    public $foto;

    public function __construct($id,$nombre, $email,$foto)
    {
        $this->id=$id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->foto=$foto;
    }
    public function cargarArchivo($dirFile)
    {
        if(file_exists($dirFile))
        {
            $resource = fopen($dirFile,"a");
            if(file_get_contents($dirFile) != "")
            {
                fwrite($resource, "\r\n"."$this->id".","."$this->nombre".","."$this->email".",".trim($this->foto,"\r\n"));
            }
            else
            {
                fwrite($resource, "$this->id".","."$this->nombre".","."$this->email".",".trim($this->foto,"\r\n"));            }
        }
        else
        {
            $resource = fopen($dirFile,"w");
            fwrite($resource, "$this->id".","."$this->nombre".","."$this->email".",".trim($this->foto,"\r\n"));        }
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
    public static function GuardarFoto($origen,$destino,$id)
    {

            $extension=pathinfo($destino, PATHINFO_EXTENSION);
            $nombreArchivo = "./img/fotos/".$id . "." . $extension;
            if(file_exists($nombreArchivo))
            {
                $nombreArchivo =  "./img/fotos/".$id . "(2)." . $extension;
                move_uploaded_file($origen, $nombreArchivo);
            }
            else
            {
                move_uploaded_file($origen, $nombreArchivo);
            }     
            return $nombreArchivo;
    }

    public static function ConstruirProveedores($dirFile)
    {
        $lineas = Proveedor::LeerArchivo($dirFile);
        $proveedores = array();
        if($lineas !=NULL)
        {
            foreach($lineas as $linea)
            {
                $datos = explode(",",$linea);
                $proveedor = new Proveedor((int)$datos[0],$datos[1],$datos[2],$datos[3]);
                array_push($proveedores,$proveedor);
            }
            return $proveedores;
        }
    }

    public static function ValidarId($id, $dirFile)
    {
        $proveedores = Proveedor::ConstruirProveedores($dirFile);
        $retorno=false;
        if($proveedores !=NULL)
        {
            foreach($proveedores as $proveedor)
            {
                if($proveedor->id == $id)
                {
                  $retorno= true;
                }
            }
        }
        return $retorno;
    }

    public function cargarProveedor($file, $response)
    {
        if(!(Proveedor::ValidarId($_POST["id"],$file)))
        {
            $origenUno=$_FILES["foto"]["tmp_name"];
            $destinoUno=$_FILES["foto"]["name"];

            $fotoUno=Proveedor::GuardarFoto($origenUno,$destinoUno,$_POST["id"]);

            $usuario = new Proveedor($_POST["id"],$_POST["nombre"],$_POST["email"],$fotoUno);
            $usuario->cargarArchivo($file);
            $newResponse = $response->withJson('El Proveedor se cargo exitosamente', 200);
        }
        else
        {
            $newResponse = $response->withJson("Legajo repetido", 200);
        }
        return $newResponse;
    }

    public static function validarProveedor($nombre, $file,$response)
    {
        $proveedores = Proveedor::ConstruirProveedores($file);
        $flag = false;

        if($proveedores!=null)
        {
            foreach($proveedores as $proveedor)
            {
                if(!strcasecmp($proveedor->nombre,$nombre))
                {
                    $newResponse = $response->withJson($proveedor, 200);
                    $flag=true;
                }
            }
            if($flag == false)
            {
                $newResponse = $response->withJson("No existe el proveedor: "."$nombre", 200);
            }
        }
        return $newResponse;   
    }

    public static function mostrarProveedores($file,$response)
    {
        $usuarios= Proveedor::ConstruirProveedores($file);
        $flag=false;
        if($usuarios!= NULL)
        {
                $newResponse = $response->withJson($usuarios, 200);
                $flag=true;
            
        }
        if($flag==false)
        {
            $newResponse = $response->withJson("No hay Proveedores", 200);
        }
        return $newResponse;
    }
}

?>