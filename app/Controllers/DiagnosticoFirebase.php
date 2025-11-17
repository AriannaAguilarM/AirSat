<?php

namespace App\Controllers;

use App\Libraries\FirebaseService;
use Config\Firebase;

class DiagnosticoFirebase extends BaseController
{
    public function index()
    {
        echo "<h1>🔍 Diagnóstico Firebase</h1>";
        
        // 1. Verificar variables de entorno
        echo "<h2>1. Variables de Entorno</h2>";
        echo "<pre>";
        echo "FIREBASE_API_KEY: " . ($_ENV['FIREBASE_API_KEY'] ?? '❌ NO ENCONTRADO') . "\n";
        echo "FIREBASE_PROJECT_ID: " . ($_ENV['FIREBASE_PROJECT_ID'] ?? '❌ NO ENCONTRADO') . "\n";
        echo "FIREBASE_DATABASE_URL: " . ($_ENV['FIREBASE_DATABASE_URL'] ?? '❌ NO ENCONTRADO') . "\n";
        echo "FIREBASE_CREDENTIALS_PATH: " . ($_ENV['FIREBASE_CREDENTIALS_PATH'] ?? '❌ NO ENCONTRADO') . "\n";
        echo "</pre>";
        
        // 2. Verificar configuración Firebase
        echo "<h2>2. Configuración Firebase</h2>";
        $firebaseConfig = new Firebase();
        echo "<pre>";
        echo "apiKey: " . $firebaseConfig->apiKey . "\n";
        echo "projectId: " . $firebaseConfig->projectId . "\n";
        echo "databaseUrl: " . $firebaseConfig->databaseUrl . "\n";
        echo "credentialsPath: " . $firebaseConfig->credentialsPath . "\n";
        echo "</pre>";
        
        // 3. Verificar archivo de credenciales
        echo "<h2>3. Archivo de Credenciales</h2>";
        $credentialsPath = $firebaseConfig->credentialsPath;
        echo "Ruta: " . $credentialsPath . "<br>";
        echo "Existe: " . (file_exists($credentialsPath) ? '✅ SÍ' : '❌ NO') . "<br>";
        
        if (file_exists($credentialsPath)) {
            echo "Es legible: " . (is_readable($credentialsPath) ? '✅ SÍ' : '❌ NO') . "<br>";
            echo "Tamaño: " . filesize($credentialsPath) . " bytes<br>";
        }
        
        // 4. Verificar conexión
        echo "<h2>4. Conexión Firebase</h2>";
        try {
            $firebaseService = new FirebaseService();
            echo "Estado conexión: " . ($firebaseService->isConnected() ? '✅ CONECTADO' : '❌ DESCONECTADO') . "<br>";
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "<br>";
        }
        
        // 5. Verificar ruta WRITEPATH
        echo "<h2>5. Rutas del Sistema</h2>";
        echo "WRITEPATH: " . WRITEPATH . "<br>";
        echo "ROOTPATH: " . ROOTPATH . "<br>";
        echo "FCPATH: " . FCPATH . "<br>";
    }
}