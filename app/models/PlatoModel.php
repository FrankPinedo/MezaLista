<?php
require_once __DIR__ . '/../../config/config.php';

class PlatoModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    }

    public function obtenerPlatos()
    {
        $query = $this->db->query("SELECT * FROM platos");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregarPlato($nombre, $precio, $stock, $descripcion, $estado, $imagen)
    {
        $stmt = $this->db->prepare("INSERT INTO platos (nombre, precio, stock, descripcion, estado, imagen) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$nombre, $precio, $stock, $descripcion, $estado, $imagen]);
    }

    public function eliminarPlato($id)
    {
        $stmt = $this->db->prepare("DELETE FROM platos WHERE id_plato = ?");
        return $stmt->execute([$id]);
    }

    public function obtenerPlatoPorId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM platos WHERE id_plato = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarProducto($id, $nombre, $precio, $stock, $descripcion, $estado, $imagen)
    {
        $stmt = $this->db->prepare("UPDATE platos SET nombre=?, precio=?, stock=?, descripcion=?, estado=?, imagen=? WHERE id_plato=?");
        return $stmt->execute([$nombre, $precio, $stock, $descripcion, $estado, $imagen, $id]);
    }
}
