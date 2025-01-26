<?php
// views/doctors/create.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== ROLE_ADMIN) {
    header('Location: ../login.php');
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
        <h1 class="text-center">Registrar Médico</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="../../controllers/DoctorsController.php?action=create" method="POST" class="mt-4">
            <div class="row">
                <!-- Nombre -->
                <div class="col-md-4 mb-3">
                    <label for="first_name" class="form-label">Nombre</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Ingrese el nombre" required>
                </div>

                <!-- Apellido -->
                <div class="col-md-4 mb-3">
                    <label for="last_name" class="form-label">Apellido</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Ingrese el apellido" required>
                </div>

                <!-- Cédula -->
                <div class="col-md-4 mb-3">
                    <label for="national_id" class="form-label">Cédula</label>
                    <input type="text" name="national_id" id="national_id" class="form-control" placeholder="Ingrese la cédula" required>
                </div>

                <!-- Especialidad -->
                <div class="col-md-6 mb-3">
                    <label for="specialty" class="form-label">Especialidad</label>
                    <select name="specialty" id="specialty" class="form-control" required>
                        <option value="">Seleccione una especialidad</option>
                        <option value="Cardiología">Cardiología</option>
                        <option value="Pediatría">Pediatría</option>
                        <option value="Dermatología">Dermatología</option>
                        <option value="Neurología">Neurología</option>
                        <option value="Medicina General">Medicina General</option>
                    </select>
                </div>

                <!-- Género -->
                <div class="col-md-4 mb-3">
                    <label for="gender" class="form-label">Género</label>
                    <select name="gender" id="gender" class="form-control" required>
                        <option value="">Seleccione el género</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <!-- Número de Contacto -->
                <div class="col-md-4 mb-3">
                    <label for="contact_number" class="form-label">Número de Contacto</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="Ingrese el número de contacto" required>
                </div>

                <!-- Dirección -->
                <div class="col-md-4 mb-3">
                    <label for="address" class="form-label">Dirección</label>
                    <textarea name="address" id="address" class="form-control" rows="2" placeholder="Ingrese la dirección" required></textarea>
                </div>

                <!-- Citas diarias permitidas -->
                <div class="col-md-6 mb-3">
                    <label for="max_daily_appointments" class="form-label">Citas Diarias Permitidas</label>
                    <input type="number" name="max_daily_appointments" id="max_daily_appointments" class="form-control" value="10" min="1" max="50" required>
                </div>

                <!-- Días de atención -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Días Disponibles para Atención</label>
                    <div class="form-check">
                        <input type="checkbox" name="available_days[]" value="Lunes" class="form-check-input" id="monday">
                        <label for="monday" class="form-check-label">Lunes</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="available_days[]" value="Martes" class="form-check-input" id="tuesday">
                        <label for="tuesday" class="form-check-label">Martes</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="available_days[]" value="Miércoles" class="form-check-input" id="wednesday">
                        <label for="wednesday" class="form-check-label">Miércoles</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="available_days[]" value="Jueves" class="form-check-input" id="thursday">
                        <label for="thursday" class="form-check-label">Jueves</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="available_days[]" value="Viernes" class="form-check-input" id="friday">
                        <label for="friday" class="form-check-label">Viernes</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="available_days[]" value="Sábado" class="form-check-input" id="saturday">
                        <label for="saturday" class="form-check-label">Sábado</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="available_days[]" value="Domingo" class="form-check-input" id="sunday">
                        <label for="sunday" class="form-check-label">Domingo</label>
                    </div>
                </div>

                <!-- Botón de registro -->
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Registrar Médico</button>
                </div>
            </div>
        </form>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
