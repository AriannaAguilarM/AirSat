<?php

namespace App\Controllers;

use App\Libraries\GeminiService;
use App\Models\LanzamientoModel;
use App\Models\ConexionModel;

class AnalisisGeminiController extends BaseController
{
    protected $geminiService;
    protected $lanzamientoModel;
    protected $conexionModel;

    public function __construct()
    {
        $this->geminiService = new GeminiService();
        $this->lanzamientoModel = new LanzamientoModel();
        $this->conexionModel = new ConexionModel();
    }

    public function index()
    {
        $lanzamientos = $this->lanzamientoModel->orderBy('id', 'DESC')->findAll();

        $data = [
            'titulo' => 'Análisis IA con Gemini - AirSat',
            'lanzamientos' => $lanzamientos
        ];

        return view('analisis_gemini/index', $data);
    }

    public function analizar($idLanzamiento)
    {
        $analisisExistente = $this->geminiService->obtenerAnalisisExistente($idLanzamiento);
        
        if ($analisisExistente) {
            return redirect()->to("/analisis-gemini/resultado/{$idLanzamiento}")
                           ->with('info', 'Este análisis ya fue generado anteriormente');
        }

        $lanzamiento = $this->lanzamientoModel->find($idLanzamiento);
        if (!$lanzamiento) {
            return redirect()->to('/analisis-gemini')->with('error', 'Lanzamiento no encontrado');
        }

        $lecturas = $this->conexionModel->getLecturasPorLanzamiento($idLanzamiento);
        if (empty($lecturas)) {
            return redirect()->to('/analisis-gemini')->with('error', 'No hay lecturas en este lanzamiento');
        }

        try {
            $analisis = $this->geminiService->analizarLanzamiento($lanzamiento, $lecturas);
            
            return redirect()->to("/analisis-gemini/resultado/{$idLanzamiento}")
                           ->with('success', 'Análisis generado exitosamente con Gemini AI');

        } catch (\Exception $e) {
            return redirect()->to('/analisis-gemini')
                           ->with('error', 'Error generando análisis: ' . $e->getMessage());
        }
    }

    public function resultado($idLanzamiento)
    {
        $lanzamiento = $this->lanzamientoModel->find($idLanzamiento);
        $analisis = $this->geminiService->obtenerAnalisisExistente($idLanzamiento);

        if (!$analisis) {
            return redirect()->to('/analisis-gemini')->with('error', 'No hay análisis generado para este lanzamiento');
        }

        $data = [
            'titulo' => 'Análisis Gemini - AirSat',
            'lanzamiento' => $lanzamiento,
            'analisis' => $analisis
        ];

        return view('analisis_gemini/resultado', $data);
    }

    public function exportarPdf($idLanzamiento)
    {
        $lanzamiento = $this->lanzamientoModel->find($idLanzamiento);
        $analisis = $this->geminiService->obtenerAnalisisExistente($idLanzamiento);

        if (!$analisis) {
            return redirect()->to('/analisis-gemini')->with('error', 'No hay análisis para exportar');
        }

        $html = view('pdf/analisis_gemini', [
            'lanzamiento' => $lanzamiento,
            'analisis' => $analisis
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "analisis_gemini_lanzamiento_{$idLanzamiento}_" . date('Y-m-d') . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
    }
}