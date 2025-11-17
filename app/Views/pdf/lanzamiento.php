<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lanzamiento <?= $lanzamiento['id'] ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 8px;
        }
        .table th, .table td { 
            border: 1px solid #ddd; 
            padding: 4px; 
            text-align: left; 
        }
        .table th { 
            background-color: #f2f2f2; 
            font-weight: bold;
        }
        .info-section { 
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
        }
        .page-break {
            page-break-after: always;
        }
        .section-title {
            background-color: #e9ecef;
            padding: 6px;
            margin: 10px 0;
            font-weight: bold;
        }
        .stats-table th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 16px;">Reporte de Lanzamiento - AirSat</h1>
        <h2 style="margin: 5px 0; font-size: 14px;">ID: <?= $lanzamiento['id'] ?></h2>
        <p style="margin: 0; font-size: 10px;">Generado: <?= date('Y-m-d H:i:s') ?></p>
    </div>

    <div class="info-section">
        <h3 style="margin: 0 0 8px 0; font-size: 12px;">Información del Lanzamiento</h3>
        <table style="width: 100%; font-size: 9px;">
            <tr>
                <td style="width: 25%;"><strong>ID:</strong> <?= $lanzamiento['id'] ?></td>
                <td style="width: 25%;"><strong>Inicio:</strong> <?= $lanzamiento['fecha_hora_inicio'] ?></td>
                <td style="width: 25%;"><strong>Fin:</strong> <?= $lanzamiento['fecha_hora_final'] ?? 'En progreso' ?></td>
                <td style="width: 25%;"><strong>Lecturas:</strong> <?= count($lecturas) ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Descripción:</strong> <?= $lanzamiento['descripcion'] ?></td>
                <td colspan="2"><strong>Lugar:</strong> <?= $lanzamiento['lugar_captura'] ?></td>
            </tr>
        </table>
    </div>

    <?php if (!empty($lecturas)): ?>
        <!-- Resumen Estadístico Completo -->
        <div class="section-title">Resumen Estadístico Completo</div>
        <table class="table stats-table">
            <thead>
                <tr>
                    <th>Parámetro</th>
                    <th>Unidad</th>
                    <th>Mínimo</th>
                    <th>Máximo</th>
                    <th>Promedio</th>
                    <th>Desviación Est.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Calcular estadísticas para todos los sensores ambientales
                $temperaturas = array_column($lecturas, 'temperatura');
                $humedades = array_column($lecturas, 'humedad');
                $presiones = array_column($lecturas, 'presion_atmosferica');
                $alturas_abs = array_column($lecturas, 'altura_absoluta');
                $alturas_rel = array_column($lecturas, 'altura_relativa');
                $aqis = array_column($lecturas, 'AQI');
                $tvocs = array_column($lecturas, 'TVOC');
                $eco2s = array_column($lecturas, 'eCO2');
                $pm1s = array_column($lecturas, 'PM1');
                $pm25s = array_column($lecturas, 'PM2_5');
                $pm10s = array_column($lecturas, 'PM10');
                $axs = array_column($lecturas, 'AX');
                $ays = array_column($lecturas, 'AY');
                $azs = array_column($lecturas, 'AZ');
                $gxs = array_column($lecturas, 'GX');
                $gys = array_column($lecturas, 'GY');
                $gzs = array_column($lecturas, 'GZ');

                // Función para calcular desviación estándar
                function calcularDesviacion($array) {
                    if (empty($array)) return 0;
                    $media = array_sum($array) / count($array);
                    $suma_cuadrados = 0;
                    foreach ($array as $valor) {
                        $suma_cuadrados += pow($valor - $media, 2);
                    }
                    return round(sqrt($suma_cuadrados / count($array)), 3);
                }
                ?>

                <!-- Sensores Ambientales -->
                <tr>
                    <td><strong>Temperatura</strong></td>
                    <td>°C</td>
                    <td><?= min($temperaturas) ?? 'N/A' ?></td>
                    <td><?= max($temperaturas) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($temperaturas) / count($temperaturas), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($temperaturas) ?></td>
                </tr>
                <tr>
                    <td><strong>Humedad</strong></td>
                    <td>%</td>
                    <td><?= min($humedades) ?? 'N/A' ?></td>
                    <td><?= max($humedades) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($humedades) / count($humedades), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($humedades) ?></td>
                </tr>
                <tr>
                    <td><strong>Presión Atmosférica</strong></td>
                    <td>hPa</td>
                    <td><?= min($presiones) ?? 'N/A' ?></td>
                    <td><?= max($presiones) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($presiones) / count($presiones), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($presiones) ?></td>
                </tr>
                <tr>
                    <td><strong>Altura Absoluta</strong></td>
                    <td>m</td>
                    <td><?= min($alturas_abs) ?? 'N/A' ?></td>
                    <td><?= max($alturas_abs) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($alturas_abs) / count($alturas_abs), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($alturas_abs) ?></td>
                </tr>
                <tr>
                    <td><strong>Altura Relativa</strong></td>
                    <td>m</td>
                    <td><?= min($alturas_rel) ?? 'N/A' ?></td>
                    <td><?= max($alturas_rel) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($alturas_rel) / count($alturas_rel), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($alturas_rel) ?></td>
                </tr>

                <!-- Calidad del Aire -->
                <tr>
                    <td><strong>Índice Calidad Aire (AQI)</strong></td>
                    <td>-</td>
                    <td><?= min($aqis) ?? 'N/A' ?></td>
                    <td><?= max($aqis) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($aqis) / count($aqis), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($aqis) ?></td>
                </tr>
                <tr>
                    <td><strong>Compuestos Orgánicos (TVOC)</strong></td>
                    <td>ppb</td>
                    <td><?= min($tvocs) ?? 'N/A' ?></td>
                    <td><?= max($tvocs) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($tvocs) / count($tvocs), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($tvocs) ?></td>
                </tr>
                <tr>
                    <td><strong>CO2 Equivalente (eCO2)</strong></td>
                    <td>ppm</td>
                    <td><?= min($eco2s) ?? 'N/A' ?></td>
                    <td><?= max($eco2s) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($eco2s) / count($eco2s), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($eco2s) ?></td>
                </tr>

                <!-- Partículas -->
                <tr>
                    <td><strong>Partículas PM1</strong></td>
                    <td>µg/m³</td>
                    <td><?= min($pm1s) ?? 'N/A' ?></td>
                    <td><?= max($pm1s) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($pm1s) / count($pm1s), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($pm1s) ?></td>
                </tr>
                <tr>
                    <td><strong>Partículas PM2.5</strong></td>
                    <td>µg/m³</td>
                    <td><?= min($pm25s) ?? 'N/A' ?></td>
                    <td><?= max($pm25s) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($pm25s) / count($pm25s), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($pm25s) ?></td>
                </tr>
                <tr>
                    <td><strong>Partículas PM10</strong></td>
                    <td>µg/m³</td>
                    <td><?= min($pm10s) ?? 'N/A' ?></td>
                    <td><?= max($pm10s) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($pm10s) / count($pm10s), 2) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($pm10s) ?></td>
                </tr>

                <!-- Aceleración -->
                <tr>
                    <td><strong>Aceleración X (AX)</strong></td>
                    <td>g</td>
                    <td><?= min($axs) ?? 'N/A' ?></td>
                    <td><?= max($axs) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($axs) / count($axs), 3) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($axs) ?></td>
                </tr>
                <tr>
                    <td><strong>Aceleración Y (AY)</strong></td>
                    <td>g</td>
                    <td><?= min($ays) ?? 'N/A' ?></td>
                    <td><?= max($ays) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($ays) / count($ays), 3) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($ays) ?></td>
                </tr>
                <tr>
                    <td><strong>Aceleración Z (AZ)</strong></td>
                    <td>g</td>
                    <td><?= min($azs) ?? 'N/A' ?></td>
                    <td><?= max($azs) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($azs) / count($azs), 3) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($azs) ?></td>
                </tr>

                <!-- Giroscopio -->
                <tr>
                    <td><strong>Giroscopio X (GX)</strong></td>
                    <td>°/s</td>
                    <td><?= min($gxs) ?? 'N/A' ?></td>
                    <td><?= max($gxs) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($gxs) / count($gxs), 3) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($gxs) ?></td>
                </tr>
                <tr>
                    <td><strong>Giroscopio Y (GY)</strong></td>
                    <td>°/s</td>
                    <td><?= min($gys) ?? 'N/A' ?></td>
                    <td><?= max($gys) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($gys) / count($gys), 3) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($gys) ?></td>
                </tr>
                <tr>
                    <td><strong>Giroscopio Z (GZ)</strong></td>
                    <td>°/s</td>
                    <td><?= min($gzs) ?? 'N/A' ?></td>
                    <td><?= max($gzs) ?? 'N/A' ?></td>
                    <td><?= round(array_sum($gzs) / count($gzs), 3) ?? 'N/A' ?></td>
                    <td><?= calcularDesviacion($gzs) ?></td>
                </tr>

            </tbody>
        </table>

        <div class="section-title">Datos de Sensores Ambientales</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Temp (°C)</th>
                    <th>Hum (%)</th>
                    <th>Presión (hPa)</th>
                    <th>Alt.Abs (m)</th>
                    <th>Alt.Rel (m)</th>
                    <th>AQI</th>
                    <th>TVOC (ppb)</th>
                    <th>eCO2 (ppm)</th>
                    <th>PM1 (µg/m³)</th>
                    <th>PM2.5 (µg/m³)</th>
                    <th>PM10 (µg/m³)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lecturas as $lectura): ?>
                    <tr>
                        <td><?= date('H:i:s', strtotime($lectura['fecha_hora'])) ?></td>
                        <td><?= $lectura['temperatura'] ?? 'N/A' ?></td>
                        <td><?= $lectura['humedad'] ?? 'N/A' ?></td>
                        <td><?= $lectura['presion_atmosferica'] ?? 'N/A' ?></td>
                        <td><?= $lectura['altura_absoluta'] ?? 'N/A' ?></td>
                        <td><?= $lectura['altura_relativa'] ?? 'N/A' ?></td>
                        <td><?= $lectura['AQI'] ?? 'N/A' ?></td>
                        <td><?= $lectura['TVOC'] ?? 'N/A' ?></td>
                        <td><?= $lectura['eCO2'] ?? 'N/A' ?></td>
                        <td><?= $lectura['PM1'] ?? 'N/A' ?></td>
                        <td><?= $lectura['PM2_5'] ?? 'N/A' ?></td>
                        <td><?= $lectura['PM10'] ?? 'N/A' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="section-title">Datos de Sensores de Movimiento</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>AX</th>
                    <th>AY</th>
                    <th>AZ</th>
                    <th>GX</th>
                    <th>GY</th>
                    <th>GZ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lecturas as $lectura): ?>
                    <tr>
                        <td><?= date('H:i:s', strtotime($lectura['fecha_hora'])) ?></td>
                        <td><?= $lectura['AX'] ?? 'N/A' ?></td>
                        <td><?= $lectura['AY'] ?? 'N/A' ?></td>
                        <td><?= $lectura['AZ'] ?? 'N/A' ?></td>
                        <td><?= $lectura['GX'] ?? 'N/A' ?></td>
                        <td><?= $lectura['GY'] ?? 'N/A' ?></td>
                        <td><?= $lectura['GZ'] ?? 'N/A' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div style="text-align: center; padding: 20px; color: #666;">
            <p>No hay lecturas asociadas a este lanzamiento</p>
        </div>
    <?php endif; ?>

    <div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 8px; text-align: center;">
        <p>Sistema AirSat - Reporte generado automáticamente</p>
    </div>
</body>
</html>