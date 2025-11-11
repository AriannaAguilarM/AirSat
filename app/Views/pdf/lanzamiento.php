<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lanzamiento <?= $lanzamiento['id'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .info-section { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Lanzamiento</h1>
        <h3>ID: <?= $lanzamiento['id'] ?></h3>
    </div>

    <div class="info-section">
        <h4>Información del Lanzamiento</h4>
        <p><strong>Inicio:</strong> <?= $lanzamiento['fecha_hora_inicio'] ?></p>
        <p><strong>Fin:</strong> <?= $lanzamiento['fecha_hora_final'] ?? 'En progreso' ?></p>
        <p><strong>Descripción:</strong> <?= $lanzamiento['descripcion'] ?></p>
        <p><strong>Lugar:</strong> <?= $lanzamiento['lugar_captura'] ?></p>
    </div>

    <h4>Lecturas (<?= count($lecturas) ?>)</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha/Hora</th>
                <th>Temp</th>
                <th>Hum</th>
                <th>Presión</th>
                <th>AQI</th>
                <th>TVOC</th>
                <th>eCO2</th>
                <th>AX</th>
                <th>AY</th>
                <th>AZ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lecturas as $lectura): ?>
                <tr>
                    <td><?= $lectura['fecha_hora'] ?></td>
                    <td><?= $lectura['temperatura'] ?></td>
                    <td><?= $lectura['humedad'] ?></td>
                    <td><?= $lectura['presion_atmosferica'] ?></td>
                    <td><?= $lectura['AQI'] ?></td>
                    <td><?= $lectura['TVOC'] ?></td>
                    <td><?= $lectura['eCO2'] ?></td>
                    <td><?= $lectura['AX'] ?></td>
                    <td><?= $lectura['AY'] ?></td>
                    <td><?= $lectura['AZ'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>