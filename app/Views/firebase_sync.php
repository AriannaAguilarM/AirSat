<?= $this->include('layout/header') ?>

<h1 class="mb-4">Sincronización con Firebase</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sincronizar Datos Locales con Firebase</h5>
            </div>
            <div class="card-body">
                <!-- Información del proyecto -->
                <div class="alert alert-primary">
                    <h6>Proyecto Firebase: <strong><?= $config['projectId'] ?></strong></h6>
                    <p class="mb-0">URL: <?= $config['databaseUrl'] ?></p>
                </div>

                <div class="alert alert-info">
                    <h6>¿Qué hace la sincronización?</h6>
                    <ul class="mb-0">
                        <li>Sube los datos de MySQL a Firebase Realtime Database</li>
                        <li>Solo sincroniza registros nuevos (no duplica)</li>
                        <li>Mantiene la integridad de las relaciones</li>
                        <li>Estructura en Firebase: /airsat/{tabla}/{id}</li>
                    </ul>
                </div>

                <div id="statusAlert" style="display: none;"></div>

                <div class="d-flex gap-2">
                    <button type="button" id="btnSincronizar" class="btn btn-primary" <?= $estadoConexion ? '' : 'disabled' ?>>
                        <i class="fas fa-sync-alt"></i> Iniciar Sincronización
                    </button>
                    <button type="button" id="btnVerEstado" class="btn btn-outline-info">
                        <i class="fas fa-info-circle"></i> Ver Estado
                    </button>
                </div>

                <div id="resultados" class="mt-4" style="display: none;">
                    <h6>Resultados de la Sincronización:</h6>
                    <div id="resultadosDetalle"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Estado del Sistema</h5>
            </div>
            <div class="card-body">
                <div id="estadoConexion" class="mb-3">
                    <strong>Conexión Firebase:</strong>
                    <span id="estadoConexionText" class="badge bg-<?= $estadoConexion ? 'success' : 'danger' ?>">
                        <?= $estadoConexion ? 'Conectado' : 'Desconectado' ?>
                    </span>
                </div>

                <?php if (!$estadoConexion): ?>
                <div class="alert alert-warning small">
                    <strong>⚠ Configuración requerida:</strong>
                    <ol class="mb-0 mt-1">
                        <li>Descarga el archivo de credenciales desde Firebase Console</li>
                        <li>Guárdalo como: <code>writable/credentials/firebase_credentials.json</code></li>
                        <li>Verifica que las credenciales sean válidas</li>
                    </ol>
                </div>
                <?php endif; ?>

                <div id="estadoDatos">
                    <strong>Datos locales:</strong>
                    <div class="small mt-1">
                        <div>Lecturas: <span id="countLecturas">-</span></div>
                        <div>Lanzamientos: <span id="countLanzamientos">-</span></div>
                        <div>Conexiones: <span id="countConexiones">-</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Estructura Firebase</h5>
            </div>
            <div class="card-body">
                <div class="small">
                    <pre class="mb-0" style="font-size: 0.7rem;">
/airsat/
├── lecturas/
│   └── {id}
├── lanzamientos/
│   └── {id}
└── conexion/
    └── {id}
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSincronizar = document.getElementById('btnSincronizar');
    const btnVerEstado = document.getElementById('btnVerEstado');
    const statusAlert = document.getElementById('statusAlert');
    const resultados = document.getElementById('resultados');
    const resultadosDetalle = document.getElementById('resultadosDetalle');

    // Verificar estado inicial
    verificarEstado();

    // Evento para sincronizar
    btnSincronizar.addEventListener('click', function() {
        sincronizarDatos();
    });

    // Evento para ver estado
    btnVerEstado.addEventListener('click', function() {
        verificarEstado();
    });

    function sincronizarDatos() {
        btnSincronizar.disabled = true;
        btnSincronizar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sincronizando...';

        fetch('<?= base_url('firebase-sync/sync') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            mostrarResultado(data);
            btnSincronizar.disabled = false;
            btnSincronizar.innerHTML = '<i class="fas fa-sync-alt"></i> Iniciar Sincronización';
            
            // Actualizar estado después de sincronizar
            setTimeout(verificarEstado, 1000);
        })
        .catch(error => {
            mostrarAlerta('Error en la sincronización: ' + error.message, 'error');
            btnSincronizar.disabled = false;
            btnSincronizar.innerHTML = '<i class="fas fa-sync-alt"></i> Iniciar Sincronización';
        });
    }

    function verificarEstado() {
        fetch('<?= base_url('firebase-sync/status') ?>')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                actualizarEstadoUI(data.data);
            } else {
                mostrarAlerta('Error obteniendo estado: ' + (data.message || 'Desconocido'), 'error');
            }
        })
        .catch(error => {
            mostrarAlerta('Error conectando al servidor: ' + error.message, 'error');
        });
    }

    function actualizarEstadoUI(estado) {
        // Estado de conexión
        const conexionBadge = document.getElementById('estadoConexionText');
        if (estado.conexion) {
            conexionBadge.className = 'badge bg-success';
            conexionBadge.textContent = 'Conectado';
            btnSincronizar.disabled = false;
        } else {
            conexionBadge.className = 'badge bg-danger';
            conexionBadge.textContent = 'Desconectado';
            btnSincronizar.disabled = true;
        }

        // Contadores locales
        document.getElementById('countLecturas').textContent = estado.local.lecturas;
        document.getElementById('countLanzamientos').textContent = estado.local.lanzamientos;
        document.getElementById('countConexiones').textContent = estado.local.conexiones;
    }

    function mostrarResultado(data) {
        resultados.style.display = 'block';
        
        if (data.success) {
            const html = `
                <div class="alert alert-${data.type}">
                    <strong>${data.message}</strong>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5>${data.data.lecturas.nuevos}</h5>
                                <small>Nuevas Lecturas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5>${data.data.lanzamientos.nuevos}</h5>
                                <small>Nuevos Lanzamientos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5>${data.data.conexiones.nuevos}</h5>
                                <small>Nuevas Conexiones</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            resultadosDetalle.innerHTML = html;
        } else {
            resultadosDetalle.innerHTML = `
                <div class="alert alert-danger">
                    <strong>${data.message}</strong>
                </div>
            `;
        }
    }

    function mostrarAlerta(mensaje, tipo) {
        statusAlert.style.display = 'block';
        statusAlert.className = `alert alert-${tipo}`;
        statusAlert.innerHTML = mensaje;
        
        setTimeout(() => {
            statusAlert.style.display = 'none';
        }, 5000);
    }
});
</script>

<?= $this->include('layout/footer') ?>