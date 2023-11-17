<?php

namespace App\Controllers;

use App\Models\Connection;


class AllContactController
{
    public function getData($fecha = '', $canal = [], $limit = '') // ! cambiar el limit a = '', de momento solo es pruebas
    {
        $conn = new Connection;

        // return strlen($fecha);

        if (strlen($fecha) > 0) {
            $query = "SELECT
                        ac.rut, ac.tipo, ac.respuesta, ac.fecha, ac.feccomp, ac.telefono, ac.glosa,
                        c.IDEmpresaCobranza, c.MesAsignacion, c.Segmento, c.IDCampana, c.IDGrupoControl, c.Contrato
                        FROM (SELECT * FROM bdcl22.all_contacts WHERE date(fecha)='$fecha' AND tipo IN ('AU', 'EM', 'SM', 'VI')) ac
                        INNER JOIN (
                            SELECT
                                c1.rutsd,
                                MAX(c1.IDEmpresaCobranza) AS IDEmpresaCobranza,
                                MAX(c1.MesAsignacion) AS MesAsignacion,
                                MAX(c1.Segmento) AS Segmento,
                                MAX(c1.IDCampana) AS IDCampana,
                                MAX(c1.IDGrupoControl) AS IDGrupoControl,
                                MAX(c1.Contrato) AS Contrato
                            FROM
                                bdcl22.cartera_primer_dia c1
                            GROUP BY c1.RUTSD
                        ) AS c ON ac.rut = c.rutsd";
        }

        $resultados = $conn->queryExe($query);
        return $resultados;
    }

    public function validar($fecha)
    {
        $res = $this->getData($fecha);
        $arr = [];



        foreach ($res as $key => $row) {

            if (preg_match('/^\d{9}$/', $row->telefono) || $row->telefono == '') {

                $ID_ACCION = '';
                $ID_NIVEL_UNO = '';
                $ID_NIVEL_DOS = '';
                $ID_NIVEL_TRES = '';
                $CLASIFICACION = '';
                $OBSERVACION = '';
                $COBRADOR = '';

                if ($row->tipo == 'AU') { // VALIDAMOS EL TIPO IVR
                    $ID_ACCION = "003";
                    $COBRADOR = '108';

                    if ($row->respuesta == '32 SC BUZON' || $row->respuesta == '30 SC OCUPADO' || $row->respuesta == 'Outbound Pre-Routing Drop' || $row->respuesta == '24 CI AGENTE NO DISPONIBLE' || $row->respuesta == '35 SC LLAMADA TERMINADA') {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '038';
                    } elseif ($row->respuesta == '34 SC NUMERO NO EXISTE') {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '037';
                    } elseif ($row->respuesta == 'Call Transferred') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '029';
                        $ID_NIVEL_TRES = '036';
                    } else {
                        $ID_NIVEL_UNO = $row->respuesta;
                    }
                } elseif ($row->tipo == 'VI') { // VALIDAMOS EL TIPO DISCADOR
                    $ID_ACCION = '009';
                    $ID_NIVEL_UNO = '017';
                    $ID_NIVEL_DOS = '021';
                    $ID_NIVEL_TRES = '038';
                    $COBRADOR = '107';
                } elseif ($row->tipo == 'EM') { // VALIDAMOS EL TIPO EMAIL
                    $ID_ACCION = '005';
                    $COBRADOR = '108';

                    if ($row->respuesta == 'SENT') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '024';
                        $ID_NIVEL_TRES = '038';
                    } elseif ($row->respuesta == 'DELIVERED') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '025';
                        $ID_NIVEL_TRES = '036';
                    } elseif ($row->respuesta == 'LEIDO' && $ID_ACCION != '009') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '027';
                        $ID_NIVEL_TRES = '036';
                    } elseif ($row->respuesta == 'BOUNCE' && $ID_ACCION != '009') {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '038';
                    } elseif ($row->respuesta == 'INVALID ADDRESS') {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '037';
                    }
                }


                $arr[$key] = [
                    'ID_EMPRESA_COBRANZA' => $row->IDEmpresaCobranza,
                    'MES_ASIGNACION' => date('Y') . ($row->MesAsignacion < 10 ? '0' . $row->MesAsignacion : $row->MesAsignacion),
                    'FECHA_GESTION' => date('d-m-Y', strtotime($row->fecha)),
                    'HORA_ACCION' => date('H:i', strtotime($row->fecha)),
                    'ID_CAMPANA' => $row->IDCampana,
                    'ID_SEGMENTO' => $row->Segmento == 'J' ? '151' : '150',
                    'ID_GRUPO_CONTROL' => $row->IDGrupoControl,
                    'RUT' =>  $row->rut,
                    'CONTRATO' => (trim($row->Contrato, " ")),
                    'ID_ACCION' => $ID_ACCION,
                    'ID_NIVEL_UNO' => $ID_NIVEL_UNO,
                    'ID_NIVEL_DOS' => $ID_NIVEL_DOS,
                    'ID_NIVEL_TRES' => $ID_NIVEL_TRES,
                    'CLASIFICACION' => $CLASIFICACION != '' ?  $CLASIFICACION  : '',
                    'OBSERVACION' => $OBSERVACION,
                    'FECHA_FUTURA' => $row->feccomp,
                    'FONO' => ($row->tipo != 'EM' ? '56' . $row->telefono : ''),
                    'EMAIL' => $row->tipo == 'EM' ? $row->glosa : '',
                    'COBRADOR' => $COBRADOR,
                ];
            }
        }
        return $arr;
        // Agregar salto de linea
        // !!vericar correo
        // ! telefono 11 digitos 

        // ! el primer fecha de vencimiento para campa;a y resto de datos. 

    }
}
