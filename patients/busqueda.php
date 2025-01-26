<?php
// views/patients/search.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

$error = '';
$citas = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documentId = trim($_POST['document_id'] ?? '');

    if (empty($documentId)) {
        $error = 'Por favor, ingrese su cédula.';
    } else {
        try {
            $db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Buscar el paciente por cédula
            $stmtPatient = $db->prepare("SELECT id, first_name, last_name FROM patients WHERE document_id = :document_id");
            $stmtPatient->bindParam(':document_id', $documentId);
            $stmtPatient->execute();
            $patient = $stmtPatient->fetch(PDO::FETCH_ASSOC);

            if ($patient) {
                // Buscar citas del paciente
                $stmtCitas = $db->prepare("
                    SELECT 
                        a.id, 
                        a.appointment_date, 
                        a.status, 
                        d.first_name AS doctor_first_name, 
                        d.last_name AS doctor_last_name, 
                        d.specialty 
                    FROM appointments a
                    JOIN doctors d ON a.doctor_id = d.id
                    WHERE a.patient_id = :patient_id
                    ORDER BY a.appointment_date DESC
                ");
                $stmtCitas->bindParam(':patient_id', $patient['id'], PDO::PARAM_INT);
                $stmtCitas->execute();
                $citas = $stmtCitas->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $error = 'Cédula no encontrada. Por favor, verifique.';
            }
        } catch (PDOException $e) {
            $error = 'Error al conectar con la base de datos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center">Consultar Estado de Citas</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-4">
            <div class="row justify-content-center">
                <div class="col-md-6 mb-3">
                    <label for="document_id" class="form-label">Ingrese su Cédula</label>
                    <input type="text" name="document_id" id="document_id" class="form-control" placeholder="V12345678" required>
                </div>
                <div class="col-md-6 text-center">
                    <button type="submit" class="btn btn-primary mt-3">Consultar</button>
                </div>
            </div>
        </form>

        <?php if (!empty($citas)): ?>
            <h2 class="mt-5">Estado de Citas</h2>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Médico</th>
                        <th>Especialidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <tr>
                            <td><?= htmlspecialchars($cita['appointment_date']) ?></td>
                            <td><?= htmlspecialchars($cita['status']) ?></td>
                            <td><?= htmlspecialchars($cita['doctor_first_name'] . ' ' . $cita['doctor_last_name']) ?></td>
                            <td><?= htmlspecialchars($cita['specialty']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($citas)): ?>
            <div class="alert alert-info text-center mt-5">No hay citas asociadas a esta cédula.</div>
        <?php endif; ?>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
