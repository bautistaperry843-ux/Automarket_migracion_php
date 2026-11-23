<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verificación 1: Autenticación básica
 * Redirige al inicio de sesión si no hay sesión activa.
 */
function verificarAutenticacion() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../inicio de sesion.php?error=acceso_denegado");
        exit;
    }
}

/**
 * Verificación 2: Control de Acceso por Roles (RBAC)
 * Verifica que la sesión esté activa y que el rol sea exclusivamente 'admin'.
 */
function verificarAdmin() {
    verificarAutenticacion();
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        die("<div style='padding: 20px; font-family: Arial; color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
                <h2>Acceso Denegado</h2>
                <p>No tenés los permisos necesarios para acceder a esta sección.</p>
                <a href='../index.php' style='color: #721c24; font-weight: bold;'>Volver al Inicio</a>
             </div>");
    }
}
?>