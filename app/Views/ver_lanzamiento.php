<?= $this->include('layout/header') ?>

<h1 class="mb-4">Lecturas del Lanzamiento</h1>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Información del Lanzamiento</h5>
    </div>
    <div class="card-body">
        <?php if ($lanzamiento): ?>
            <div class="row">
                <div class="col-md-3">
                    <strong>ID:</strong> <?= $lanzamiento['id'] ?>
                </div>
                <div class="col-md-3">
                    <strong>Inicio:</strong> <?= $lanzamiento['fecha_hora_inicio'] ?>
                </div>
                <div class="col-md-3">
                    <strong>Fin:</strong> <?= $lanzamiento['fecha_hora_final'] ?? 'En progreso' ?>
                </div>
                <div class="col-md-3">
                    <strong>Lugar:</strong> <?= $lanzamiento['lugar_captura'] ?>
                </div>
                <div class="col-12 mt-2">
                    <strong>Descripción:</strong> <?= $lanzamiento['descripcion'] ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lecturas Asociadas (<?= count($lecturas) ?>)</h5>
        <a href="<?= base_url('export/pdf/' . $lanzamiento['id']) ?>" class="btn btn-warning">
            Exportar a PDF
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($lecturas)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Temp (°C)</th>
                            <th>Hum (%)</th>
                            <th>Presión</th>
                            <th>Alt.Abs</th>
                            <th>Alt.Rel</th>
                            <th>AQI</th>
                            <th>TVOC</th>
                            <th>eCO2</th>
                            <th>PM1</th>
                            <th>PM2.5</th>
                            <th>PM10</th>
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
                                <td><?= $lectura['altura_absoluta'] ?></td>
                                <td><?= $lectura['altura_relativa'] ?></td>
                                <td><?= $lectura['AQI'] ?></td>
                                <td><?= $lectura['TVOC'] ?></td>
                                <td><?= $lectura['eCO2'] ?></td>
                                <td><?= $lectura['PM1'] ?></td>
                                <td><?= $lectura['PM2_5'] ?></td>
                                <td><?= $lectura['PM10'] ?></td>
                                <td><?= $lectura['AX'] ?></td>
                                <td><?= $lectura['AY'] ?></td>
                                <td><?= $lectura['AZ'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No hay lecturas asociadas a este lanzamiento</p>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3">
    <a href="<?= base_url('lanzamientos') ?>" class="btn btn-secondary">Volver al Listado</a>
</div>

<?= $this->include('layout/footer') ?>