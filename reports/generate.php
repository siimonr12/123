<?php
// views/reports/generate.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== ROLE_ADMIN) {
    header('Location: ../login.php');
    exit();
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Generar Reportes</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form action="../../controllers/ReportsController.php?action=generate" method="POST" class="mt-4">
            <div class="row">
                <!-- Tipo de Reporte -->
                <div class="col-md-6 mb-3">
                    <label for="report_type" class="form-label">Tipo de Reporte</label>
                    <select name="report_type" id="report_type" class="form-control" required>
                        <option value="general">General</option>
                        <option value="by_doctor">Por Médico</option>
                        <option value="by_disease">Por Enfermedad</option>
                    </select>
                </div>

                <!-- Fecha de Inicio -->
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Fecha de Inicio</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                </div>

                <!-- Fecha de Fin -->
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Fecha de Fin</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                </div>

                <!-- Botón de Generar -->
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
                </div>
            </div>
        </form>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
