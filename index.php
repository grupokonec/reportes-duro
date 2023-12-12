<?php
// Instaciamos las variables de entorno.
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// Llamada a las clases que se utilizan durante el proceso.
use App\Controllers\RegistroContactoController;
use App\Controllers\AllContactController;
use App\Controllers\ExportCSVController;

// Instanciacion de las clases
$rc = new RegistroContactoController;
$ac = new AllContactController;
$export = new ExportCSVController;

// Para escoger un documento con fecha anterior se debe  descomentar la siguiente linea y comentar la que contiene el GET

// $fecha = '2023-12-06';

// Obtenemos el parametro de la url
$fecha = $_GET['fecha'];


// Solicitamos los datos.
$resultados_ac = $ac->validar($fecha);
$resultados_rc = $rc->validar($fecha);

// Hacemos un merge con los resultados
$all = array_merge($resultados_ac, $resultados_rc);



// Descargamos el reporte.
return $export->exportCSV($all, $fecha);
