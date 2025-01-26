<?php
// views/dashboard/patient.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== ROLE_PATIENT) {
    header('Location: ../login.php');
    exit();
}

$patientId = $_SESSION['user_id'];

// Conexión a la base de datos
$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

// Obtener estadísticas del paciente
$totalAppointments = $db->prepare("SELECT COUNT(*) AS total FROM appointments WHERE patient_id = :patient_id");
$totalAppointments->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
$totalAppointments->execute();
$totalAppointments = $totalAppointments->fetch(PDO::FETCH_ASSOC)['total'];

$upcomingAppointments = $db->prepare("
    SELECT a.id, a.appointment_date, d.first_name AS doctor_name, a.description
    FROM appointments a
    LEFT JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = :patient_id AND a.appointment_date > NOW()
    ORDER BY a.appointment_date ASC
");
$upcomingAppointments->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
$upcomingAppointments->execute();
$upcomingAppointments = $upcomingAppointments->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Panel de Control - Paciente</h1>

        <!-- Sección de estadísticas -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Citas Totales</h5>
                        <p class="card-text"><?= $totalAppointments ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de próximas citas -->
        <div class="mt-5">
            <h3 class="text-center">Próximas Citas</h3>
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Médico</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($upcomingAppointments): ?>
                        <?php foreach ($upcomingAppointments as $index => $appointment): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($appointment['description']) ?></td>
                                <td>
                                    <a href="../appointments/summary.php?id=<?= $appointment['id'] ?>" class="btn btn-info btn-sm">Ver Detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay próximas citas programadas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
