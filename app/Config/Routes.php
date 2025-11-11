<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\BaseController;
use App\Controllers\ExportController;
use App\Controllers\Home;
use App\Controllers\LanzamientoController;
use App\Controllers\LecturasController;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('lecturas/ultima', 'LecturasController::ultimaLectura');
$routes->get('lecturas/recientes', 'LecturasController::lecturasRecientes');
$routes->get('lanzamientos', 'LanzamientoController::index');
$routes->get('lanzamiento/ver/(:num)', 'LanzamientoController::ver/$1');
$routes->post('lanzamiento/iniciar', 'LanzamientoController::iniciar');
$routes->post('lanzamiento/finalizar', 'LanzamientoController::finalizar');
$routes->post('lanzamiento/finalizar/(:num)', 'LanzamientoController::finalizar/$1');

$routes->get('export/pdf/(:num)', 'ExportController::pdfLanzamiento/$1');
$routes->post('export/multiples', 'ExportController::pdfMultiplesLanzamientos');