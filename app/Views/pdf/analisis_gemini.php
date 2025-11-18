<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Análisis Gemini - Lanzamiento <?= $lanzamiento['id'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .info-lanzamiento { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-left: 4px solid #007bff; }
        .analisis-content { white-space: pre-wrap; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Análisis IA Gemini - AirSat</h1>
        <h2>Lanzamiento #<?= $lanzamiento['id'] ?></h2>
        <p>Generado: <?= date('Y-m-d H:i:s') ?></p>
    </div>

    <div class="info-lanzamiento">
        <h3>Información del Lanzamiento</h3>
        <p><strong>Descripción:</strong> <?= $lanzamiento['descripcion'] ?></p>
        <p><strong>Lugar:</strong> <?= $lanzamiento['lugar_captura'] ?></p>
        <p><strong>Inicio:</strong> <?= $lanzamiento['fecha_hora_inicio'] ?></p>
        <p><strong>Fin:</strong> <?= $lanzamiento['fecha_hora_final'] ?? 'En progreso' ?></p>
    </div>

    <div class="analisis-content">
        <?= nl2br(htmlspecialchars($analisis)) ?>
    </div>

    <div style="margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; text-align: center; font-size: 10px;">
        <p>Generado automáticamente por AirSat con Google Gemini IA</p>
    </div>
</body>
</html>