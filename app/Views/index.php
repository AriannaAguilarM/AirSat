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
                            <strong>Altura Abs.:</strong> <span id="altura_absoluta"><?= $ultimaLectura['altura_absoluta'] ?? 'N/A' ?></span> m
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>Altura Rel.:</strong> <span id="altura_relativa"><?= $ultimaLectura['altura_relativa'] ?? 'N/A' ?></span> m
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
                        <!-- Partículas -->
                        <div class="col-md-3 mb-2">
                            <strong>PM1:</strong> <span id="pm1"><?= $ultimaLectura['PM1'] ?? 'N/A' ?></span> µg/m³
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>PM2.5:</strong> <span id="pm25"><?= $ultimaLectura['PM2_5'] ?? 'N/A' ?></span> µg/m³
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>PM10:</strong> <span id="pm10"><?= $ultimaLectura['PM10'] ?? 'N/A' ?></span> µg/m³
                        </div>
                        <!-- Aceleración -->
                        <div class="col-md-3 mb-2">
                            <strong>AX:</strong> <span id="ax"><?= $ultimaLectura['AX'] ?? 'N/A' ?></span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>AY:</strong> <span id="ay"><?= $ultimaLectura['AY'] ?? 'N/A' ?></span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>AZ:</strong> <span id="az"><?= $ultimaLectura['AZ'] ?? 'N/A' ?></span>
                        </div>
                        <!-- Giroscopio -->
                        <div class="col-md-3 mb-2">
                            <strong>GX:</strong> <span id="gx"><?= $ultimaLectura['GX'] ?? 'N/A' ?></span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>GY:</strong> <span id="gy"><?= $ultimaLectura['GY'] ?? 'N/A' ?></span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>GZ:</strong> <span id="gz"><?= $ultimaLectura['GZ'] ?? 'N/A' ?></span>
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
                <h5 class="mb-0">Partículas (PM1, PM2.5, PM10)</h5>
            </div>
            <div class="card-body">
                <canvas id="graficaParticulas" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Presión y Alturas</h5>
            </div>
            <div class="card-body">
                <canvas id="graficaPresionAlturas" height="250"></canvas>
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
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                data: []
            },
            {
                label: 'Humedad (%)',
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
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
                backgroundColor: 'rgba(255, 159, 64, 0.1)',
                data: []
            },
            {
                label: 'TVOC (ppb)',
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                data: []
            },
            {
                label: 'eCO2 (ppm)',
                borderColor: 'rgb(153, 102, 255)',
                backgroundColor: 'rgba(153, 102, 255, 0.1)',
                data: []
            }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const graficaParticulas = new Chart(document.getElementById('graficaParticulas'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'PM1 (µg/m³)',
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                data: []
            },
            {
                label: 'PM2.5 (µg/m³)',
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                data: []
            },
            {
                label: 'PM10 (µg/m³)',
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                data: []
            }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const graficaPresionAlturas = new Chart(document.getElementById('graficaPresionAlturas'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'Presión (hPa)',
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                data: [],
                yAxisID: 'y'
            },
            {
                label: 'Altura Abs. (m)',
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                data: [],
                yAxisID: 'y1'
            },
            {
                label: 'Altura Rel. (m)',
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                data: [],
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Presión (hPa)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Altura (m)'
                },
                grid: {
                    drawOnChartArea: false
                }
            }
        }
    }
});

const graficaAcel = new Chart(document.getElementById('graficaAceleracion'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            { 
                label: 'AX', 
                borderColor: 'rgb(255, 99, 132)', 
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                data: [] 
            },
            { 
                label: 'AY', 
                borderColor: 'rgb(54, 162, 235)', 
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                data: [] 
            },
            { 
                label: 'AZ', 
                borderColor: 'rgb(75, 192, 192)', 
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                data: [] 
            }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const graficaGiro = new Chart(document.getElementById('graficaGiroscopio'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            { 
                label: 'GX', 
                borderColor: 'rgb(255, 99, 132)', 
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                data: [] 
            },
            { 
                label: 'GY', 
                borderColor: 'rgb(54, 162, 235)', 
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                data: [] 
            },
            { 
                label: 'GZ', 
                borderColor: 'rgb(75, 192, 192)', 
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                data: [] 
            }
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
                document.getElementById('altura_absoluta').textContent = data.altura_absoluta || 'N/A';
                document.getElementById('altura_relativa').textContent = data.altura_relativa || 'N/A';
                document.getElementById('aqi').textContent = data.AQI || 'N/A';
                document.getElementById('tvoc').textContent = data.TVOC || 'N/A';
                document.getElementById('eco2').textContent = data.eCO2 || 'N/A';
                document.getElementById('pm1').textContent = data.PM1 || 'N/A';
                document.getElementById('pm25').textContent = data.PM2_5 || 'N/A';
                document.getElementById('pm10').textContent = data.PM10 || 'N/A';
                document.getElementById('ax').textContent = data.AX || 'N/A';
                document.getElementById('ay').textContent = data.AY || 'N/A';
                document.getElementById('az').textContent = data.AZ || 'N/A';
                document.getElementById('gx').textContent = data.GX || 'N/A';
                document.getElementById('gy').textContent = data.GY || 'N/A';
                document.getElementById('gz').textContent = data.GZ || 'N/A';
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
                
                // Partículas
                graficaParticulas.data.labels = labels;
                graficaParticulas.data.datasets[0].data = data.map(lectura => lectura.PM1).reverse();
                graficaParticulas.data.datasets[1].data = data.map(lectura => lectura.PM2_5).reverse();
                graficaParticulas.data.datasets[2].data = data.map(lectura => lectura.PM10).reverse();
                graficaParticulas.update();
                
                // Presión y Alturas
                graficaPresionAlturas.data.labels = labels;
                graficaPresionAlturas.data.datasets[0].data = data.map(lectura => lectura.presion_atmosferica).reverse();
                graficaPresionAlturas.data.datasets[1].data = data.map(lectura => lectura.altura_absoluta).reverse();
                graficaPresionAlturas.data.datasets[2].data = data.map(lectura => lectura.altura_relativa).reverse();
                graficaPresionAlturas.update();
                
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