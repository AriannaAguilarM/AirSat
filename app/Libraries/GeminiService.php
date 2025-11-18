<?php

namespace App\Libraries;

use Exception;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    protected $conexionModel;

    public function __construct()
    {
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
        if (empty($this->apiKey)) {
            throw new Exception('API Key de Gemini no configurada');
        }
        $this->conexionModel = new \App\Models\ConexionModel();
    }

    public function analizarLanzamiento($lanzamiento, $lecturas)
    {
        $prompt = $this->construirPromptCompleto($lanzamiento, $lecturas);
        
        try {
            $analisis = $this->llamarGemini($prompt);
            $analisisLimpio = $this->limpiarFormato($analisis);
            $this->guardarAnalisisEnBD($lanzamiento['id'], $analisisLimpio);
            
            return $analisisLimpio;
            
        } catch (Exception $e) {
            throw new Exception('Error analizando con Gemini: ' . $e->getMessage());
        }
    }

    private function construirPromptCompleto($lanzamiento, $lecturas)
    {
        $infoLanzamiento = $this->formatearInfoLanzamiento($lanzamiento);
        $estadisticas = $this->calcularEstadisticasCompletas($lecturas);

        return "Eres un experto en análisis ambiental. Analiza estos datos de monitoreo ambiental:

{$infoLanzamiento}

## DATOS ESTADÍSTICOS:
{$estadisticas['texto']}

Proporciona un análisis profesional que incluide:

1. RESUMEN EJECUTIVO
   - Contexto general del monitoreo
   - Hallazgos principales

2. ANÁLISIS POR CATEGORÍA
   - Condiciones térmicas
   - Calidad del aire
   - Condiciones de presión y altitud
   - Datos de movimiento

3. EVALUACIÓN DE RIESGOS
   - Niveles de riesgo por categoría
   - Factores críticos detectados

4. RECOMENDACIONES ESPECÍFICAS
   - Acciones inmediatas
   - Recomendaciones preventivas

5. TENDENCIAS Y PATRONES
   - Comportamiento temporal
   - Correlaciones identificadas

IMPORTANTE: Usa un formato limpio sin emojis, sin markdown, sin símbolos especiales. Usa solo texto plano con saltos de línea.";
    }

    private function formatearInfoLanzamiento($lanzamiento)
    {
        $lecturas = $this->conexionModel->getLecturasPorLanzamiento($lanzamiento['id']);
        $totalLecturas = $lecturas ? count($lecturas) : 0;

        return "INFORMACIÓN DEL LANZAMIENTO
ID: {$lanzamiento['id']}
Descripción: {$lanzamiento['descripcion']}
Lugar: {$lanzamiento['lugar_captura']}
Fecha/Hora Inicio: {$lanzamiento['fecha_hora_inicio']}
Fecha/Hora Fin: " . ($lanzamiento['fecha_hora_final'] ?? 'En progreso') . "
Duración: " . $this->calcularDuracion($lanzamiento) . "
Total de lecturas: {$totalLecturas}";
    }

    private function calcularEstadisticasCompletas($lecturas)
    {
        if (empty($lecturas)) {
            return ['total_lecturas' => 0, 'texto' => 'No hay lecturas'];
        }

        $temperaturas = array_column($lecturas, 'temperatura');
        $humedades = array_column($lecturas, 'humedad');
        $presiones = array_column($lecturas, 'presion_atmosferica');
        $aqis = array_column($lecturas, 'AQI');
        $pm25s = array_column($lecturas, 'PM2_5');
        $pm10s = array_column($lecturas, 'PM10');
        $tvocs = array_column($lecturas, 'TVOC');
        $eco2s = array_column($lecturas, 'eCO2');

        $texto = "Total de lecturas analizadas: " . count($lecturas) . "\n";
        $texto .= "Temperatura: Mín " . min($temperaturas) . "°C, Máx " . max($temperaturas) . "°C, Prom " . round(array_sum($temperaturas) / count($temperaturas), 2) . "°C\n";
        $texto .= "Humedad: Mín " . min($humedades) . "%, Máx " . max($humedades) . "%, Prom " . round(array_sum($humedades) / count($humedades), 2) . "%\n";
        $texto .= "AQI: Mín " . min($aqis) . ", Máx " . max($aqis) . ", Prom " . round(array_sum($aqis) / count($aqis), 2) . "\n";
        $texto .= "PM2.5: Mín " . min($pm25s) . " μg/m³, Máx " . max($pm25s) . " μg/m³\n";
        $texto .= "PM10: Mín " . min($pm10s) . " μg/m³, Máx " . max($pm10s) . " μg/m³\n";
        $texto .= "TVOC: Mín " . min($tvocs) . " ppb, Máx " . max($tvocs) . " ppb\n";
        $texto .= "eCO2: Mín " . min($eco2s) . " ppm, Máx " . max($eco2s) . " ppm";

        return [
            'total_lecturas' => count($lecturas),
            'texto' => $texto
        ];
    }

    private function llamarGemini($prompt)
    {
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 4000,
            ]
        ];

        $url = $this->baseUrl . '?key=' . $this->apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Error en API Gemini: HTTP ' . $httpCode);
        }

        $result = json_decode($response, true);

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        } else {
            throw new Exception('Respuesta inesperada de Gemini');
        }
    }

    private function limpiarFormato($texto)
    {
        $limpiar = $texto;
        
        $limpiar = preg_replace('/[\\*#_{}()\\[\\]]/', '', $limpiar);
        
        $limpiar = preg_replace('/🎯|📊|⚠|🚀|🔍|📈|🛠|🛡|✅|❌|🌡|💧|🌫|🧪|🔥|🍃|👥|🏠|📞|💡|📍|📖|🎯|🤖/u', '', $limpiar);
        
        $limpiar = str_replace(['', ''], '', $limpiar);
        
        $limpiar = preg_replace('/\n\s*\n\s*\n/', "\n\n", $limpiar);
        
        $limpiar = trim($limpiar);
        
        return $limpiar;
    }

    private function calcularDuracion($lanzamiento)
    {
        if (!$lanzamiento['fecha_hora_final']) {
            return 'En progreso';
        }

        $inicio = new \DateTime($lanzamiento['fecha_hora_inicio']);
        $fin = new \DateTime($lanzamiento['fecha_hora_final']);
        $diferencia = $inicio->diff($fin);

        return $diferencia->format('%H:%I:%S');
    }

    private function guardarAnalisisEnBD($idLanzamiento, $analisis)
    {
        $db = \Config\Database::connect();
        
        $existe = $db->table('analisis_ia')
                    ->where('id_lanzamiento', $idLanzamiento)
                    ->countAllResults();

        $data = [
            'id_lanzamiento' => $idLanzamiento,
            'analisis_texto' => $analisis,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'modelo_utilizado' => 'gemini-pro'
        ];

        if ($existe > 0) {
            $db->table('analisis_ia')
               ->where('id_lanzamiento', $idLanzamiento)
               ->update($data);
        } else {
            $db->table('analisis_ia')->insert($data);
        }
    }

    public function obtenerAnalisisExistente($idLanzamiento)
    {
        $db = \Config\Database::connect();
        
        $analisis = $db->table('analisis_ia')
                      ->where('id_lanzamiento', $idLanzamiento)
                      ->get()
                      ->getRowArray();

        return $analisis ? $analisis['analisis_texto'] : null;
    }
}