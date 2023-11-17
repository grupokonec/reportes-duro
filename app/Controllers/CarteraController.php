<?php

namespace App\Controllers;

use App\Models\Connection;


class CarteraController
{


    public function getDatosCampania($rut)
    {

        $conn = new Connection;
        $query = "SELECT
                   IDEmpresaCobranza,
                    MesAsignacion,
                    Segmento,
                    IDCampana,
                    IDGrupoControl,
                    Contrato
                FROM
                    cartera
                WHERE
                    RUTSD = '$rut'
                ORDER BY
                    FechaVencimiento ASC
                LIMIT 1";


        $arr_res = $conn->queryExe($query);


        // return $query;
        return $arr_res;
    }
}
