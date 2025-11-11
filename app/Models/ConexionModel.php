<?php

namespace App\Models;

use CodeIgniter\Model;

class ConexionModel extends Model
{
    protected $table            = 'Conexion';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_lecturas', 'id_lanzamiento'];

    public function conectarLecturas($idLanzamiento, $idsLecturas)
    {
        $data = [];
        foreach ($idsLecturas as $idLectura) {
            $data[] = [
                'id_lecturas' => $idLectura,
                'id_lanzamiento' => $idLanzamiento
            ];
        }
        
        if (!empty($data)) {
            return $this->insertBatch($data);
        }
        return true;
    }

    public function getLecturasPorLanzamiento($idLanzamiento)
    {
        $builder = $this->db->table('Conexion c');
        $builder->select('l.*')
                ->join('Lecturas l', 'l.id = c.id_lecturas')
                ->where('c.id_lanzamiento', $idLanzamiento)
                ->orderBy('l.fecha_hora', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}