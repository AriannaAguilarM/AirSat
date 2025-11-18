<?= $this->include('layout/header') ?>

<h1 class="mb-4">Análisis IA con Gemini</h1>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Seleccionar Lanzamiento para Análisis</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($lanzamientos)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Lugar</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lanzamientos as $lanzamiento): ?>
                            <tr>
                                <td><?= $lanzamiento['id'] ?></td>
                                <td><?= $lanzamiento['descripcion'] ?></td>
                                <td><?= $lanzamiento['lugar_captura'] ?></td>
                                <td><?= $lanzamiento['fecha_hora_inicio'] ?></td>
                                <td><?= $lanzamiento['fecha_hora_final'] ?? 'En progreso' ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url("analisis-gemini/analizar/{$lanzamiento['id']}") ?>" 
                                           class="btn btn-primary" 
                                           onclick="return confirm('¿Generar análisis IA con Gemini? Esto puede tomar unos segundos.')">
                                            <i class="fas fa-robot"></i> Analizar con IA
                                        </a>
                                        <a href="<?= base_url("analisis-gemini/resultado/{$lanzamiento['id']}") ?>" 
                                           class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No hay lanzamientos registrados</p>
        <?php endif; ?>
    </div>
</div>

<div class="alert alert-info mt-3">
    <h6>💡 ¿Qué hace el análisis con Gemini?</h6>
    <ul class="mb-0">
        <li>Analiza <strong>TODAS las lecturas</strong> del lanzamiento seleccionado</li>
        <li>Genera un reporte exhaustivo con Gemini IA</li>
        <li>Guarda el análisis para no tener que generarlo nuevamente</li>
        <li>Permite exportar a PDF para guardar en tu PC</li>
        <li>Incluye: Resumen ejecutivo, análisis por categoría, evaluación de riesgos y recomendaciones</li>
    </ul>
</div>

<?= $this->include('layout/footer') ?>