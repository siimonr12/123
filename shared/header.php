<?php
// Verificar si la sesión ya está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_role'])) {
    header('Location: ../views/login.php?error=Debe iniciar sesión.');
    exit();
}

$userRole = $_SESSION['user_role'];
$userName = $_SESSION['user_name'] ?? 'Usuario'; // Asumimos que el nombre del usuario está almacenado
$userImage = $_SESSION['user_image'] ?? '../../public/images/default-user.png'; // Imagen por defecto
?>

<!-- Botón de toggle (solo visible en móvil) -->
<button class="navbar-toggle" onclick="toggleNavbar()">☰</button>

<!-- Barra de navegación -->
<nav class="navbar" id="navbar">
    <div class="navbar-header">
        <img src="<?= htmlspecialchars($userImage) ?>" alt="Usuario" class="user-image">
        <div class="user-info">
            <h5><?= htmlspecialchars($userName) ?></h5>
            <span><?= htmlspecialchars($userRole) ?></span>
        </div>
    </div>
    <ul class="navbar-nav">
        <!-- Opciones comunes a todos los usuarios -->
        <li class="nav-item">
            <a class="nav-link <?= ($_SERVER['PHP_SELF'] === '/dashboard/index.php') ? 'active' : '' ?>" href="../dashboard/index.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>

        <?php if ($userRole === ROLE_ADMIN): ?>
            <li class="nav-item">
                <a class="nav-link <?= ($_SERVER['PHP_SELF'] === '/patients/list.php') ? 'active' : '' ?>" href="../patients/list.php">
                    <i class="fas fa-user-injured"></i> Pacientes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_SERVER['PHP_SELF'] === '/doctors/list.php') ? 'active' : '' ?>" href="../doctors/list.php">
                    <i class="fas fa-user-md"></i> Médicos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_SERVER['PHP_SELF'] === '/appointments/list.php') ? 'active' : '' ?>" href="../appointments/list.php">
                    <i class="fas fa-calendar-check"></i> Citas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($_SERVER['PHP_SELF'] === '/reports/index.php') ? 'active' : '' ?>" href="../reports/index.php">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </li>
        <?php endif; ?>
        
        <li class="nav-item logout">
            <a class="nav-link text-danger" href="../../helpers/session_manager.php?action=logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </li>
    </ul>
</nav>

<!-- Script de toggle -->
<script>
    function toggleNavbar() {
        const navbar = document.getElementById('navbar');
        navbar.classList.toggle('open');
    }
</script>

<!-- Estilos de la barra de navegación -->
<style>
    :root {
        --primary-color: #064C58;
        --secondary-color: #056E7C;
        --accent-color: #32B6C1;
        --text-color: #ffffff;
        --active-color: #028C96;
        --hover-color: rgba(255, 255, 255, 0.1);
    }

    /* Barra de navegación */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 240px;
        background-color: var(--primary-color);
        color: var(--text-color);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1rem;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 1000;
    }

    .navbar.open,
    @media (min-width: 768px) {
        .navbar {
            transform: translateX(0);
        }
    }

    .navbar .navbar-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .navbar .user-image {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--accent-color);
    }

    .navbar .user-info h5 {
        font-size: 1rem;
        margin: 0;
        color: var(--text-color);
    }

    .navbar .user-info span {
        font-size: 0.875rem;
        color: #B0E3E5;
    }

    .navbar-nav {
        list-style: none;
        padding: 0;
    }

    .nav-item {
        margin-bottom: 1rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: var(--text-color);
        font-size: 1rem;
        padding: 0.75rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s ease;
    }

    .nav-link:hover,
    .nav-link.active {
        background-color: var(--hover-color);
    }

    .nav-link i {
        font-size: 1.2rem;
    }

    .navbar-toggle {
        display: none;
        position: fixed;
        top: 1rem;
        left: 1rem;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        padding: 0.75rem;
        font-size: 1.25rem;
        z-index: 1100;
    }

    .logout {
        margin-top: auto;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar {
            transform: translateX(-100%);
        }

        .navbar-toggle {
            display: block;
        }
    }
</style>
