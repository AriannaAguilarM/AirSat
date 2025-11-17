<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Firebase extends BaseConfig
{
    public string $apiKey;
    public string $projectId;
    public string $databaseUrl;
    public string $credentialsPath;

    public function __construct()
    {
        parent::__construct();

        // Cargar desde .env o usar valores por defecto
        $this->apiKey = $_ENV['FIREBASE_API_KEY'] ?? 'AIzaSyDnqLAeumgpAb9KAAzYwn12CYOkxUELtJ4';
        $this->projectId = $_ENV['FIREBASE_PROJECT_ID'] ?? 'airsat-ia-392e8';
        $this->databaseUrl = $_ENV['FIREBASE_DATABASE_URL'] ?? 'https://airsat-ia-392e8-default-rtdb.firebaseio.com';
        
        // ✅ USAR "writable" LITERAL en lugar de WRITEPATH
        $this->credentialsPath = $_ENV['FIREBASE_CREDENTIALS_PATH'] ?? 'writable/credentials/firebase_credentials.json';
        
        // Si la ruta es relativa, hacerla absoluta
        if (strpos($this->credentialsPath, 'writable/') === 0) {
            $this->credentialsPath = ROOTPATH . $this->credentialsPath;
        }
    }
}