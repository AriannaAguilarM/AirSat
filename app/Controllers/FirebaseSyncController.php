<?php

namespace App\Controllers;

use App\Libraries\FirebaseService;
use App\Models\LecturasModel;
use App\Models\LanzamientoModel;
use App\Models\ConexionModel;

class FirebaseSyncController extends BaseController
{
    protected $firebaseService;
    protected $lecturasModel;
    protected $lanzamientoModel;
    protected $conexionModel;

    public function __construct()
    {
        $this->firebaseService = new FirebaseService();
        $this->lecturasModel = new LecturasModel();
        $this->lanzamientoModel = new LanzamientoModel();
        $this->conexionModel = new ConexionModel();
    }

    public function index()
    {
        $firebaseConfig = new \Config\Firebase();
        $conexionStatus = $this->firebaseService->isConnected();
        
        $data = [
            'titulo' => 'Sincronización con Firebase - AirSat',
            'estadoConexion' => $conexionStatus,
            'config' => [
                'projectId' => $firebaseConfig->projectId,
                'databaseUrl' => $firebaseConfig->databaseUrl
            ]
        ];

        return view('firebase_sync', $data);
    }

    public function sync()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/firebase-sync');
        }

        if (!$this->firebaseService->isConnected()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: No hay conexión con Firebase',
                'type' => 'error'
            ]);
        }

        try {
            $resultados = [
                'lecturas' => $this->sincronizarLecturas(),
                'lanzamientos' => $this->sincronizarLanzamientos(),
                'conexiones' => $this->sincronizarConexiones(),
                'analisis_ia' => $this->sincronizarAnalisisIA()
            ];

            $totalNuevos = array_sum(array_column($resultados, 'nuevos'));
            $totalExistentes = array_sum(array_column($resultados, 'existentes'));

            if ($totalNuevos > 0) {
                $mensaje = "Sincronización completa. Se agregaron {$totalNuevos} nuevos registros a Firebase.";
                $tipo = 'success';
            } else {
                $mensaje = "No había datos nuevos para sincronizar. Todos los registros ya existen en Firebase.";
                $tipo = 'info';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $mensaje,
                'type' => $tipo,
                'data' => $resultados
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error en la sincronización: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    private function sincronizarLecturas(): array
    {
        $lecturas = $this->lecturasModel->findAll();
        $nuevos = 0;
        $existentes = 0;

        foreach ($lecturas as $lectura) {
            $id = $lectura['id'];
            $path = 'airsat/lecturas';

            if ($this->firebaseService->exists($path, $id)) {
                $existentes++;
                continue;
            }

            $datosFirebase = [
                'id' => $lectura['id'],
                'temperatura' => $lectura['temperatura'] ?? null,
                'humedad' => $lectura['humedad'] ?? null,
                'presion_atmosferica' => $lectura['presion_atmosferica'] ?? null,
                'altura_absoluta' => $lectura['altura_absoluta'] ?? null,
                'altura_relativa' => $lectura['altura_relativa'] ?? null,
                'AQI' => $lectura['AQI'] ?? null,
                'TVOC' => $lectura['TVOC'] ?? null,
                'eCO2' => $lectura['eCO2'] ?? null,
                'PM1' => $lectura['PM1'] ?? null,
                'PM2_5' => $lectura['PM2_5'] ?? null,
                'PM10' => $lectura['PM10'] ?? null,
                'AX' => $lectura['AX'] ?? null,
                'AY' => $lectura['AY'] ?? null,
                'AZ' => $lectura['AZ'] ?? null,
                'GX' => $lectura['GX'] ?? null,
                'GY' => $lectura['GY'] ?? null,
                'GZ' => $lectura['GZ'] ?? null,
                'fecha_hora' => $lectura['fecha_hora'],
                'sync_timestamp' => date('Y-m-d H:i:s')
            ];

            if ($this->firebaseService->set($path, $id, $datosFirebase)) {
                $nuevos++;
            }
        }

        return [
            'total' => count($lecturas),
            'nuevos' => $nuevos,
            'existentes' => $existentes
        ];
    }

    private function sincronizarLanzamientos(): array
    {
        $lanzamientos = $this->lanzamientoModel->findAll();
        $nuevos = 0;
        $existentes = 0;

        foreach ($lanzamientos as $lanzamiento) {
            $id = $lanzamiento['id'];
            $path = 'airsat/lanzamientos';

            if ($this->firebaseService->exists($path, $id)) {
                $existentes++;
                continue;
            }

            $datosFirebase = [
                'id' => $lanzamiento['id'],
                'fecha_hora_inicio' => $lanzamiento['fecha_hora_inicio'],
                'fecha_hora_final' => $lanzamiento['fecha_hora_final'] ?? null,
                'descripcion' => $lanzamiento['descripcion'],
                'lugar_captura' => $lanzamiento['lugar_captura'],
                'sync_timestamp' => date('Y-m-d H:i:s')
            ];

            if ($this->firebaseService->set($path, $id, $datosFirebase)) {
                $nuevos++;
            }
        }

        return [
            'total' => count($lanzamientos),
            'nuevos' => $nuevos,
            'existentes' => $existentes
        ];
    }

    private function sincronizarConexiones(): array
    {
        $conexiones = $this->conexionModel->findAll();
        $nuevos = 0;
        $existentes = 0;

        foreach ($conexiones as $conexion) {
            $id = $conexion['id'];
            $path = 'airsat/conexion';

            if ($this->firebaseService->exists($path, $id)) {
                $existentes++;
                continue;
            }

            $datosFirebase = [
                'id' => $conexion['id'],
                'id_lecturas' => $conexion['id_lecturas'],
                'id_lanzamiento' => $conexion['id_lanzamiento'],
                'sync_timestamp' => date('Y-m-d H:i:s')
            ];

            if ($this->firebaseService->set($path, $id, $datosFirebase)) {
                $nuevos++;
            }
        }

        return [
            'total' => count($conexiones),
            'nuevos' => $nuevos,
            'existentes' => $existentes
        ];
    }

    private function sincronizarAnalisisIA(): array
    {
        $db = \Config\Database::connect();
        $analisis = $db->table('analisis_ia')->get()->getResultArray();
        
        $nuevos = 0;
        $existentes = 0;

        foreach ($analisis as $analisisItem) {
            $idLanzamiento = $analisisItem['id_lanzamiento'];
            
            if ($this->firebaseService->exists('airsat/analisis_ia', $idLanzamiento)) {
                $existentes++;
                continue;
            }

            $datosFirebase = [
                'id_lanzamiento' => $analisisItem['id_lanzamiento'],
                'analisis_texto' => $analisisItem['analisis_texto'],
                'fecha_creacion' => $analisisItem['fecha_creacion'],
                'modelo_utilizado' => $analisisItem['modelo_utilizado'],
                'sync_timestamp' => date('Y-m-d H:i:s')
            ];

            if ($this->firebaseService->sincronizarAnalisisIA($datosFirebase)) {
                $nuevos++;
            }
        }

        return [
            'total' => count($analisis),
            'nuevos' => $nuevos,
            'existentes' => $existentes
        ];
    }

    public function status()
    {
        try {
            $lecturasFirebase = $this->firebaseService->getAll('airsat/lecturas');
            $lanzamientosFirebase = $this->firebaseService->getAll('airsat/lanzamientos');
            $conexionesFirebase = $this->firebaseService->getAll('airsat/conexion');
            $analisisFirebase = $this->firebaseService->getAll('airsat/analisis_ia');

            $lecturasLocal = $this->lecturasModel->countAll();
            $lanzamientosLocal = $this->lanzamientoModel->countAll();
            $conexionesLocal = $this->conexionModel->countAll();
            
            $db = \Config\Database::connect();
            $analisisLocal = $db->table('analisis_ia')->countAllResults();

            $estado = [
                'conexion' => $this->firebaseService->testConnection(),
                'local' => [
                    'lecturas' => $lecturasLocal,
                    'lanzamientos' => $lanzamientosLocal,
                    'conexiones' => $conexionesLocal,
                    'analisis_ia' => $analisisLocal
                ],
                'firebase' => [
                    'lecturas' => count($lecturasFirebase),
                    'lanzamientos' => count($lanzamientosFirebase),
                    'conexiones' => count($conexionesFirebase),
                    'analisis_ia' => count($analisisFirebase)
                ]
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $estado
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error obteniendo estado: ' . $e->getMessage()
            ]);
        }
    }
}