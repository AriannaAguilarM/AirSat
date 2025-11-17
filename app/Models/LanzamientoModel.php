<?php

namespace App\Models;

use CodeIgniter\Model;

class LanzamientoModel extends Model
{
    protected $table            = 'Lanzamiento';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['fecha_hora_inicio', 'fecha_hora_final', 'descripcion', 'lugar_captura'];

    public function getLanzamientosConLecturas()
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    public function iniciarLanzamiento($data)
    {
        return $this->insert($data);
    }

    public function finalizarLanzamiento($id, $fechaFinal)
    {
        return $this->update($id, ['fecha_hora_final' => $fechaFinal]);
    }

    public function getLanzamientoActivo()
    {
        return $this->where('fecha_hora_final IS NULL')->first();
    }
}