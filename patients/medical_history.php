<?php
// views/patients/medical_history.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

if (!isset($_SESSION['user_id']) || in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_DOCTOR]) === false) {
    header('Location: ../login.php');
    exit();
}

$patientId = $_GET['id'] ?? null;

if (!$patientId) {
    header('Location: list.php?error=Paciente no encontrado.');
    exit();
}

// Conexión a la base de datos
$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

// Obtener información del paciente
$patientStmt = $db->prepare("SELECT * FROM patients WHERE id = :id");
$patientStmt->bindParam(':id', $patientId, PDO::PARAM_INT);
$patientStmt->execute();
$patient = $patientStmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    header('Location: list.php?error=Paciente no encontrado.');
    exit();
}

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Historia Clínica de <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></h1>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="../../controllers/PatientsController.php?action=update_history&id=<?= $patientId ?>" method="POST" class="mt-4">
            <div class="row">
                <!-- Enfermedades preexistentes -->
                <div class="col-md-6 mb-3">
                    <label for="preexisting_conditions" class="form-label">Enfermedades Preexistentes</label>
                    <textarea name="preexisting_conditions" id="preexisting_conditions" class="form-control" rows="2"></textarea>
                </div>

                <!-- Alergias -->
                <div class="col-md-6 mb-3">
                    <label for="allergies" class="form-label">Alergias</label>
                    <textarea name="allergies" id="allergies" class="form-control" rows="2"></textarea>
                </div>

                <!-- Medicamentos en uso -->
                <div class="col-md-6 mb-3">
                    <label for="current_medications" class="form-label">Medicamentos en Uso</label>
                    <textarea name="current_medications" id="current_medications" class="form-control" rows="2"></textarea>
                </div>

                <!-- Cirugías anteriores -->
                <div class="col-md-6 mb-3">
                    <label for="previous_surgeries" class="form-label">Cirugías Anteriores</label>
                    <textarea name="previous_surgeries" id="previous_surgeries" class="form-control" rows="2"></textarea>
                </div>

                <!-- Hábitos de vida -->
                <div class="col-md-6 mb-3">
                    <label for="lifestyle" class="form-label">Hábitos de Vida</label>
                    <textarea name="lifestyle" id="lifestyle" class="form-control" rows="2"></textarea>
                </div>

                <!-- Botón -->
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Guardar Historia Clínica</button>
                </div>
            </div>
        </form>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
