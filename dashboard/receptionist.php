<?php
// views/dashboard/receptionist.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== ROLE_RECEPTIONIST) {
    header('Location: ../login.php');
    exit();
}

// Conexión a la base de datos
$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

// Obtener estadísticas
$totalAppointments = $db->query("SELECT COUNT(*) AS total FROM appointments")->fetch(PDO::FETCH_ASSOC)['total'];
$totalPatients = $db->query("SELECT COUNT(*) AS total FROM patients")->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener citas del día actual
$todayAppointments = $db->query("SELECT COUNT(*) AS total FROM appointments WHERE DATE(appointment_date) = CURDATE()")->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Panel de Control - Recepcionista</h1>

        <!-- Sección de estadísticas -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Pacientes Registrados</h5>
                        <p class="card-text"><?= $totalPatients ?></p>
                        <a href="../patients/create.php" class="btn btn-light btn-sm">Agregar Paciente</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Citas Programadas Hoy</h5>
                        <p class="card-text"><?= $todayAppointments ?></p>
                        <a href="../appointments/create.php" class="btn btn-light btn-sm">Programar Cita</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acceso rápido -->
        <div class="mt-5 text-center">
            <a href="../patients/list.php" class="btn btn-success mx-2">Gestionar Pacientes</a>
            <a href="../appointments/list.php" class="btn btn-warning mx-2">Ver Citas</a>
        </div>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>

