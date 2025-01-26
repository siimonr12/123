<?php
// views/consultations/summary.php

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
$stmt = $db->prepare("SELECT c.*, CONCAT(p.first_name, ' ', p.last_name) AS patient_name 
                      FROM consultations c
                      LEFT JOIN patients p ON c.patient_id = p.id
                      WHERE c.id = :id");
$stmt->bindParam(':id', $consultationId, PDO::PARAM_INT);
$stmt->execute();

$consultation = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirigir si no se encuentra la consulta
if (!$consultation) {
    header('Location: ../appointments/list.php?error=Consulta no encontrada.');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Resumen de Consulta</h1>

        <div class="mt-4">
            <h3>Paciente: <?= htmlspecialchars($consultation['patient_name']) ?></h3>
            <p><strong>Fecha:</strong> <?= htmlspecialchars($consultation['created_at']) ?></p>
            <p><strong>Diagnóstico:</strong> <?= htmlspecialchars($consultation['diagnosis']) ?></p>
            <p><strong>Procedimiento:</strong> <?= htmlspecialchars($consultation['procedure']) ?></p>
            <p><strong>Observaciones:</strong> <?= htmlspecialchars($consultation['observations']) ?></p>
        </div>

        <!-- Botón para generar PDF -->
        <div class="text-center mt-4">
            <a href="../../helpers/pdf_generator.php?consultation_id=<?= $consultationId ?>" class="btn btn-success">Descargar PDF</a>
            <a href="../appointments/list.php" class="btn btn-secondary">Regresar</a>
        </div>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
