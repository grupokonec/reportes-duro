<?php

namespace App\Controllers;


class ExportCSVController
{

    public function exportCSV(array $arr, string $date)
    {
        //Preparamos al navegador para descargar un CSV
        ob_start();

        // Le indicamos el tipo de caracter a utilizar
        header('Content-Type: text/csv; charset=utf-8');

        //Nombre para la descarga del documento: GestionesDiariasDDMMYYYYKonectados
        header('Content-Disposition: attachment; filename=GestionesDiarias' . date('dmY', strtotime($date)) . 'Konectados.csv');

        // Argumentos de la cabecera
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

        // Le decimos que aperture el documento y se pueda escribir.
        $output = fopen('php://output', 'w');

        // Rellenamos el documento con los datos obtenidos.
        fputcsv($output, $header_args, ";", '"', "\\", PHP_EOL);
        foreach ($arr as $data_item) {
            $row = implode(';', $data_item) . "\r\n";
            fwrite($output, $row);
        }

        //Solicitamos la descarga del documento.
        fclose($output);
        exit;
    }
}
