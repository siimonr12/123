<?php
// views/dashboard/doctor.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== ROLE_DOCTOR) {
    header('Location: ../login.php');
    exit();
}

$doctorId = $_SESSION['user_id'];

// Conexión a la base de datos
$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

// Obtener estadísticas del doctor
$totalAppointments = $db->prepare("SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = :doctor_id");
$totalAppointments->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
$totalAppointments->execute();
$totalAppointments = $totalAppointments->fetch(PDO::FETCH_ASSOC)['total'];

$todayAppointments = $db->prepare("SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = :doctor_id AND DATE(appointment_date) = CURDATE()");
$todayAppointments->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
$todayAppointments->execute();
$todayAppointments = $todayAppointments->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener citas del día
$appointments = $db->prepare("
    SELECT a.id, CONCAT(p.first_name, ' ', p.last_name) AS patient_name, a.appointment_date, a.description 
    FROM appointments a
    LEFT JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = :doctor_id AND DATE(a.appointment_date) = CURDATE()
    ORDER BY a.appointment_date ASC
");
$appointments->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
$appointments->execute();
$appointments = $appointments->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Panel de Control - Médico</h1>

        <!-- Sección de estadísticas -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Citas Totales</h5>
                        <p class="card-text"><?= $totalAppointments ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Citas Hoy</h5>
                        <p class="card-text"><?= $todayAppointments ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de citas del día -->
        <div class="mt-5">
            <h3 class="text-center">Citas de Hoy</h3>
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments): ?>
                        <?php foreach ($appointments as $index => $appointment): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                                <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                <td><?= htmlspecialchars($appointment['description']) ?></td>
                                <td>
                                    <a href="../consultations/panel.php?id=<?= $appointment['id'] ?>" class="btn btn-primary btn-sm">Ir a Consulta</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay citas programadas para hoy.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
