<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

class Routes extends BaseConfig
{
    public function __construct()
    {
        $this->routes = new RouteCollection(null);
        $this->configureRoutes();
    }

    private function configureRoutes(): void
    {
        // Página principal
        $this->routes->get('/', 'Home::index');
        
        // Lecturas
        $this->routes->get('lecturas/ultima', 'LecturasController::ultimaLectura');
        $this->routes->get('lecturas/recientes', 'LecturasController::lecturasRecientes');
        
        // Lanzamientos
        $this->routes->get('lanzamientos', 'LanzamientoController::index');
        $this->routes->get('lanzamiento/ver/(:num)', 'LanzamientoController::ver/$1');
        $this->routes->post('lanzamiento/iniciar', 'LanzamientoController::iniciar');
        $this->routes->post('lanzamiento/finalizar', 'LanzamientoController::finalizar');
        $this->routes->post('lanzamiento/finalizar/(:num)', 'LanzamientoController::finalizar/$1');
        
        // Exportación
        $this->routes->get('export/pdf/(:num)', 'ExportController::pdfLanzamiento/$1');
        $this->routes->post('export/multiples', 'ExportController::pdfMultiplesLanzamientos');
    }

    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }
}