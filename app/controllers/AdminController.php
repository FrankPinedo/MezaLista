<?php
class AdminController
{
    private $usuario;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        $this->usuario = verificarSesion();
        session_regenerate_id(true);

        if ($this->usuario['rol'] !== 'admin') {
            require_once __DIR__ . '/../controllers/ErrorController.php';
            (new ErrorController())->index('403');
            exit();
        }
    }

    public function index()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/inicio.php';
    }

    public function empresa()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/empresa.php';
    }
    public function contacto()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/contacto.php';
    }
    public function bebidas()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/bebidas.php';
    }
    public function platos()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/PlatoModel.php';
        $platoModel = new PlatoModel();
        $platos = $platoModel->obtenerPlatos();

        $_GLOBALS['platos'] = $platos;
        require_once __DIR__ . '/../views/admin/platos.php';
    }

    public function usuarios()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/usuarios.php';
    }
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit();
    }
}
