<?php

namespace App\Controllers;

use App\Models\Connection; // llamamos a la clase que contiene la conexion


class AllContactController
{
    public function getData(string $fecha)
    {
        $conn = new Connection; // Instanciamos la conexion.

        if (strlen($fecha) > 0) { // Verificamos que la fecha contenga un un valor.

            // Realizamos la consulta
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

        // Ejecutamos la consulta.
        $resultados = $conn->queryExe($query);

        //Retornamos los resultados
        return $resultados;
    }

    public function validar(string $fecha)
    {
        // Hacemos la peticion de la data, instanciando al metodo getData y le pasamos como parametro el string con la fecha.
        $res = $this->getData($fecha);

        // Declaramos un arreglo vacio, que va a contener los registros formateados.
        $arr = [];

        // Instancamos la lista negra, nos retorna un arreglo con los numeros de telefonos de la lista negra.
        $rst_ln = ListaNegraController::getDatos();


        // Iniciamos el recorrido. 
        foreach ($res as $key => $row) {

            //Primera verificacion, Lista negra, donde verificamos si el telefono existe dentro de la lista.
            if (!in_array($row->telefono, $rst_ln)) {

                /**
                 * Segunda verificacion: 
                 * validamos que no venga el telefono vacio
                 * validamos que no venga el telefono en null
                 * validamos que no venga el telefono tenga un largo de 9 digitos
                 */

                if ($row->telefono == '' || $row->telefono == null || preg_match('/^\d{9}$/', $row->telefono)) {

                    // Declaramos las variables con los datos a rellenar en vacio.
                    $ID_ACCION = '';
                    $ID_NIVEL_UNO = '';
                    $ID_NIVEL_DOS = '';
                    $ID_NIVEL_TRES = '';
                    $CLASIFICACION = '';
                    $OBSERVACION = '';
                    $COBRADOR = '';

                    if ($row->tipo == 'AU') { // VALIDAMOS EL TIPO IVR

                        // Asignamos valores a las variables.
                        $ID_ACCION = "003";
                        $COBRADOR = '108';

                        // Segun el arbol y el tipo de respuesta encontrado comenzamos a validar para continuar la asignacion de variables.
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


                    // Armamos el arreglo con el registros

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
        }
        return $arr;
    }
}
