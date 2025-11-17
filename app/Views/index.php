<?= $this->include('layout/header') ?>

<h1 class="mb-4">Panel Principal - Monitoreo en Tiempo Real</h1>

<!-- Última Lectura -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Última Lectura</h5>
                <span class="badge bg-success" id="ultimaActualizacion">Actualizado: <?= date('H:i:s') ?></span>
            </div>
            <div class="card-body" id="ultimaLecturaContainer">
                <?php if ($ultimaLectura): ?>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <strong>Temperatura:</strong> <span id="temperatura"><?= $ultimaLectura['temperatura'] ?? 'N/A' ?></span>°C
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>Humedad:</strong> <span id="humedad"><?= $ultimaLectura['humedad'] ?? 'N/A' ?></span>%
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>Presión:</strong> <span id="presion"><?= $ultimaLectura['presion_atmosferica'] ?? 'N/A' ?></span> hPa
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>AQI:</strong> <span id="aqi"><?= $ultimaLectura['AQI'] ?? 'N/A' ?></span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>TVOC:</strong> <span id="tvoc"><?= $ultimaLectura['TVOC'] ?? 'N/A' ?></span> ppb
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>eCO2:</strong> <span id="eco2"><?= $ultimaLectura['eCO2'] ?? 'N/A' ?></span> ppm
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No hay lecturas disponibles</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Gráficas -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Temperatura y Humedad</h5>
            </div>
            <div class="card-body">
                <canvas id="graficaTemperaturaHumedad" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Calidad del Aire</h5>
            </div>
            <div class="card-body">
                <canvas id="graficaCalidadAire" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Aceleración (AX, AY, AZ)</h5>
            </div>
            <div class="card-body">
                <canvas id="graficaAceleracion" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Giroscopio (GX, GY, GZ)</h5>
            </div>
            <div class="card-body">
                <canvas id="graficaGiroscopio" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Formulario Lanzamiento -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Gestión de Lanzamientos</h5>
            </div>
            <div class="card-body">
                <?php if (!$lanzamientoActivo): ?>
                    <form action="<?= base_url('lanzamiento/iniciar') ?>" method="post">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción *</label>
                                    <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="lugar_captura" class="form-label">Lugar de Captura *</label>
                                    <input type="text" class="form-control" id="lugar_captura" name="lugar_captura" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Iniciar Lanzamiento</button>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <h6>Lanzamiento Activo</h6>
                        <p><strong>Iniciado:</strong> <?= $lanzamientoActivo['fecha_hora_inicio'] ?></p>
                        <p><strong>Descripción:</strong> <?= $lanzamientoActivo['descripcion'] ?></p>
                        <p><strong>Lugar:</strong> <?= $lanzamientoActivo['lugar_captura'] ?></p>
                        <form action="<?= base_url('lanzamiento/finalizar') ?>" method="post" class="mt-3">
                            <input type="hidden" name="id_lanzamiento" value="<?= $lanzamientoActivo['id'] ?>">
                            <button type="submit" class="btn btn-danger">Finalizar Lanzamiento</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Configuración de gráficas
const graficaTH = new Chart(document.getElementById('graficaTemperaturaHumedad'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'Temperatura (°C)',
                borderColor: 'rgb(255, 99, 132)',
                data: []
            },
            {
                label: 'Humedad (%)',
                borderColor: 'rgb(54, 162, 235)',
                data: []
            }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const graficaCA = new Chart(document.getElementById('graficaCalidadAire'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'AQI',
                borderColor: 'rgb(255, 159, 64)',
                data: []
            },
            {
                label: 'TVOC (ppb)',
                borderColor: 'rgb(75, 192, 192)',
                data: []
            },
            {
                label: 'eCO2 (ppm)',
                borderColor: 'rgb(153, 102, 255)',
                data: []
            }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const graficaAcel = new Chart(document.getElementById('graficaAceleracion'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            { label: 'AX', borderColor: 'rgb(255, 99, 132)', data: [] },
            { label: 'AY', borderColor: 'rgb(54, 162, 235)', data: [] },
            { label: 'AZ', borderColor: 'rgb(75, 192, 192)', data: [] }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const graficaGiro = new Chart(document.getElementById('graficaGiroscopio'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            { label: 'GX', borderColor: 'rgb(255, 99, 132)', data: [] },
            { label: 'GY', borderColor: 'rgb(54, 162, 235)', data: [] },
            { label: 'GZ', borderColor: 'rgb(75, 192, 192)', data: [] }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Actualización en tiempo real
function actualizarDatos() {
    fetch('<?= base_url('lecturas/ultima') ?>')
        .then(response => response.json())
        .then(data => {
            if (data) {
                document.getElementById('temperatura').textContent = data.temperatura || 'N/A';
                document.getElementById('humedad').textContent = data.humedad || 'N/A';
                document.getElementById('presion').textContent = data.presion_atmosferica || 'N/A';
                document.getElementById('aqi').textContent = data.AQI || 'N/A';
                document.getElementById('tvoc').textContent = data.TVOC || 'N/A';
                document.getElementById('eco2').textContent = data.eCO2 || 'N/A';
                document.getElementById('ultimaActualizacion').textContent = 'Actualizado: ' + new Date().toLocaleTimeString();
            }
        });

    fetch('<?= base_url('lecturas/recientes') ?>')
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                const labels = data.map(lectura => new Date(lectura.fecha_hora).toLocaleTimeString()).reverse();
                
                // Temperatura y Humedad
                graficaTH.data.labels = labels;
                graficaTH.data.datasets[0].data = data.map(lectura => lectura.temperatura).reverse();
                graficaTH.data.datasets[1].data = data.map(lectura => lectura.humedad).reverse();
                graficaTH.update();
                
                // Calidad del Aire
                graficaCA.data.labels = labels;
                graficaCA.data.datasets[0].data = data.map(lectura => lectura.AQI).reverse();
                graficaCA.data.datasets[1].data = data.map(lectura => lectura.TVOC).reverse();
                graficaCA.data.datasets[2].data = data.map(lectura => lectura.eCO2).reverse();
                graficaCA.update();
                
                // Aceleración
                graficaAcel.data.labels = labels;
                graficaAcel.data.datasets[0].data = data.map(lectura => lectura.AX).reverse();
                graficaAcel.data.datasets[1].data = data.map(lectura => lectura.AY).reverse();
                graficaAcel.data.datasets[2].data = data.map(lectura => lectura.AZ).reverse();
                graficaAcel.update();
                
                // Giroscopio
                graficaGiro.data.labels = labels;
                graficaGiro.data.datasets[0].data = data.map(lectura => lectura.GX).reverse();
                graficaGiro.data.datasets[1].data = data.map(lectura => lectura.GY).reverse();
                graficaGiro.data.datasets[2].data = data.map(lectura => lectura.GZ).reverse();
                graficaGiro.update();
            }
        });
}

// Actualizar cada 5 segundos
setInterval(actualizarDatos, 5000);
actualizarDatos(); // Ejecutar inmediatamente
</script>

<?= $this->include('layout/footer') ?>