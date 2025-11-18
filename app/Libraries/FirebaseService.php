<?php

namespace App\Libraries;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Config\Firebase as FirebaseConfig;
use Exception;

class FirebaseService
{
    protected $database;
    protected $firebaseConfig;
    protected $isConnected = false;
    protected $lastError = '';

    public function __construct()
    {
        $this->firebaseConfig = new FirebaseConfig();
        $this->initializeFirebase();
    }

    private function initializeFirebase()
    {
        try {
            if (!file_exists($this->firebaseConfig->credentialsPath)) {
                throw new Exception('Archivo de credenciales no encontrado: ' . $this->firebaseConfig->credentialsPath);
            }

            $factory = (new Factory)
                ->withServiceAccount($this->firebaseConfig->credentialsPath)
                ->withDatabaseUri($this->firebaseConfig->databaseUrl);

            $this->database = $factory->createDatabase();
            $this->isConnected = true;
            
        } catch (Exception $e) {
            $this->isConnected = false;
            $this->lastError = $e->getMessage();
        }
    }

    public function getDatabase(): ?Database
    {
        return $this->isConnected ? $this->database : null;
    }

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
            return false;
        }
    }

    public function set(string $path, string $key, array $data): bool
    {
        if (!$this->isConnected) {
            return false;
        }

        try {
            $reference = $this->database->getReference($path . '/' . $key);
            $reference->set($data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

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
            return [];
        }
    }

    public function testConnection(): bool
    {
        if (!$this->isConnected) {
            return false;
        }

        try {
            $testRef = $this->database->getReference('connection_test');
            $testRef->set(['test' => date('Y-m-d H:i:s')]);
            $testRef->remove();
            return true;
        } catch (Exception $e) {
            $this->isConnected = false;
            return false;
        }
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function getLastError(): string
    {
        return $this->lastError;
    }

    public function sincronizarAnalisisIA($analisisData): bool
    {
        if (!$this->isConnected) {
            return false;
        }

        try {
            $path = 'airsat/analisis_ia';
            $key = $analisisData['id_lanzamiento'];
            
            if ($this->exists($path, $key)) {
                return true;
            }

            $dataFirebase = [
                'id_lanzamiento' => $analisisData['id_lanzamiento'],
                'analisis_texto' => $analisisData['analisis_texto'],
                'fecha_creacion' => $analisisData['fecha_creacion'],
                'modelo_utilizado' => $analisisData['modelo_utilizado'],
                'sync_timestamp' => date('Y-m-d H:i:s')
            ];

            return $this->set($path, $key, $dataFirebase);
            
        } catch (Exception $e) {
            return false;
        }
    }

    public function obtenerAnalisisFirebase($idLanzamiento): ?array
    {
        if (!$this->isConnected) {
            return null;
        }

        try {
            $path = 'airsat/analisis_ia/' . $idLanzamiento;
            $reference = $this->database->getReference($path);
            $snapshot = $reference->getSnapshot();
            
            return $snapshot->exists() ? $snapshot->getValue() : null;
            
        } catch (Exception $e) {
            return null;
        }
    }
}