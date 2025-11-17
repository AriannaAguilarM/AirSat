<?php
namespace App\Controllers;

class TestEnv extends BaseController
{
    public function index()
    {
        echo "<h1>🧪 Test Variables de Entorno</h1>";
        
        // Verificar .env
        $envPath = ROOTPATH . '.env';
        echo "<h3>1. Archivo .env</h3>";
        echo "Ruta: " . $envPath . "<br>";
        echo "Existe: " . (file_exists($envPath) ? '✅ SÍ' : '❌ NO') . "<br>";
        
        if (file_exists($envPath)) {
            echo "Contenido:<br><pre>";
            echo htmlspecialchars(file_get_contents($envPath));
            echo "</pre>";
        }
        
        // Verificar variables
        echo "<h3>2. Variables Cargadas</h3>";
        echo "<pre>";
        echo "FIREBASE_API_KEY: " . ($_ENV['FIREBASE_API_KEY'] ?? '❌ NO ENCONTRADA') . "\n";
        echo "FIREBASE_PROJECT_ID: " . ($_ENV['FIREBASE_PROJECT_ID'] ?? '❌ NO ENCONTRADA') . "\n";
        echo "FIREBASE_DATABASE_URL: " . ($_ENV['FIREBASE_DATABASE_URL'] ?? '❌ NO ENCONTRADA') . "\n";
        echo "FIREBASE_CREDENTIALS_PATH: " . ($_ENV['FIREBASE_CREDENTIALS_PATH'] ?? '❌ NO ENCONTRADA') . "\n";
        echo "</pre>";
        
        // Verificar configuración Firebase
        echo "<h3>3. Configuración Firebase</h3>";
        try {
            $config = new \Config\Firebase();
            echo "<pre>";
            echo "apiKey: " . ($config->apiKey ? '✅ CONFIGURADA' : '❌ VACÍA') . "\n";
            echo "projectId: " . $config->projectId . "\n";
            echo "databaseUrl: " . $config->databaseUrl . "\n";
            echo "credentialsPath: " . $config->credentialsPath . "\n";
            echo "</pre>";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}