<?= $this->include('layout/header') ?>

<h1 class="mb-4">Análisis Gemini IA</h1>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lanzamiento #<?= $lanzamiento['id'] ?> - <?= $lanzamiento['descripcion'] ?></h5>
                <div class="btn-group">
                    <a href="<?= base_url("analisis-gemini/exportar-pdf/{$lanzamiento['id']}") ?>" 
                       class="btn btn-success">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a href="<?= base_url('analisis-gemini') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="analisis-content" style="font-family: Arial, sans-serif; line-height: 1.6; white-space: pre-line; background: #f8f9fa; padding: 20px; border-radius: 5px;">
                    <?= htmlspecialchars($analisis) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layout/footer') ?>