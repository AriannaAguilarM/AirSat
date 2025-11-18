<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'AirSat IA' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .nav-item {
            margin: 0 2px;
        }
        .nav-link {
            border-radius: 6px;
            transition: all 0.3s ease;
            padding: 8px 16px !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
        .nav-link i {
            width: 16px;
            text-align: center;
        }
        .navbar-nav {
            gap: 4px;
        }
        .alert {
            border: none;
            border-left: 4px solid;
        }
        .alert-success {
            border-left-color: #198754;
            background-color: #f8fff9;
        }
        .alert-danger {
            border-left-color: #dc3545;
            background-color: #fff8f8;
        }
        @media (max-width: 991.98px) {
            .navbar-nav {
                gap: 0;
            }
            .nav-link {
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url() ?>">
                <i class="fas fa-satellite me-2"></i>
                AirSat IA
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>">
                            <i class="fas fa-home"></i>
                            Panel Principal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('lanzamientos') ?>">
                            <i class="fas fa-rocket"></i>
                            Lanzamientos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('firebase-sync') ?>">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Sincronizar Firebase
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('analisis-gemini') ?>">
                            <i class="fas fa-robot"></i>
                            Análisis Gemini IA
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div><?= session()->getFlashdata('success') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>