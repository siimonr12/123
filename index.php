<?php
// index.php

require_once 'config/constants.php';
require_once 'config/database.php';

session_start();

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_id'])) {
    // Redirigir según el rol del usuario
    switch ($_SESSION['user_role']) {
        case ROLE_ADMIN:
            header('Location: views/dashboard/admin.php');
            break;
        case ROLE_DOCTOR:
            header('Location: views/dashboard/doctor.php');
            break;
        case ROLE_RECEPTIONIST:
            header('Location: views/dashboard/receptionist.php');
            break;
        case ROLE_PATIENT:
            header('Location: views/dashboard/patient.php');
            break;
        default:
            echo "Rol no reconocido. Contacte al administrador del sistema.";
            exit();
    }
} else {
    // Si no está autenticado, redirigir al login
    header('Location: views/login.php');
    exit();
}
