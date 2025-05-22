<?php
class MozoController
{
    private $usuario;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        $this->usuario = verificarSesion();
        session_regenerate_id(true);

        if ($this->usuario['rol'] !== 'mozo') {
            require_once __DIR__ . '/../controllers/ErrorController.php';
            (new ErrorController())->index('403');
            exit();
        }
        
        // QUITAR ESTA LÍNEA - NO CARGAR LA VISTA AQUÍ
        // require_once __DIR__ . '/../views/mozo/inicio.php';
    }

    public function index()
    {
        // AHORA SÍ CARGAR LA VISTA AQUÍ
        require_once __DIR__ . '/../views/mozo/inicio.php';
    }

    /**
     * Redirige al módulo de comanda para una mesa específica
     */
    public function comanda($mesa = null)
    {
        if (!$mesa) {
            header('Location: ' . BASE_URL . '/mozo');
            exit();
        }

        // Incluir y ejecutar el ComandaController
        require_once __DIR__ . '/ComandaController.php';
        $comandaController = new ComandaController();
        $comandaController->index($mesa);
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