<?php
// views/patients/details.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_DOCTOR, ROLE_RECEPTIONIST]) === false) {
    header('Location: ../login.php');
    exit();
}

$patientId = $_GET['id'] ?? null;

// Verificar que el ID del paciente esté presente
if (!$patientId) {
    header('Location: list.php?error=Paciente no encontrado.');
    exit();
}

$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

// Obtener información básica del paciente
$patientStmt = $db->prepare("SELECT * FROM patients WHERE id = :id");
$patientStmt->bindParam(':id', $patientId, PDO::PARAM_INT);
$patientStmt->execute();
$patient = $patientStmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    header('Location: list.php?error=Paciente no encontrado.');
    exit();
}

// Obtener historial de citas del paciente
$appointmentsStmt = $db->prepare("
    SELECT a.*, CONCAT(d.first_name, ' ', d.last_name) AS doctor_name 
    FROM appointments a
    LEFT JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = :patient_id
    ORDER BY a.appointment_date DESC
");
$appointmentsStmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
$appointmentsStmt->execute();
$appointments = $appointmentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Detalles del Paciente</h1>

        <!-- Información personal -->
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Información Personal</h4>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
                <p><strong>Documento de Identidad:</strong> <?= htmlspecialchars($patient['national_id']) ?></p>
                <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($patient['date_of_birth']) ?></p>
                <p><strong>Edad:</strong> <?= date_diff(date_create($patient['date_of_birth']), date_create('today'))->y ?> años</p>
                <p><strong>Género:</strong> <?= htmlspecialchars($patient['gender']) ?></p>
                <p><strong>Número de Contacto:</strong> <?= htmlspecialchars($patient['contact_number']) ?></p>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($patient['address']) ?></p>
            </div>
        </div>

        <!-- Historia clínica -->
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title">Historia Clínica</h4>
                <p><strong>Enfermedades Preexistentes:</strong> <?= htmlspecialchars($patient['preexisting_conditions'] ?? 'No especificado') ?></p>
                <p><strong>Alergias:</strong> <?= htmlspecialchars($patient['allergies'] ?? 'No especificado') ?></p>
                <p><strong>Medicamentos en Uso:</strong> <?= htmlspecialchars($patient['current_medications'] ?? 'No especificado') ?></p>
                <p><strong>Cirugías Anteriores:</strong> <?= htmlspecialchars($patient['previous_surgeries'] ?? 'No especificado') ?></p>
                <p><strong>Hábitos de Vida:</strong> <?= htmlspecialchars($patient['lifestyle'] ?? 'No especificado') ?></p>
                <p><strong>Datos Demográficos:</strong> <?= htmlspecialchars($patient['demographics'] ?? 'No especificado') ?></p>
                <p><strong>Inmunizaciones:</strong> <?= htmlspecialchars($patient['immunizations'] ?? 'No especificado') ?></p>

                <div class="mt-3 text-end">
                    <a href="medical_history.php?id=<?= $patient['id'] ?>" class="btn btn-primary">Editar Historia Clínica</a>
                </div>
            </div>
        </div>

        <!-- Historial de citas -->
        <div class="mt-5">
            <h4>Historial de Citas</h4>
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Médico</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments): ?>
                        <?php foreach ($appointments as $index => $appointment): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($appointment['description']) ?></td>
                                <td><?= htmlspecialchars($appointment['status'] ?? 'Pendiente') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay citas registradas para este paciente.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Botón de regresar -->
        <div class="mt-4 text-center">
            <a href="list.php" class="btn btn-secondary">Regresar a la lista</a>
        </div>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
