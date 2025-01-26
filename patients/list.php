<?php
// views/patients/list.php

require_once '../../config/constants.php';
require_once '../../config/database.php';

session_start();

// Verificar si el usuario está autenticado y tiene permisos adecuados
if (!isset($_SESSION['user_id']) || in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_RECEPTIONIST]) === false) {
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

// Consulta de pacientes con búsqueda
$sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name, national_id, history_number, contact_number 
        FROM patients
        WHERE CONCAT(first_name, ' ', last_name) LIKE :searchTerm OR national_id LIKE :searchTerm
        ORDER BY first_name ASC
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
$stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar el total de resultados con búsqueda
$countStmt = $db->prepare("SELECT COUNT(*) as total FROM patients WHERE CONCAT(first_name, ' ', last_name) LIKE :searchTerm OR national_id LIKE :searchTerm");
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
        <h1 class="text-center">Lista de Pacientes</h1>

        <!-- Buscador -->
        <form action="list.php" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o cédula" value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Número de Historia</th>
                    <th>Contacto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($patients): ?>
                    <?php foreach ($patients as $index => $patient): ?>
                        <tr>
                            <td><?= $offset + $index + 1 ?></td>
                            <td><?= htmlspecialchars($patient['full_name']) ?></td>
                            <td><?= htmlspecialchars($patient['national_id']) ?></td>
                            <td><?= htmlspecialchars($patient['history_number']) ?></td>
                            <td><?= htmlspecialchars($patient['contact_number']) ?></td>
                            <td>
                            <a href="details.php?id=<?= $patient['id'] ?>" class="btn btn-info btn-sm">Ver Detalles</a>
    <a href="edit.php?id=<?= $patient['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
    <a href="delete.php?id=<?= $patient['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron resultados para "<?= htmlspecialchars($searchTerm) ?>".</td>
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
