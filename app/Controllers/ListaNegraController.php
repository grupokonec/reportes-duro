<?php

namespace App\Controllers;

use App\Models\Connection;

class ListaNegraController
{

    public static function getDatos()
    {
        $conn = new Connection; // Instanciamos la conexion.

        // Preparamos la query 
        $query = "SELECT telefono FROM lista_negra WHERE status = 'AVO'";

        // Obtenemos los resultados
        $resultados = $conn->queryExe($query);

        $telefonos = [];

        // Los agregamos en un arreglo.
        foreach ($resultados as $telefono) {
            $telefonos[] = $telefono->telefono;
        }

        // Retornamos el arreglo.
        return $telefonos;
    }
}
