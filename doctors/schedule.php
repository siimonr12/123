<?php
// views/doctors/schedule.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_DOCTOR])) {
    header('Location: ../login.php');
    exit();
}

// Obtener el ID del médico de la sesión o parámetro
$doctorId = $_SESSION['user_role'] === ROLE_DOCTOR ? $_SESSION['user_id'] : ($_GET['doctor_id'] ?? null);

if (!$doctorId) {
    header('Location: list.php?error=Debe seleccionar un médico.');
    exit();
}

// Conexión a la base de datos
$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

// Obtener horarios del médico
$stmt = $db->prepare("SELECT * FROM doctor_schedules WHERE doctor_id = :doctor_id ORDER BY day ASC, start_time ASC");
$stmt->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
$stmt->execute();

$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener información del médico
$doctorStmt = $db->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM doctors WHERE id = :id");
$doctorStmt->bindParam(':id', $doctorId, PDO::PARAM_INT);
$doctorStmt->execute();

$doctor = $doctorStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Horario de <?= htmlspecialchars($doctor['full_name']) ?></h1>

        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Día</th>
                    <th>Hora de Inicio</th>
                    <th>Hora de Fin</th>
                    <th>Citas Máximas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($schedules): ?>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?= htmlspecialchars($schedule['day']) ?></td>
                            <td><?= htmlspecialchars($schedule['start_time']) ?></td>
                            <td><?= htmlspecialchars($schedule['end_time']) ?></td>
                            <td><?= htmlspecialchars($schedule['max_appointments']) ?></td>
                            <td>
                                <a href="edit_schedule.php?id=<?= $schedule['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="delete_schedule.php?id=<?= $schedule['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay horarios registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Botón para agregar nuevo horario -->
        <div class="text-center mt-4">
            <a href="create_schedule.php?doctor_id=<?= $doctorId ?>" class="btn btn-primary">Agregar Nuevo Horario</a>
        </div>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
