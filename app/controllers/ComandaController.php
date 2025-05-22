<?php
class ComandaController
{
    private $usuario;
    private $comandaModel;
    private $platoModel;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        require_once __DIR__ . '/../models/ComandaModel.php';
        require_once __DIR__ . '/../models/PlatoModel.php';
        
        $this->usuario = verificarSesion();
        session_regenerate_id(true);
        
        if ($this->usuario['rol'] !== 'mozo') {
            require_once __DIR__ . '/ErrorController.php';
            (new ErrorController())->index('403');
            exit();
        }
        
        $this->comandaModel = new ComandaModel();
        $this->platoModel = new PlatoModel();
    }

    /**
     * Muestra la vista de comanda para una mesa específica
     */
    public function index($mesa = null)
    {
        if (!$mesa) {
            header('Location: ' . BASE_URL . '/mozo');
            exit();
        }

        // Verificar si la mesa tiene una comanda activa
        $comanda = $this->comandaModel->obtenerComandaActivaPorMesa($mesa);
        
        if (!$comanda) {
            // Si no existe una comanda activa, crear una nueva
            $id_comanda = $this->comandaModel->crearComanda($mesa, $this->usuario['id_user']);
            $comanda = [
                'id_comanda' => $id_comanda,
                'mesa' => $mesa,
                'estado' => 'pendiente'
            ];
        }
        
        // Obtener los platos según su tipo usando el PlatoModel existente
        $platos = $this->platoModel->obtenerPlatosPorTipo('comida');
        $bebidas = $this->platoModel->obtenerPlatosPorTipo('bebida');
        $combos = $this->platoModel->obtenerPlatosPorTipo('combo');
        
        // Obtener los detalles de la comanda
        $detalles = $this->comandaModel->obtenerDetallesComanda($comanda['id_comanda']);
        
        // Calcular el total
        $total = $this->comandaModel->obtenerTotalComanda($comanda['id_comanda']);

        // Pasar datos a la vista
        $usuario = $this->usuario;
        
        // Cargar la vista
        require_once __DIR__ . '/../views/mozo/comanda.php';
    }

    /**
     * Agrega un plato a la comanda (AJAX)
     */
    public function agregarPlato()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJson([
                'status' => 'error', 
                'message' => 'Método no permitido'
            ]);
        }
        
        $id_comanda = $_POST['id_comanda'] ?? null;
        $id_plato = $_POST['id_plato'] ?? null;
        $cantidad = intval($_POST['cantidad'] ?? 1);
        $comentario = $_POST['comentario'] ?? null;
        
        if (!$id_comanda || !$id_plato) {
            $this->responderJson([
                'status' => 'error', 
                'message' => 'Datos incompletos'
            ]);
        }
        
        try {
            // Verificar stock del plato
            $plato = $this->platoModel->obtenerPlatoPorId($id_plato);
            
            if (!$plato) {
                $this->responderJson([
                    'status' => 'error', 
                    'message' => 'El plato no existe'
                ]);
            }
            
            if ($plato['estado'] !== 'activo') {
                $this->responderJson([
                    'status' => 'error', 
                    'message' => 'El plato no está disponible'
                ]);
            }
            
            if ($plato['stock'] < $cantidad) {
                $this->responderJson([
                    'status' => 'error', 
                    'message' => 'Stock insuficiente'
                ]);
            }
            
            // Agregar el plato a la comanda
            $id_detalle = $this->comandaModel->agregarDetalleComanda($id_comanda, $id_plato, $cantidad, $comentario);
            
            // Actualizar el stock usando el método existente
            $nuevo_stock = $plato['stock'] - $cantidad;
            $this->platoModel->actualizarProducto(
                $id_plato, 
                $plato['nombre'], 
                $plato['precio'], 
                $nuevo_stock, 
                $plato['descripcion'], 
                $plato['estado'], 
                $plato['imagen']
            );
            
            // Obtener los detalles de la comanda actualizados
            $detalles = $this->comandaModel->obtenerDetallesComanda($id_comanda);
            $total = $this->comandaModel->obtenerTotalComanda($id_comanda);
            
            $this->responderJson([
                'status' => 'success',
                'message' => 'Plato agregado con éxito',
                'detalles' => $detalles,
                'total' => $total
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Agrega o actualiza un comentario a un plato (AJAX)
     */
    public function agregarComentario()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJson([
                'status' => 'error', 
                'message' => 'Método no permitido'
            ]);
        }
        
        $id_detalle = $_POST['id_detalle'] ?? null;
        $comentario = $_POST['comentario'] ?? null;
        
        if (!$id_detalle) {
            $this->responderJson([
                'status' => 'error', 
                'message' => 'Datos incompletos'
            ]);
        }
        
        try {
            $this->comandaModel->actualizarComentarioDetalle($id_detalle, $comentario);
            
            $this->responderJson([
                'status' => 'success',
                'message' => 'Comentario agregado con éxito'
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Elimina un plato de la comanda (AJAX)
     */
    public function eliminarPlato()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJson([
                'status' => 'error', 
                'message' => 'Método no permitido'
            ]);
        }
        
        $id_detalle = $_POST['id_detalle'] ?? null;
        $id_comanda = $_POST['id_comanda'] ?? null;
        
        if (!$id_detalle || !$id_comanda) {
            $this->responderJson([
                'status' => 'error', 
                'message' => 'Datos incompletos'
            ]);
        }
        
        try {
            // Obtener detalle antes de eliminar para devolver stock
            $detalles = $this->comandaModel->obtenerDetallesComanda($id_comanda);
            $detalle = null;
            
            foreach ($detalles as $d) {
                if ($d['id_detalle'] == $id_detalle) {
                    $detalle = $d;
                    break;
                }
            }
            
            if ($detalle) {
                // Restaurar stock
                $plato = $this->platoModel->obtenerPlatoPorId($detalle['id_plato']);
                $nuevo_stock = $plato['stock'] + $detalle['cantidad'];
                $this->platoModel->actualizarProducto(
                    $detalle['id_plato'], 
                    $plato['nombre'], 
                    $plato['precio'], 
                    $nuevo_stock, 
                    $plato['descripcion'], 
                    $plato['estado'], 
                    $plato['imagen']
                );
                
                // Eliminar detalle
                $this->comandaModel->eliminarDetalleComanda($id_detalle);
                
                // Obtener datos actualizados
                $detalles = $this->comandaModel->obtenerDetallesComanda($id_comanda);
                $total = $this->comandaModel->obtenerTotalComanda($id_comanda);
                
                $this->responderJson([
                    'status' => 'success',
                    'message' => 'Plato eliminado con éxito',
                    'detalles' => $detalles,
                    'total' => $total
                ]);
            } else {
                $this->responderJson([
                    'status' => 'error', 
                    'message' => 'El detalle no existe'
                ]);
            }
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envía la comanda a cocina (AJAX)
     */
    public function enviarComanda()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJson([
                'status' => 'error', 
                'message' => 'Método no permitido'
            ]);
        }
        
        $id_comanda = $_POST['id_comanda'] ?? null;
        
        if (!$id_comanda) {
            $this->responderJson([
                'status' => 'error', 
                'message' => 'Datos incompletos'
            ]);
        }
        
        try {
            $detalles = $this->comandaModel->obtenerDetallesComanda($id_comanda);
            
            if (empty($detalles)) {
                $this->responderJson([
                    'status' => 'error', 
                    'message' => 'La comanda no tiene productos'
                ]);
            }
            
            $this->comandaModel->actualizarEstadoComanda($id_comanda, 'en_cocina');
            
            $this->responderJson([
                'status' => 'success',
                'message' => 'Comanda enviada a cocina con éxito'
            ]);
        } catch (Exception $e) {
            $this->responderJson([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Método auxiliar para responder en formato JSON
     */
    private function responderJson($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}