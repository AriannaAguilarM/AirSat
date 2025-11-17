<?php

namespace App\Libraries;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Config\Firebase as FirebaseConfig;
use Exception;

class FirebaseService
{
    protected $database;
    protected $firebaseConfig;
    protected $isConnected = false;

    public function __construct()
    {
        $this->firebaseConfig = new FirebaseConfig();
        $this->initializeFirebase();
    }

    private function initializeFirebase()
    {
        try {
            // Verificar que el archivo de credenciales existe
            if (!file_exists($this->firebaseConfig->credentialsPath)) {
                throw new Exception('Archivo de credenciales no encontrado: ' . $this->firebaseConfig->credentialsPath);
            }

            $factory = (new Factory)
                ->withServiceAccount($this->firebaseConfig->credentialsPath)
                ->withDatabaseUri($this->firebaseConfig->databaseUrl);

            $this->database = $factory->createDatabase();
            $this->isConnected = true;
            
            log_message('info', 'Firebase inicializado correctamente');

        } catch (Exception $e) {
            $this->isConnected = false;
            log_message('error', 'Error inicializando Firebase: ' . $e->getMessage());
            throw new Exception('No se pudo conectar a Firebase: ' . $e->getMessage());
        }
    }

    /**
     * Obtener referencia a la base de datos
     */
    public function getDatabase(): ?Database
    {
        return $this->isConnected ? $this->database : null;
    }

    /**
     * Verificar si un registro existe en Firebase
     */
    public function exists(string $path, string $key): bool
    {
        if (!$this->isConnected) {
            return false;
        }

        try {
            $reference = $this->database->getReference($path . '/' . $key);
            $snapshot = $reference->getSnapshot();
            return $snapshot->exists();
        } catch (Exception $e) {
            log_message('error', "Error verificando existencia en {$path}/{$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guardar datos con ID específico
     */
    public function set(string $path, string $key, array $data): bool
    {
        if (!$this->isConnected) {
            log_message('error', "Intento de guardar en Firebase sin conexión: {$path}/{$key}");
            return false;
        }

        try {
            $reference = $this->database->getReference($path . '/' . $key);
            $reference->set($data);
            log_message('info', "Datos guardados en {$path}/{$key}");
            return true;
        } catch (Exception $e) {
            log_message('error', "Error guardando en {$path}/{$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todos los registros de una ruta
     */
    public function getAll(string $path): array
    {
        if (!$this->isConnected) {
            return [];
        }

        try {
            $reference = $this->database->getReference($path);
            $snapshot = $reference->getSnapshot();
            return $snapshot->getValue() ?? [];
        } catch (Exception $e) {
            log_message('error', "Error obteniendo datos de {$path}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar conexión con Firebase
     */
    public function testConnection(): bool
    {
        if (!$this->isConnected) {
            return false;
        }

        try {
            $testRef = $this->database->getReference('connection_test');
            $testRef->set(['test' => date('Y-m-d H:i:s')]);
            $testRef->remove(); // Limpiar después del test
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error probando conexión Firebase: ' . $e->getMessage());
            $this->isConnected = false;
            return false;
        }
    }

    /**
     * Verificar si el servicio está conectado
     */
    public function isConnected(): bool
    {
        return $this->isConnected;
    }
}