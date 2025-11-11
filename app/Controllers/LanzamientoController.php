<?php

namespace App\Controllers;

use App\Models\LanzamientoModel;
use App\Models\LecturasModel;
use App\Models\ConexionModel;

class LanzamientoController extends BaseController
{
    protected $lanzamientoModel;
    protected $lecturasModel;
    protected $conexionModel;

    public function __construct()
    {
        $this->lanzamientoModel = new LanzamientoModel();
        $this->lecturasModel = new LecturasModel();
        $this->conexionModel = new ConexionModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Histórico de Lanzamientos - AirSat',
            'lanzamientos' => $this->lanzamientoModel->getLanzamientosConLecturas()
        ];

        return view('lanzamientos', $data);
    }

    public function iniciar()
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'fecha_hora_inicio' => date('Y-m-d H:i:s'),
                'descripcion' => $this->request->getPost('descripcion'),
                'lugar_captura' => $this->request->getPost('lugar_captura')
            ];

            if ($this->lanzamientoModel->iniciarLanzamiento($data)) {
                return redirect()->to('/')->with('success', 'Lanzamiento iniciado correctamente');
            } else {
                return redirect()->to('/')->with('error', 'Error al iniciar el lanzamiento');
            }
        }
    }

    public function finalizar($id = null)
    {
        if ($this->request->getMethod() === 'post' || $id) {
            $lanzamientoId = $id ?: $this->request->getPost('id_lanzamiento');
            
            $lanzamiento = $this->lanzamientoModel->find($lanzamientoId);
            if (!$lanzamiento) {
                return redirect()->to('/')->with('error', 'Lanzamiento no encontrado');
            }

            $fechaFinal = date('Y-m-d H:i:s');
            
            // Finalizar lanzamiento
            $this->lanzamientoModel->finalizarLanzamiento($lanzamientoId, $fechaFinal);
            
            // Obtener lecturas del rango
            $lecturas = $this->lecturasModel->getLecturasPorRango(
                $lanzamiento['fecha_hora_inicio'], 
                $fechaFinal
            );
            
            // Conectar lecturas al lanzamiento
            $idsLecturas = array_column($lecturas, 'id');
            $this->conexionModel->conectarLecturas($lanzamientoId, $idsLecturas);

            return redirect()->to('/')->with('success', 'Lanzamiento finalizado correctamente');
        }
    }

    public function ver($id)
    {
        $lecturas = $this->conexionModel->getLecturasPorLanzamiento($id);
        $lanzamiento = $this->lanzamientoModel->find($id);

        $data = [
            'titulo' => 'Lecturas del Lanzamiento - AirSat',
            'lecturas' => $lecturas,
            'lanzamiento' => $lanzamiento
        ];

        return view('ver_lanzamiento', $data);
    }
}