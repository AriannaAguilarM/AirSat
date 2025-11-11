<?php

namespace App\Controllers;

use App\Models\LecturasModel;
use App\Models\LanzamientoModel;

class Home extends BaseController
{
    protected $lecturasModel;
    protected $lanzamientoModel;

    public function __construct()
    {
        $this->lecturasModel = new LecturasModel();
        $this->lanzamientoModel = new LanzamientoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Panel Principal - AirSat',
            'ultimaLectura' => $this->lecturasModel->getUltimaLectura(),
            'lanzamientoActivo' => $this->lanzamientoModel->getLanzamientoActivo()
        ];

        return view('index', $data);
    }
}