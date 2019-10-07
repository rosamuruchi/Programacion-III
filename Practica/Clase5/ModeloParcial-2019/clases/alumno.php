<?php
class Alumno
{
    public $nombre;
    public $apellido;
    public $email;
    public $foto;

    public function __construct($nombre, $apellido, $email)
    {
        $this->nombre = $nombre;
        $this->apellido= $apellido;
        $this->email = $email;
    }

    public function cargarAlumno($dirFile)
    {
        if(file_exists($dirFile))
        {
            $resource = fopen($dirFile,"a");
            if(file_get_contents($dirFile) != "")
            {
                fwrite($resource, "\r\n"."$this->nombre".","."$this->apellido".","."$this->email".",".trim($this->foto,"\r\n"));
            }
            else
            {
                fwrite($resource, "$this->nombre".","."$this->apellido".","."$this->email".",".trim($this->foto,"\r\n"));
            }
        }
        else
        {
            $resource = fopen($dirFile,"w");
            fwrite($resource, "$this->nombre".","."$this->apellido".","."$this->email".",".trim($this->foto,"\r\n"));
        }
        fclose($resource);
    }

    public static function ValidarEmail($email, $dirFile)
    {
        $Alumnos = Alumno::ConstruirAlumnos($dirFile);
        if($Alumnos !=NULL)
        {
            foreach($Alumnos as $Alumno)
            {
                if($Alumno->email == $email)
                {
                  return -1;  
                }
            }
        }
        return 0;
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

    public static function ConstruirAlumnos($dirFile)
    {
        $lineas = Alumno::LeerArchivo($dirFile);
        $Alumnos = array();
        if($lineas !=NULL)
        {
            foreach($lineas as $linea)
            {
                $datos = explode(",",$linea);
                $Alumno = new Alumno($datos[0],$datos[1],$datos[2]);
                $Alumno->foto = $datos[3];
                array_push($Alumnos,$Alumno);
            }
            return $Alumnos;
        }
    }

    public function MostrarAlumno()
    {
        echo "<br>Nombre: ",$this->nombre;
        echo "<br>Apellido: ",$this->apellido;
        echo "<br>Email: ",$this->email;
        echo "<br>Foto: ",$this->foto;
        echo "<br>";
    }


    public static function ConsultarAlumno($dirFile, $apellido)
    {
        $Alumnos = Alumno::ConstruirAlumnos($dirFile);
        $flag = false;
        if($Alumnos != NULL)
        {
            foreach($Alumnos as $Alumno)
            {
                if($Alumno->apellido == $apellido)
                {
                    $Alumno->MostrarAlumno();
                    $flag = true;
                }
            }
            if($flag == false)
            {
                echo "No existe Alumno con apellido: ",$apellido;
            }
        }
    }


    public function GuardarFoto($path)
    {
        if(file_exists($_FILES["foto"]["tmp_name"]))
        {
            $nombreArchivo = "";
            $arrayNombre = explode(".",$_FILES["foto"]["name"]);
            $nombreArchivo .=  $this->nombre . $this->apellido . '.' . $arrayNombre[1];
            $path .= '/' . $nombreArchivo;
            if(file_exists($path))
            {
                $this->ReemplazarFoto("./backUpFotos",$path);
            }
        
            return $path;
        }
    }

    private function ReemplazarFoto($pathBackup,$FotoExistente)
    {
        $nombreArchivo = "";
        $arrayNombre = explode(".",$FotoExistente);
        date_default_timezone_set('America/Argentina/Buenos_Aires'); //Seteo la zona horaria para que al imprimir la hora sea la hora local de argentina
        $fecha = date("d\-m\-y--H\.i\.s"); //Recibo la hora en formato dia-Mes-Año--Hora.Minuto.Seugndo
        $nombreArchivo .= $this->patente . "_" . $fecha . '.' . $arrayNombre[2]; //Creo el nombre del archivo con el Legajo, nombre, fecha y extension
        $pathBackup .= '/' . $nombreArchivo;        
        rename($FotoExistente,$pathBackup); //Muevo la foto a archivos Backup 
    }

}

?>