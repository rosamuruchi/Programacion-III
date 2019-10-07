<?php
include_once "alumno.php";
class Materia
{
    #region Atributos
    public $nombre;
    public $codigo;
    public $cupoAlumnos;
    public $aula;
    #endregion
    #region Constructor
    public function __construct($nombre, $codigo, $cupoAlumnos, $aula)
    {
        $this->nombre =$nombre;
        $this->codigo = $codigo;
        $this->cupoAlumnos = $cupoAlumnos;
        $this->aula= $aula;
    }
    #endregion
    #region Metodos
    public function cargarMateria($dirFile)
    {
        if(file_exists($dirFile))
        {
            $resource = fopen($dirFile,"a");
            if(file_get_contents($dirFile) != "")
            {
                fwrite($resource, "\r\n"."$this->nombre".","."$this->codigo".","."$this->cupoAlumnos".","."$this->aula");
            }
            else
            {
                fwrite($resource, "\r\n"."$this->nombre".","."$this->codigo".","."$this->cupoAlumnos".","."$this->aula");
            }
        }
        else
        {
            $resource = fopen($dirFile,"w");
            fwrite($resource, "\r\n"."$this->nombre".","."$this->codigo".","."$this->cupoAlumnos".","."$this->aula");
        }
        fclose($resource);
    }


    public static function ValidarCodigoMateria($codigoMateria, $dirFile)
    {
        $Materias = Materia::ConstruirMaterias($dirFile);
        if($Materias !=NULL)
        {
            foreach($Materias as $Materia)
            {
                if($materia->codigo == $codigoMateria)
                {
                  return -1;  
                }
            }
        }
        return 0;
    }



    public static function LeerArchivoMaterias($dirFile)
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
    public static function ConstruirMaterias($dirFile)
    {
        $lineas = Materia::LeerArchivoMaterias($dirFile);
        $Materias = array();
        if($lineas !=NULL)
        {
            foreach($lineas as $linea)
            {
                if(!empty($linea))
                {
                    $datos = explode(",",$linea);
                    $materia = new Materia($datos[2],(int)$datos[0],(int)$datos[1]);
                    array_push($Materias,$materia);
                }
            }
            return $Materias;
        }
    }
    /*public static function MostrarPedidos($dirFile,$dirFileProveedor)
    {
        $pedidos = Pedidos::ConstruirPedidos($dirFile);
        $proveedores = Proveedor::ConstruirProveedores($dirFileProveedor);
        foreach($pedidos as $pedido)
        {
            foreach($proveedores as $proveedor)
            {
                if($proveedor->id == $pedido->idProveedor)
                {
                    $pedido->ListarPedidos($proveedor->nombre);
                    echo "<br>Nombre del Proveedor: ",$proveedor->nombre;
                    echo "<br>";
                    break;
                }
            }
        }
    }
    public function ListarPedidos()
    {
        echo "<br><br>Producto: ",$this->producto;
        echo "<br>Cantidad: ",$this->cantidad;
        echo "<br>Id Proveedor: ",$this->idProveedor;
    }
    public static function listarPedidoProveedor($dirFilePedido,$dirFileProveedor,$id)
    {
        $pedidos = Pedidos::ConstruirPedidos($dirFilePedido);
        $proveedores = Proveedor::ConstruirProveedores($dirFileProveedor);
        $flag = false;
        foreach($proveedores as $proveedor)
        {
            if($proveedor->id == $id)
            {
                foreach($pedidos as $pedido)
                {
                    if($proveedor->id == $pedido->idProveedor)
                    {
                        $pedido->ListarPedidos();
                        $flag = true;
                    }
                }
                break;
            }
        }
        if(!($flag))
        {
            echo "<br>El proveedor ingresado no existe<br>";
        }
    }
    #endregion*/
}
?>