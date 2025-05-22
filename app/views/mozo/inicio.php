<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'mozo') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Panel Mozo - MesaLista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/mozo/inicio/inicio.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<div class="d-flex vh-100">
    <!-- MENÚ LATERAL -->
    <div class="bg-dark text-white p-4 d-flex flex-column justify-content-between sidebar-mozo">
        <div>
            <h5 class="mb-4 text-center">MENÚ</h5>
            <div class="row row-cols-2 g-3 justify-content-center">
                <div class="col text-center">
                    <button class="menu-icon-btn">
                        <img src="<?= BASE_URL ?>/public/assets/img/comanda.png" alt="">
                        <span>Comanda</span>
                    </button>
                </div>
                <div class="col text-center">
                    <button class="menu-icon-btn">
                        <img src="<?= BASE_URL ?>/public/assets/img/CerrarCuenta.png" alt="">
                        <span>Cerrar Cuenta</span>
                    </button>
                </div>
                <div class="col text-center">
                    <button class="menu-icon-btn">
                        <img src="<?= BASE_URL ?>/public/assets/img/Recargar.png" alt="">
                        <span>Recargar</span>
                    </button>
                </div>
                <div class="col text-center">
                    <button class="menu-icon-btn">
                        <img src="<?= BASE_URL ?>/public/assets/img/Deliviry.png" alt="">
                        <span>Delivery</span>
                    </button>
                </div>
                <div class="col text-center">
                    <button class="menu-icon-btn">
                        <img src="<?= BASE_URL ?>/public/assets/img/Juntar.png" alt="">
                        <span>Juntar Mesas</span>
                    </button>
                </div>
                <div class="col text-center">
                    <button class="menu-icon-btn">
                        <img src="<?= BASE_URL ?>/public/assets/img/Agregar.png" alt="">
                        <span>Agregar Mesa</span>
                    </button>
                </div>
                <div class="col text-center">
                    <button class="menu-icon-btn">
                        <img src="<?= BASE_URL ?>/public/assets/img/Quitar.png" alt="">
                        <span>Quitar Mesa</span>
                    </button>
                </div>
                <div class="col text-center">
                    <button class="menu-icon-btn">
                        <img src="<?= BASE_URL ?>/public/assets/img/Separar.png" alt="">
                        <span>Separar Mesas</span>
                    </button>
                </div>
            </div>
        </div>

        <a href="<?= BASE_URL ?>/logout.php" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 mt-4">
                 Salir
                 </a>

    </div>

    <!-- PANEL DE MESAS -->
    <div class="flex-grow-1 p-4 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>MESAS</h4>
            <i class="bi bi-bell-fill fs-3 text-warning"></i>
        </div>

        <div class="row g-3" id="contenedorMesas">
            <div class="col-6 col-sm-4 col-md-3">
                <button class="mesa-btn mesa-reservado" data-mesa="M1">M1</button>
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <button class="mesa-btn mesa-esperando" data-mesa="M2">M2</button>
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <button class="mesa-btn mesa-pagando" data-mesa="M3">M3</button>
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <button class="mesa-btn mesa-libre" data-mesa="M4">M4</button>
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <button class="mesa-btn mesa-libre" data-mesa="M5">M5</button>
            </div>
            <div class="col-6 col-sm-6 col-md-4">
                <button class="mesa-btn mesa-combinada" data-mesa="C1">C1<br>(M6 + M7)</button>
            </div>
            <div class="col-6 col-sm-4 col-md-3">
                <button class="mesa-btn mesa-libre" data-mesa="M8">M8</button>
            </div>
        </div>

        <!-- LEYENDA -->
        <div class="mt-4 d-flex justify-content-center gap-4">
            <span><span class="badge bg-secondary">&nbsp;&nbsp;</span> Libre</span>
            <span><span class="badge bg-warning">&nbsp;&nbsp;</span> Reservado</span>
            <span><span class="badge bg-danger">&nbsp;&nbsp;</span> Esperando</span>
            <span><span class="badge bg-success">&nbsp;&nbsp;</span> Pagando</span>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/assets/js/mozo/panel.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


