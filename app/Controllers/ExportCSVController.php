<?php

namespace App\Controllers;


class ExportCSVController
{

    public function exportCSV($arr, $date)
    {
        ob_start();

        header('Content-Type: text/csv; charset=utf-8');
        //GestionesDiariasDDMMYYYYKonectados
        header('Content-Disposition: attachment; filename=GestionesDiarias' . date('dmY', strtotime($date)) . 'Konectados.csv');

        $header_args = [
            'ID_EMPRESA_COBRANZA',
            'MES_ASIGNACION',
            'FECHA_GESTION',
            'HORA_ACCION',
            'ID_CAMPANA',
            'ID_SEGMENTO',
            'ID_GRUPO_CONTROL',
            'RUT',
            'CONTRATO',
            'ID_ACCION',
            'ID_NIVEL_UNO',
            'ID_NIVEL_DOS',
            'ID_NIVEL_TRES',
            'CLASIFICACION',
            'OBSERVACION',
            'FECHA_FUTURA',
            'FONO',
            'EMAIL',
            'COBRADOR',
        ];

        ob_end_clean();

        $output = fopen('php://output', 'w');

        fputcsv($output, $header_args, ";", '"', "\\", PHP_EOL);
        foreach ($arr as $data_item) {
            $row = implode(';', $data_item) . "\r\n";
            fwrite($output, $row);
        }

        fclose($output);
        exit;
    }
}
