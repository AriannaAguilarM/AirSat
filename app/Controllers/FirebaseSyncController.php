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

    /**
     * Página principal de sincronización
     */
    public function index()
    {
        $conexionStatus = $this->firebaseService->isConnected();
        
        $data = [
            'titulo' => 'Sincronización con Firebase - AirSat',
            'estadoConexion' => $conexionStatus,
            'config' => [
                'projectId' => $_ENV['FIREBASE_PROJECT_ID'] ?? 'No configurado',
                'databaseUrl' => $_ENV['FIREBASE_DATABASE_URL'] ?? 'No configurado'
            ]
        ];

        return view('firebase_sync', $data);
    }

    /**
     * Sincronizar todos los datos con Firebase
     */
    public function sync()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/firebase-sync');
        }

        // Verificar conexión primero
        if (!$this->firebaseService->isConnected()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: No hay conexión con Firebase. Verifica la configuración.',
                'type' => 'error'
            ]);
        }

        try {
            $resultados = [
                'lecturas' => $this->sincronizarLecturas(),
                'lanzamientos' => $this->sincronizarLanzamientos(),
                'conexiones' => $this->sincronizarConexiones()
            ];

            $totalNuevos = array_sum(array_column($resultados, 'nuevos'));
            $totalExistentes = array_sum(array_column($resultados, 'existentes'));

            if ($totalNuevos > 0) {
                $mensaje = "✅ Sincronización completa. Se agregaron {$totalNuevos} nuevos registros a Firebase.";
                $tipo = 'success';
            } else {
                $mensaje = "ℹ No había datos nuevos para sincronizar. Todos los registros ya existen en Firebase.";
                $tipo = 'info';
            }

            // Registrar en log
            log_message('info', "Sincronización Firebase completada - Proyecto: airsat-ia-392e8");

            return $this->response->setJSON([
                'success' => true,
                'message' => $mensaje,
                'type' => $tipo,
                'data' => $resultados
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en sincronización Firebase: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ Error en la sincronización: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }


    /**
     * Sincronizar lecturas
     */
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

            // Preparar datos para Firebase
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

    /**
     * Sincronizar lanzamientos
     */
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

    /**
     * Sincronizar conexiones
     */
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

    /**
     * Verificar estado de sincronización
     */
    public function status()
    {
        try {
            $lecturasFirebase = $this->firebaseService->getAll('airsat/lecturas');
            $lanzamientosFirebase = $this->firebaseService->getAll('airsat/lanzamientos');
            $conexionesFirebase = $this->firebaseService->getAll('airsat/conexion');

            $lecturasLocal = $this->lecturasModel->countAll();
            $lanzamientosLocal = $this->lanzamientoModel->countAll();
            $conexionesLocal = $this->conexionModel->countAll();

            $estado = [
                'conexion' => $this->firebaseService->testConnection(),
                'local' => [
                    'lecturas' => $lecturasLocal,
                    'lanzamientos' => $lanzamientosLocal,
                    'conexiones' => $conexionesLocal
                ],
                'firebase' => [
                    'lecturas' => count($lecturasFirebase),
                    'lanzamientos' => count($lanzamientosFirebase),
                    'conexiones' => count($conexionesFirebase)
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