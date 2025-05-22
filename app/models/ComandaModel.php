<?php
class ComandaModel
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        try {
            $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Obtiene una comanda activa para una mesa específica
     */
    public function obtenerComandaActivaPorMesa($mesa)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM comandas 
            WHERE mesa = :mesa AND estado IN ('pendiente', 'en_cocina') 
            ORDER BY fecha_creacion DESC LIMIT 1
        ");
        $stmt->bindParam(':mesa', $mesa);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva comanda
     */
    public function crearComanda($mesa, $id_mozo)
    {
        $stmt = $this->db->prepare("
            INSERT INTO comandas (mesa, id_mozo, estado, fecha_creacion) 
            VALUES (:mesa, :id_mozo, 'pendiente', NOW())
        ");
        $stmt->bindParam(':mesa', $mesa);
        $stmt->bindParam(':id_mozo', $id_mozo);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Agrega un detalle a la comanda o actualiza la cantidad si ya existe
     */
    public function agregarDetalleComanda($id_comanda, $id_plato, $cantidad = 1, $comentario = null)
    {
        // Verificar si el plato ya existe en la comanda con el mismo comentario
        $stmt = $this->db->prepare("
            SELECT id_detalle, cantidad 
            FROM detalle_comandas 
            WHERE id_comanda = :id_comanda AND id_plato = :id_plato 
            AND (comentario = :comentario OR (comentario IS NULL AND :comentario IS NULL))
        ");
        $stmt->bindParam(':id_comanda', $id_comanda);
        $stmt->bindParam(':id_plato', $id_plato);
        $stmt->bindParam(':comentario', $comentario);
        $stmt->execute();
        $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($detalle) {
            // Si el plato ya existe, actualizamos la cantidad
            $nuevaCantidad = $detalle['cantidad'] + $cantidad;
            $stmtUpdate = $this->db->prepare("
                UPDATE detalle_comandas 
                SET cantidad = :cantidad 
                WHERE id_detalle = :id_detalle
            ");
            $stmtUpdate->bindParam(':cantidad', $nuevaCantidad);
            $stmtUpdate->bindParam(':id_detalle', $detalle['id_detalle']);
            $stmtUpdate->execute();
            return $detalle['id_detalle'];
        } else {
            // Si el plato no existe, lo agregamos
            $stmtInsert = $this->db->prepare("
                INSERT INTO detalle_comandas (id_comanda, id_plato, cantidad, comentario, estado) 
                VALUES (:id_comanda, :id_plato, :cantidad, :comentario, 'pendiente')
            ");
            $stmtInsert->bindParam(':id_comanda', $id_comanda);
            $stmtInsert->bindParam(':id_plato', $id_plato);
            $stmtInsert->bindParam(':cantidad', $cantidad);
            $stmtInsert->bindParam(':comentario', $comentario);
            $stmtInsert->execute();
            return $this->db->lastInsertId();
        }
    }

    /**
     * Obtiene los detalles de una comanda
     */
    public function obtenerDetallesComanda($id_comanda)
    {
        $stmt = $this->db->prepare("
            SELECT dc.id_detalle, dc.cantidad, dc.comentario, dc.estado, 
                   p.id_plato, p.nombre, p.precio, p.imagen
            FROM detalle_comandas dc
            JOIN platos p ON dc.id_plato = p.id_plato
            WHERE dc.id_comanda = :id_comanda
            ORDER BY dc.id_detalle ASC
        ");
        $stmt->bindParam(':id_comanda', $id_comanda);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el comentario de un detalle
     */
    public function actualizarComentarioDetalle($id_detalle, $comentario)
    {
        $stmt = $this->db->prepare("
            UPDATE detalle_comandas 
            SET comentario = :comentario 
            WHERE id_detalle = :id_detalle
        ");
        $stmt->bindParam(':comentario', $comentario);
        $stmt->bindParam(':id_detalle', $id_detalle);
        $stmt->execute();
        return true;
    }

    /**
     * Elimina un detalle de la comanda
     */
    public function eliminarDetalleComanda($id_detalle)
    {
        $stmt = $this->db->prepare("
            DELETE FROM detalle_comandas 
            WHERE id_detalle = :id_detalle
        ");
        $stmt->bindParam(':id_detalle', $id_detalle);
        $stmt->execute();
        return true;
    }

    /**
     * Calcula el total de una comanda
     */
    public function obtenerTotalComanda($id_comanda)
    {
        $stmt = $this->db->prepare("
            SELECT SUM(dc.cantidad * p.precio) as total
            FROM detalle_comandas dc
            JOIN platos p ON dc.id_plato = p.id_plato
            WHERE dc.id_comanda = :id_comanda
        ");
        $stmt->bindParam(':id_comanda', $id_comanda);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] ?? 0;
    }

    /**
     * Actualiza el estado de una comanda
     */
    public function actualizarEstadoComanda($id_comanda, $estado)
    {
        $stmt = $this->db->prepare("
            UPDATE comandas 
            SET estado = :estado 
            WHERE id_comanda = :id_comanda
        ");
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_comanda', $id_comanda);
        $stmt->execute();
        return true;
    }
}