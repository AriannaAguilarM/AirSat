<?= $this->include('layout/header') ?>

<h1 class="mb-4">Histórico de Lanzamientos</h1>

<form action="<?= base_url('export/multiples') ?>" method="post" id="formExportar">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista de Lanzamientos</h5>
            <button type="submit" class="btn btn-success" id="btnExportar" style="display: none;">
                Exportar Seleccionados a PDF
            </button>
        </div>
        <div class="card-body">
            <?php if (!empty($lanzamientos)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>ID</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Descripción</th>
                                <th>Lugar</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lanzamientos as $lanzamiento): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="lanzamientos[]" value="<?= $lanzamiento['id'] ?>" class="lanzamiento-checkbox">
                                    </td>
                                    <td><?= $lanzamiento['id'] ?></td>
                                    <td><?= $lanzamiento['fecha_hora_inicio'] ?></td>
                                    <td><?= $lanzamiento['fecha_hora_final'] ?? 'En progreso' ?></td>
                                    <td><?= $lanzamiento['descripcion'] ?></td>
                                    <td><?= $lanzamiento['lugar_captura'] ?></td>
                                    <td>
                                        <a href="<?= base_url('lanzamiento/ver/' . $lanzamiento['id']) ?>" class="btn btn-sm btn-info">
                                            Ver Lecturas
                                        </a>
                                        <a href="<?= base_url('export/pdf/' . $lanzamiento['id']) ?>" class="btn btn-sm btn-warning">
                                            Exportar PDF
                                        </a>
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
</form>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.lanzamiento-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    toggleExportButton();
});

document.querySelectorAll('.lanzamiento-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', toggleExportButton);
});

function toggleExportButton() {
    const checked = document.querySelectorAll('.lanzamiento-checkbox:checked').length > 0;
    document.getElementById('btnExportar').style.display = checked ? 'block' : 'none';
}
</script>

<?= $this->include('layout/footer') ?>