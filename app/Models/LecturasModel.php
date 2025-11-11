<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturasModel extends Model
{
    protected $table            = 'Lecturas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'temperatura', 'humedad', 'presion_atmosferica', 'altura_absoluta',
        'altura_relativa', 'AQI', 'TVOC', 'eCO2', 'PM1', 'PM2_5', 'PM10',
        'AX', 'AY', 'AZ', 'GX', 'GY', 'GZ', 'fecha_hora'
    ];

    protected bool $allowEmptyInserts = false;

    public function getUltimaLectura()
    {
        return $this->orderBy('fecha_hora', 'DESC')->first();
    }

    public function getLecturasRecientes($limite = 10)
    {
        return $this->orderBy('fecha_hora', 'DESC')->limit($limite)->find();
    }

    public function getLecturasPorRango($fechaInicio, $fechaFin)
    {
        return $this->where('fecha_hora >=', $fechaInicio)
                    ->where('fecha_hora <=', $fechaFin)
                    ->orderBy('fecha_hora', 'ASC')
                    ->findAll();
    }
}