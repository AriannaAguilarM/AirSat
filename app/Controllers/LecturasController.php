<?php

namespace App\Controllers;

use App\Models\LecturasModel;

class LecturasController extends BaseController
{
    protected $lecturasModel;

    public function __construct()
    {
        $this->lecturasModel = new LecturasModel();
    }

    public function ultimaLectura()
    {
        $lectura = $this->lecturasModel->getUltimaLectura();
        return $this->response->setJSON($lectura);
    }

    public function lecturasRecientes()
    {
        $lecturas = $this->lecturasModel->getLecturasRecientes(20);
        return $this->response->setJSON($lecturas);
    }
}