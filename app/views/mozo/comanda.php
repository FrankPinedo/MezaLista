<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MesaLista - Comanda Mesa <?= htmlspecialchars($mesa) ?></title>

    <!-- Logo -->
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
        crossorigin="anonymous" />

    <!-- Estilos -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .producto-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .producto-card.disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .producto-card.disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .comentario-btn, 
        .comentario-plato-btn,
        .eliminar-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tab-content {
            min-height: 60vh;
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>

    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Header con información general y campana -->
            <header class="col-12 bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Mesa <?= htmlspecialchars($mesa) ?></h4>
                    <small>Mozo: <?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></small>
                </div>
                <div>
                    <i class="bi bi-bell fs-4"></i>
                </div>
            </header>

            <!-- Contenido principal -->
            <div class="col-12">
                <div class="row">
                    <!-- Sección Comanda (izquierda) -->
                    <div class="col-md-4 p-2 border-end">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">COMANDA</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 70vh;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Cant</th>
                                                <th>Descripción</th>
                                                <th>Precio</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="comanda-items">
                                            <?php if (!empty($detalles)): ?>
                                                <?php foreach ($detalles as $detalle): ?>
                                                    <tr>
                                                        <td><?= $detalle['cantidad'] ?></td>
                                                        <td>
                                                            <?= htmlspecialchars($detalle['nombre']) ?>
                                                            <?php if (!empty($detalle['comentario'])): ?>
                                                                <small class="text-muted d-block"><?= htmlspecialchars($detalle['comentario']) ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>S/ <?= number_format($detalle['precio'] * $detalle['cantidad'], 2) ?></td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class="btn btn-sm btn-outline-secondary comentario-btn" 
                                                                    data-id-detalle="<?= $detalle['id_detalle'] ?>"
                                                                    data-comentario="<?= htmlspecialchars($detalle['comentario'] ?? '') ?>">
                                                                    <i class="bi bi-chat-left-text"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger eliminar-btn" 
                                                                    data-id-detalle="<?= $detalle['id_detalle'] ?>">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-3">No hay items en la comanda</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Total a pagar:</h5>
                                        <h5 class="mb-0" id="total-comanda">S/ <?= number_format($total, 2) ?></h5>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button id="btn-salir" class="btn btn-secondary">Salir</button>
                                        <button id="btn-aceptar" class="btn btn-success">Aceptar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Productos (derecha) -->
                    <div class="col-md-8 p-2">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">PRODUCTOS</h5>
                            </div>
                            <div class="card-body p-0">
                                <!-- Filtros de productos -->
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-comida-tab" data-bs-toggle="tab" data-bs-target="#nav-comida" type="button" role="tab" aria-controls="nav-comida" aria-selected="true">Comida</button>
                                    <button class="nav-link" id="nav-bebidas-tab" data-bs-toggle="tab" data-bs-target="#nav-bebidas" type="button" role="tab" aria-controls="nav-bebidas" aria-selected="false">Bebidas</button>
                                    <button class="nav-link" id="nav-combos-tab" data-bs-toggle="tab" data-bs-target="#nav-combos" type="button" role="tab" aria-controls="nav-combos" aria-selected="false">Combos</button>
                                </div>
                                
                                <!-- Contenido de las pestañas -->
                                <div class="tab-content" id="nav-tabContent">
                                    <!-- Tab Comida -->
                                    <div class="tab-pane fade show active" id="nav-comida" role="tabpanel" aria-labelledby="nav-comida-tab">
                                        <div class="row row-cols-1 row-cols-md-3 g-3 p-3">
                                            <?php if (!empty($platos)): ?>
                                                <?php foreach ($platos as $plato): ?>
                                                    <div class="col">
                                                        <div class="card h-100 producto-card <?= $plato['estado'] !== 'activo' ? 'disabled bg-light' : '' ?>" 
                                                             data-id-plato="<?= $plato['id_plato'] ?>" 
                                                             data-precio="<?= $plato['precio'] ?>" 
                                                             data-nombre="<?= htmlspecialchars($plato['nombre']) ?>"
                                                             data-disponible="<?= $plato['estado'] === 'activo' ? '1' : '0' ?>">
                                                            <div class="card-img-top text-center pt-2">
                                                                <?php if (!empty($plato['imagen'])): ?>
                                                                    <img src="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($plato['imagen']) ?>" alt="<?= htmlspecialchars($plato['nombre']) ?>" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <div class="bg-light p-4 rounded">
                                                                        <i class="bi bi-image text-secondary" style="font-size: 3rem;"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6 class="card-title"><?= htmlspecialchars($plato['nombre']) ?></h6>
                                                                <p class="card-text small">
                                                                    <?php if (!empty($plato['descripcion'])): ?>
                                                                        <?= htmlspecialchars($plato['descripcion']) ?>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">Sin descripción</span>
                                                                    <?php endif; ?>
                                                                </p>
                                                            </div>
                                                            <div class="card-footer d-flex justify-content-between align-items-center">
                                                                <span class="fw-bold">S/ <?= number_format($plato['precio'], 2) ?></span>
                                                                <div>
                                                                    <span class="badge <?= $plato['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                                        Stock: <?= $plato['stock'] ?>
                                                                    </span>
                                                                    <?php if ($plato['estado'] === 'activo' && $plato['stock'] > 0): ?>
                                                                        <button class="btn btn-sm btn-outline-primary comentario-plato-btn" data-id-plato="<?= $plato['id_plato'] ?>">
                                                                            <i class="bi bi-chat-left-text"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="col-12 text-center py-5">
                                                    <p class="text-muted">No hay platos disponibles</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Tab Bebidas -->
                                    <div class="tab-pane fade" id="nav-bebidas" role="tabpanel" aria-labelledby="nav-bebidas-tab">
                                        <div class="row row-cols-1 row-cols-md-3 g-3 p-3">
                                            <?php if (!empty($bebidas)): ?>
                                                <?php foreach ($bebidas as $bebida): ?>
                                                    <div class="col">
                                                        <div class="card h-100 producto-card <?= $bebida['estado'] !== 'activo' ? 'disabled bg-light' : '' ?>" 
                                                             data-id-plato="<?= $bebida['id_plato'] ?>" 
                                                             data-precio="<?= $bebida['precio'] ?>" 
                                                             data-nombre="<?= htmlspecialchars($bebida['nombre']) ?>"
                                                             data-disponible="<?= $bebida['estado'] === 'activo' ? '1' : '0' ?>">
                                                            <div class="card-img-top text-center pt-2">
                                                                <?php if (!empty($bebida['imagen'])): ?>
                                                                    <img src="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($bebida['imagen']) ?>" alt="<?= htmlspecialchars($bebida['nombre']) ?>" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <div class="bg-light p-4 rounded">
                                                                        <i class="bi bi-cup-straw text-secondary" style="font-size: 3rem;"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6 class="card-title"><?= htmlspecialchars($bebida['nombre']) ?></h6>
                                                                <p class="card-text small">
                                                                    <?php if (!empty($bebida['descripcion'])): ?>
                                                                        <?= htmlspecialchars($bebida['descripcion']) ?>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">Sin descripción</span>
                                                                    <?php endif; ?>
                                                                </p>
                                                            </div>
                                                            <div class="card-footer d-flex justify-content-between align-items-center">
                                                                <span class="fw-bold">S/ <?= number_format($bebida['precio'], 2) ?></span>
                                                                <div>
                                                                    <span class="badge <?= $bebida['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                                        Stock: <?= $bebida['stock'] ?>
                                                                    </span>
                                                                    <?php if ($bebida['estado'] === 'activo' && $bebida['stock'] > 0): ?>
                                                                        <button class="btn btn-sm btn-outline-primary comentario-plato-btn" data-id-plato="<?= $bebida['id_plato'] ?>">
                                                                            <i class="bi bi-chat-left-text"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="col-12 text-center py-5">
                                                    <p class="text-muted">No hay bebidas disponibles</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Tab Combos -->
                                    <div class="tab-pane fade" id="nav-combos" role="tabpanel" aria-labelledby="nav-combos-tab">
                                        <div class="row row-cols-1 row-cols-md-3 g-3 p-3">
                                            <?php if (!empty($combos)): ?>
                                                <?php foreach ($combos as $combo): ?>
                                                    <div class="col">
                                                        <div class="card h-100 producto-card <?= $combo['estado'] !== 'activo' ? 'disabled bg-light' : '' ?>" 
                                                             data-id-plato="<?= $combo['id_plato'] ?>" 
                                                             data-precio="<?= $combo['precio'] ?>" 
                                                             data-nombre="<?= htmlspecialchars($combo['nombre']) ?>"
                                                             data-disponible="<?= $combo['estado'] === 'activo' ? '1' : '0' ?>">
                                                            <div class="card-img-top text-center pt-2">
                                                                <?php if (!empty($combo['imagen'])): ?>
                                                                    <img src="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($combo['imagen']) ?>" alt="<?= htmlspecialchars($combo['nombre']) ?>" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <div class="bg-light p-4 rounded">
                                                                        <i class="bi bi-box text-secondary" style="font-size: 3rem;"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6 class="card-title"><?= htmlspecialchars($combo['nombre']) ?></h6>
                                                                <p class="card-text small">
                                                                    <?php if (!empty($combo['descripcion'])): ?>
                                                                        <?= htmlspecialchars($combo['descripcion']) ?>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">Sin descripción</span>
                                                                    <?php endif; ?>
                                                                </p>
                                                            </div>
                                                            <div class="card-footer d-flex justify-content-between align-items-center">
                                                                <span class="fw-bold">S/ <?= number_format($combo['precio'], 2) ?></span>
                                                                <div>
                                                                    <span class="badge <?= $combo['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                                        Stock: <?= $combo['stock'] ?>
                                                                    </span>
                                                                    <?php if ($combo['estado'] === 'activo' && $combo['stock'] > 0): ?>
                                                                        <button class="btn btn-sm btn-outline-primary comentario-plato-btn" data-id-plato="<?= $combo['id_plato'] ?>">
                                                                            <i class="bi bi-chat-left-text"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="col-12 text-center py-5">
                                                    <p class="text-muted">No hay combos disponibles</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar comentarios -->
    <div class="modal fade" id="comentarioModal" tabindex="-1" aria-labelledby="comentarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="comentarioModalLabel">Agregar comentario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="comentarioForm">
                        <input type="hidden" id="id-detalle" name="id_detalle">
                        <input type="hidden" id="id-plato-nuevo" name="id_plato_nuevo">
                        <input type="hidden" id="modo" name="modo" value="editar">
                        <div class="mb-3">
                            <label for="comentario" class="form-label">Comentario</label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="3" placeholder="Ej: Sin ají, bien cocido, etc."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarComentario">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para enviar comanda -->
    <div class="modal fade" id="confirmarEnvioModal" tabindex="-1" aria-labelledby="confirmarEnvioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarEnvioModalLabel">Confirmar envío a cocina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas enviar esta comanda a cocina?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmarEnvio">Enviar a cocina</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="id-comanda" value="<?= $comanda['id_comanda'] ?>">

    <script>
        const BASE_URL = '<?= BASE_URL ?>';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/mozo/comanda.js"></script>
    <script> const BASE_URL = '<?= BASE_URL ?>'; </script>
</body>
</html>