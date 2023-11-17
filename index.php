<?php
// die();
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Controllers\RegistroContactoController;
use App\Controllers\AllContactController;
use App\Controllers\ExportCSVController;

$rc = new RegistroContactoController;
$ac = new AllContactController;

$fecha = '2023-11-16';

$resultados_ac = $ac->validar($fecha);
$resultados_rc = $rc->validar($fecha);


$all = array_merge($resultados_ac, $resultados_rc);


$export = new ExportCSVController;

return $export->exportCSV($all, $fecha);
