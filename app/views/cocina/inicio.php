<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Vista Cocina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-warning-subtle">
    <div class="container mt-5">
        <h1 class="text-warning">Panel de Cocina</h1>
        <p>Preparación y seguimiento de pedidos.</p>
        <form action="<?= BASE_URL ?>/cocina/logout" method="post">
            <button type="submit" class="btn btn-outline-dark">Cerrar sesión</button>
        </form>
    </div>
</body>

</html>