<?php

namespace App\Controllers;

use App\Models\LanzamientoModel;
use App\Models\ConexionModel;

class ExportController extends BaseController
{
    protected $lanzamientoModel;
    protected $conexionModel;

    public function __construct()
    {
        $this->lanzamientoModel = new LanzamientoModel();
        $this->conexionModel = new ConexionModel();
    }

    public function pdfLanzamiento($id)
    {
        $lecturas = $this->conexionModel->getLecturasPorLanzamiento($id);
        $lanzamiento = $this->lanzamientoModel->find($id);

        $html = view('pdf/lanzamiento', [
            'lanzamiento' => $lanzamiento,
            'lecturas' => $lecturas
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Mismo formato que múltiples lanzamientos
        $filename = "lanzamiento_{$id}_" . date('Y-m-d_H-i-s') . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
    }

    public function pdfMultiplesLanzamientos()
    {
        $ids = $this->request->getPost('lanzamientos');
        
        if (empty($ids)) {
            return redirect()->to('/lanzamientos')->with('error', 'No se seleccionaron lanzamientos');
        }

        $lanzamientos = [];
        foreach ($ids as $id) {
            $lanzamiento = $this->lanzamientoModel->find($id);
            if ($lanzamiento) {
                $lanzamiento['lecturas'] = $this->conexionModel->getLecturasPorLanzamiento($id);
                $lanzamientos[] = $lanzamiento;
            }
        }

        // ✅ ORDENAR POR ID (numérico) en lugar de por fecha
        usort($lanzamientos, function($a, $b) {
            return $a['id'] - $b['id']; // Orden ascendente por ID
        });

        $html = view('pdf/multiples_lanzamientos', ['lanzamientos' => $lanzamientos]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = "multiples_lanzamientos_" . date('Y-m-d_H-i-s') . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
    }
}