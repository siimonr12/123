<?php
// views/appointments/create.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_RECEPTIONIST])) {
    header('Location: ../login.php');
    exit();
}

// Obtener lista de médicos y pacientes
$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

$doctors = $db->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM doctors")->fetchAll(PDO::FETCH_ASSOC);
$patients = $db->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM patients")->fetchAll(PDO::FETCH_ASSOC);

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Programar Nueva Cita</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="../../controllers/AppointmentsController.php?action=create" method="POST" class="mt-4">
            <div class="row">
                <!-- Selección de Paciente -->
                <div class="col-md-6 mb-3">
                    <label for="patient_id" class="form-label">Paciente</label>
                    <select name="patient_id" id="patient_id" class="form-control" required>
                        <option value="">Seleccione un paciente</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?= $patient['id'] ?>"><?= htmlspecialchars($patient['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Selección de Médico -->
                <div class="col-md-6 mb-3">
                    <label for="doctor_id" class="form-label">Médico</label>
                    <select name="doctor_id" id="doctor_id" class="form-control" required>
                        <option value="">Seleccione un médico</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['id'] ?>"><?= htmlspecialchars($doctor['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Fecha de la Cita -->
                <div class="col-md-6 mb-3">
                    <label for="appointment_date" class="form-label">Fecha de la Cita</label>
                    <input type="datetime-local" name="appointment_date" id="appointment_date" class="form-control" required>
                </div>

                <!-- Descripción -->
                <div class="col-md-6 mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Descripción breve de la cita"></textarea>
                </div>

                <!-- Ubicación -->
                <div class="col-md-6 mb-3">
                    <label for="location" class="form-label">Ubicación</label>
                    <input type="text" name="location" id="location" class="form-control" placeholder="Ingrese la ubicación" required>
                </div>

                <!-- Botón de Programar -->
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Programar Cita</button>
                </div>
            </div>
        </form>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
