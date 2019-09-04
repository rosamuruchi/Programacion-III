<?php

class AlumnoDAO extends alumno
{
    public static function Guardar($alumno)
    {
        $unoAlumno= new alumno($alumno['nombre'],$alumno['dni'],$alumno['legajo']);
        
        echo "Alumno Guardado , Legajo:".$unoAlumno->legajo;
    }

    public static function Modificar($alumno)
    {
        
    }
}


?>