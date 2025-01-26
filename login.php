<?php
// views/login.php

require_once '../config/constants.php';
require_once '../config/database.php';

session_start();

// Redirigir al dashboard si el usuario ya ha iniciado sesión
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_role']) {
        case ROLE_ADMIN:
            header('Location: dashboard/admin.php');
            break;
        case ROLE_DOCTOR:
            header('Location: dashboard/doctor.php');
            break;
        case ROLE_RECEPTIONIST:
            header('Location: dashboard/receptionist.php');
            break;
        case ROLE_PATIENT:
            header('Location: dashboard/patient.php');
            break;
    }
    exit();
}

// Mostrar errores si hay algún problema
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">

<?php include 'shared/head.php'; ?>

<body class="d-flex align-items-center justify-content-center bg-light" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center">Iniciar Sesión</h3>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form action="../controllers/AuthController.php?action=login" method="POST" class="mt-4">
                            <!-- Usuario -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Ingrese su usuario" required>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su contraseña" required>
                            </div>

                            <!-- Botón de iniciar sesión -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                        </form>

                        <!-- Enlace de ayuda -->
                        <div class="mt-3 text-center">
                            <a href="#" class="text-muted">¿Olvidó su contraseña?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'shared/footer.php'; ?>
</body>

</html>
