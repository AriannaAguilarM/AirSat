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
        // Verificar si ya hay un lanzamiento activo
        $lanzamientoActivo = $this->lanzamientoModel->getLanzamientoActivo();
        if ($lanzamientoActivo) {
            return redirect()->to('/')->with('error', 'Ya hay un lanzamiento activo. Finalízalo antes de iniciar uno nuevo.');
        }

        // Validar datos del formulario
        $descripcion = $this->request->getPost('descripcion');
        $lugarCaptura = $this->request->getPost('lugar_captura');

        if (empty($descripcion) || empty($lugarCaptura)) {
            return redirect()->to('/')->with('error', 'La descripción y el lugar de captura son obligatorios');
        }

        // Usar la hora local del servidor
        $fechaHoraInicio = date('Y-m-d H:i:s');

        $data = [
            'fecha_hora_inicio' => $fechaHoraInicio,
            'descripcion' => $descripcion,
            'lugar_captura' => $lugarCaptura,
            'fecha_hora_final' => null
        ];

        try {
            $result = $this->lanzamientoModel->iniciarLanzamiento($data);
            
            if ($result) {
                return redirect()->to('/')->with('success', 'Lanzamiento iniciado correctamente a las ' . $fechaHoraInicio);
            } else {
                return redirect()->to('/')->with('error', 'Error al iniciar el lanzamiento');
            }
        } catch (\Exception $e) {
            return redirect()->to('/')->with('error', 'Error en la base de datos: ' . $e->getMessage());
        }
    }

    public function finalizar($id = null)
    {
        // Si no se proporciona ID por URL, obtener del formulario
        $lanzamientoId = $id ?? $this->request->getPost('id_lanzamiento');
        
        if (!$lanzamientoId) {
            return redirect()->to('/')->with('error', 'ID de lanzamiento no proporcionado');
        }

        // Verificar que el lanzamiento existe y está activo
        $lanzamiento = $this->lanzamientoModel->find($lanzamientoId);
        if (!$lanzamiento) {
            return redirect()->to('/')->with('error', 'Lanzamiento no encontrado');
        }

        if ($lanzamiento['fecha_hora_final'] !== null) {
            return redirect()->to('/')->with('error', 'El lanzamiento ya fue finalizado anteriormente');
        }

        // Usar la hora local del servidor
        $fechaFinal = date('Y-m-d H:i:s');
        
        try {
            // DEBUG: Ver las fechas que se están usando
            // file_put_contents(WRITEPATH . 'debug.log', 
            //     "Inicio: " . $lanzamiento['fecha_hora_inicio'] . "\n" .
            //     "Final: " . $fechaFinal . "\n", 
            //     FILE_APPEND
            // );

            // Finalizar lanzamiento
            $this->lanzamientoModel->finalizarLanzamiento($lanzamientoId, $fechaFinal);
            
            // Obtener lecturas del rango
            $lecturas = $this->lecturasModel->getLecturasPorRango(
                $lanzamiento['fecha_hora_inicio'], 
                $fechaFinal
            );
            
            // DEBUG: Ver cuántas lecturas se encontraron
            // file_put_contents(WRITEPATH . 'debug.log', 
            //     "Lecturas encontradas: " . count($lecturas) . "\n", 
            //     FILE_APPEND
            // );

            // Conectar lecturas al lanzamiento
            if (!empty($lecturas)) {
                $idsLecturas = array_column($lecturas, 'id');
                $this->conexionModel->conectarLecturas($lanzamientoId, $idsLecturas);
                $mensaje = 'Lanzamiento finalizado correctamente. ' . count($lecturas) . ' lecturas asociadas.';
            } else {
                $mensaje = 'Lanzamiento finalizado correctamente. No se encontraron lecturas en el rango de tiempo.';
            }

            return redirect()->to('/')->with('success', $mensaje);

        } catch (\Exception $e) {
            return redirect()->to('/')->with('error', 'Error al finalizar el lanzamiento: ' . $e->getMessage());
        }
    }

    public function ver($id)
    {
        $lecturas = $this->conexionModel->getLecturasPorLanzamiento($id);
        $lanzamiento = $this->lanzamientoModel->find($id);

        if (!$lanzamiento) {
            return redirect()->to('/lanzamientos')->with('error', 'Lanzamiento no encontrado');
        }

        $data = [
            'titulo' => 'Lecturas del Lanzamiento - AirSat',
            'lecturas' => $lecturas,
            'lanzamiento' => $lanzamiento
        ];

        return view('ver_lanzamiento', $data);
    }
}