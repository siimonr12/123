<?php
// views/doctors/list.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_RECEPTIONIST])) {
    header('Location: ../login.php');
    exit();
}

// Conexión a la base de datos
$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

// Variables para paginación y búsqueda
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$searchTerm = $_GET['search'] ?? '';

// Consulta de médicos con búsqueda
$sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name, specialty, contact_number 
        FROM doctors
        WHERE CONCAT(first_name, ' ', last_name) LIKE :searchTerm OR specialty LIKE :searchTerm
        ORDER BY first_name ASC
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
$stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar el total de resultados con búsqueda
$countStmt = $db->prepare("SELECT COUNT(*) as total FROM doctors WHERE CONCAT(first_name, ' ', last_name) LIKE :searchTerm OR specialty LIKE :searchTerm");
$countStmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
$countStmt->execute();
$totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Lista de Médicos</h1>

        <!-- Buscador -->
        <form action="list.php" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o especialidad" value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Contacto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($doctors): ?>
                    <?php foreach ($doctors as $index => $doctor): ?>
                        <tr>
                            <td><?= $offset + $index + 1 ?></td>
                            <td><?= htmlspecialchars($doctor['full_name']) ?></td>
                            <td><?= htmlspecialchars($doctor['specialty']) ?></td>
                            <td><?= htmlspecialchars($doctor['contact_number']) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $doctor['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="delete.php?id=<?= $doctor['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                <a href="../../controllers/DoctorsController.php?action=details&id=<?= $doctor['id'] ?>" 
                                   class="btn btn-info btn-sm">
                                    Ver Detalles
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No se encontraron resultados para "<?= htmlspecialchars($searchTerm) ?>".</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($searchTerm) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
