<?php
// views/consultations/panel.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== ROLE_DOCTOR) {
    header('Location: ../login.php');
    exit();
}

$consultationId = $_GET['id'] ?? null;

// Verificar si se proporciona un ID de consulta
if (!$consultationId) {
    header('Location: ../appointments/list.php?error=Debe seleccionar una consulta.');
    exit();
}

// Conexión a la base de datos
$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

// Obtener detalles de la consulta
$stmt = $db->prepare("SELECT * FROM consultations WHERE id = :id");
$stmt->bindParam(':id', $consultationId, PDO::PARAM_INT);
$stmt->execute();
$consultation = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirigir si no se encuentra la consulta
if (!$consultation) {
    header('Location: ../appointments/list.php?error=Consulta no encontrada.');
    exit();
}

// Obtener información del paciente
$patientStmt = $db->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name FROM patients WHERE id = :id");
$patientStmt->bindParam(':id', $consultation['patient_id'], PDO::PARAM_INT);
$patientStmt->execute();
$patient = $patientStmt->fetch(PDO::FETCH_ASSOC);

// Obtener medicamentos disponibles
$medicinesStmt = $db->query("SELECT id, name, quantity FROM medicines WHERE quantity > 0");
$medicines = $medicinesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Panel de Consulta</h1>
        <h3 class="text-center">Paciente: <?= htmlspecialchars($patient['name']) ?></h3>

        <form action="../../controllers/ConsultationsController.php?action=update" method="POST" class="mt-4">
            <input type="hidden" name="consultation_id" value="<?= htmlspecialchars($consultationId) ?>">

            <!-- Diagnóstico -->
            <div class="mb-3">
                <label for="diagnosis" class="form-label">Diagnóstico</label>
                <textarea name="diagnosis" id="diagnosis" class="form-control" rows="3" required><?= htmlspecialchars($consultation['diagnosis']) ?></textarea>
            </div>

            <!-- Procedimiento -->
            <div class="mb-3">
                <label for="procedure" class="form-label">Procedimiento</label>
                <textarea name="procedure" id="procedure" class="form-control" rows="3" required><?= htmlspecialchars($consultation['procedure']) ?></textarea>
            </div>

            <!-- Observaciones -->
            <div class="mb-3">
                <label for="observations" class="form-label">Observaciones</label>
                <textarea name="observations" id="observations" class="form-control" rows="3"><?= htmlspecialchars($consultation['observations']) ?></textarea>
            </div>

            <!-- Medicamentos -->
            <div class="mb-3">
                <label for="medicines" class="form-label">Medicamentos</label>
                <select name="medicines[]" id="medicines" class="form-control" multiple>
                    <?php foreach ($medicines as $medicine): ?>
                        <option value="<?= htmlspecialchars($medicine['id']) ?>">
                            <?= htmlspecialchars($medicine['name']) ?> (Disponible: <?= htmlspecialchars($medicine['quantity']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Cantidades -->
            <div class="mb-3">
                <label for="quantities" class="form-label">Cantidades por Medicamento</label>
                <input type="text" name="quantities" id="quantities" class="form-control" placeholder="Separar por comas (ej: 1,2,3)" required>
            </div>

            <!-- Botones -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="../../controllers/ConsultationsController.php?action=finalize&id=<?= htmlspecialchars($consultationId) ?>" class="btn btn-success">Finalizar Consulta</a>
                <a href="../../controllers/ConsultationsController.php?action=print&id=<?= htmlspecialchars($consultationId) ?>" class="btn btn-secondary">Imprimir</a>
            </div>
        </form>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
