<?php
class PlatosController
{
    public function guardarPlato()
    {
        require_once __DIR__ . '/../models/PlatoModel.php';
        $platoModel = new PlatoModel();

        // Recoger datos enviados desde el formulario
        $nombre = $_POST['nombre'] ?? '';
        $precio = $_POST['precio'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        // Subir imagen
        $imagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

            $directorio = __DIR__ . '/../../public/uploads/';

            $nombreImagen = uniqid() . '_' . basename($_FILES['imagen']['name']);
            $rutaDestino = $directorio . $nombreImagen; $nombreImagen;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
            $imagen = $nombreImagen;
        }

        // Insertar en la base de datos
        $platoModel->agregarPlato($nombre, $precio, $stock, $descripcion, $estado, $imagen);

        // Redireccionar nuevamente a productos
        header('Location: ' . BASE_URL . '/admin/platos');
        exit();
    }
}
