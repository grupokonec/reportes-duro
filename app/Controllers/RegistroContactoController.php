<?php

namespace App\Controllers;

use App\Models\Connection;


class RegistroContactoController
{


    //llamada solo es 
    //agente o titular nada enviado



    public function getData($fecha = '', $canal = [], $limit = 10) // ! cambiar el limit a = '', de momento solo es pruebas
    {
        $conn = new Connection;

        if (strlen($fecha) > 0) {
            $query = "SELECT
                        rc.rut,
                rc.telefono,
                rc.nopago,
                rc.idrespuesta,
                rc.modo,
                rc.fecha,
                rc.feccomp,
                        c.IDEmpresaCobranza, c.MesAsignacion, c.Segmento, c.IDCampana, c.IDGrupoControl, c.Contrato
                        FROM (SELECT * FROM registro_contacto WHERE date(fecha)='$fecha' AND telefono != 'MANUAL') rc
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
                        ) AS c ON rc.rut = c.rutsd";
        }

        // return $query;
        $resultados = $conn->queryExe($query);

        return $resultados;
    }


    public function validar($fecha)
    {

        $res = $this->getData($fecha);

        $arr_rc = [];

        foreach ($res as $key_rc => $row_rc) {
            if (preg_match('/^\d{9}$/', $row_rc->telefono)) {
                if ($row_rc->idrespuesta != '555') {
                    $ID_ACCION = '';
                    $ID_NIVEL_UNO = '';
                    $ID_NIVEL_DOS = '';
                    $ID_NIVEL_TRES = '';
                    $CLASIFICACION = '';
                    $OBSERVACION = '';
                    $COBRADOR = '';


                    switch ($row_rc->modo) {
                        case 'INBOUND':
                            $COBRADOR = '106';
                            $ID_ACCION = '008';
                            break;
                        case 'OUTBOUND':
                            $COBRADOR = '106';
                            $ID_ACCION = '009';
                            break;
                        case 'MANUAL':
                            $COBRADOR = '106';
                            $ID_ACCION = '009';
                            break;
                        case 'BOT':
                            $COBRADOR = '108';
                            $ID_ACCION = '001';
                            break;
                    }

                    $CLASIFICACION =  $row_rc->nopago;
                    $OBSERVACION =  $row_rc->idrespuesta;

                    if (
                        ($row_rc->idrespuesta == '050' ||
                            $row_rc->idrespuesta == '051' ||
                            $row_rc->idrespuesta == '052' ||
                            $row_rc->idrespuesta == '053' ||
                            $row_rc->idrespuesta == '054' ||
                            $row_rc->idrespuesta == '055' ||
                            $row_rc->idrespuesta == '056' ||
                            $row_rc->idrespuesta == '057' ||
                            $row_rc->idrespuesta == '059' ||
                            $row_rc->idrespuesta == '060' ||
                            $row_rc->idrespuesta == '061' ||
                            $row_rc->idrespuesta == '063' ||
                            $row_rc->idrespuesta == '064'
                        ) &&
                        ($row_rc->nopago == '095' ||
                            $row_rc->nopago == '086' ||
                            $row_rc->nopago == '087' ||
                            $row_rc->nopago == '088' ||
                            $row_rc->nopago == '089' ||
                            $row_rc->nopago == '090' ||
                            $row_rc->nopago == '092' ||
                            $row_rc->nopago == '093' ||
                            $row_rc->nopago == '094')
                    ) {

                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '023';
                        $ID_NIVEL_TRES = '036';
                    } elseif ($row_rc->idrespuesta == '058' && $ID_ACCION != '001') {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '022';
                        $ID_NIVEL_TRES = '037';
                        $CLASIFICACION = '';
                    } elseif ($row_rc->idrespuesta == '062' || $row_rc->idrespuesta == '065' || $row_rc->idrespuesta == '066') {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '022';
                        $ID_NIVEL_TRES = '039';
                        $CLASIFICACION = '';
                    }

                    if ($row_rc->idrespuesta == '090' && ($ID_ACCION != '009' && $ID_ACCION != '001')) {
                        $ID_NIVEL_UNO = '017';
                        $ID_NIVEL_DOS = '026';
                        $ID_NIVEL_TRES = '038';
                    } elseif ($row_rc->idrespuesta == '051' && ($ID_ACCION != '009' && $ID_ACCION != '001')) {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '024';
                        $ID_NIVEL_TRES = '038';
                    } elseif (($row_rc->idrespuesta == '053' || $row_rc->idrespuesta == '050') && ($ID_ACCION != '009' && $ID_ACCION != '008' && $ID_ACCION != '001')) {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '027';
                        $ID_NIVEL_TRES = '036';
                    } elseif ($row_rc->idrespuesta == '058' && ($ID_ACCION != '009' && $ID_ACCION != '001')) {
                        $ID_NIVEL_UNO = '016';
                        $ID_NIVEL_DOS = '027';
                        $ID_NIVEL_TRES = '037';
                    }




                    if ($ID_NIVEL_UNO != '') {
                        $arr_rc[$key_rc] = [
                            'ID_EMPRESA_COBRANZA' => $row_rc->IDEmpresaCobranza,
                            'MES_ASIGNACION' => date('Y') . ($row_rc->MesAsignacion < 10 ? '0' . $row_rc->MesAsignacion : $row_rc->MesAsignacion),
                            'FECHA_GESTION' => date('d-m-Y', strtotime($row_rc->fecha)),
                            'HORA_ACCION' => date('H:i', strtotime($row_rc->fecha)),
                            'ID_CAMPANA' => $row_rc->IDCampana,
                            'ID_SEGMENTO' => $row_rc->Segmento == 'J' ? '151' : '150',
                            'ID_GRUPO_CONTROL' => $row_rc->IDGrupoControl,
                            'RUT' =>  $row_rc->rut,
                            'CONTRATO' => (trim($row_rc->Contrato, " ")),
                            'ID_ACCION' => $ID_ACCION,
                            'ID_NIVEL_UNO' => $ID_NIVEL_UNO,
                            'ID_NIVEL_DOS' => $ID_NIVEL_DOS,
                            'ID_NIVEL_TRES' => $ID_NIVEL_TRES,
                            'CLASIFICACION' => $CLASIFICACION,
                            'OBSERVACION' => $OBSERVACION,
                            'FECHA_FUTURA' => $row_rc->feccomp,
                            'FONO' => '56' . $row_rc->telefono,
                            'EMAIL' => '',
                            'COBRADOR' => $COBRADOR,
                        ];
                    }
                }
            }
        }

        return $arr_rc;
    }
}
