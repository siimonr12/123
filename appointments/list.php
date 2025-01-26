<?php
// views/appointments/list.php

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

// Consulta de citas con búsqueda
$sql = "SELECT a.id, CONCAT(p.first_name, ' ', p.last_name) AS patient_name, 
               CONCAT(d.first_name, ' ', d.last_name) AS doctor_name, a.appointment_date, a.description
        FROM appointments a
        LEFT JOIN patients p ON a.patient_id = p.id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        WHERE CONCAT(p.first_name, ' ', p.last_name) LIKE :searchTerm 
           OR CONCAT(d.first_name, ' ', d.last_name) LIKE :searchTerm
           OR a.description LIKE :searchTerm
        ORDER BY a.appointment_date ASC
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
$stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar el total de resultados con búsqueda
$countStmt = $db->prepare("SELECT COUNT(*) as total FROM appointments a
                           LEFT JOIN patients p ON a.patient_id = p.id
                           LEFT JOIN doctors d ON a.doctor_id = d.id
                           WHERE CONCAT(p.first_name, ' ', p.last_name) LIKE :searchTerm 
                              OR CONCAT(d.first_name, ' ', d.last_name) LIKE :searchTerm
                              OR a.description LIKE :searchTerm");
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
        <h1 class="text-center">Lista de Citas</h1>

        <!-- Buscador -->
        <form action="list.php" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Buscar por paciente, médico o descripción" value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($appointments): ?>
                    <?php foreach ($appointments as $index => $appointment): ?>
                        <tr>
                            <td><?= $offset + $index + 1 ?></td>
                            <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                            <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                            <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                            <td><?= htmlspecialchars($appointment['description']) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $appointment['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="delete.php?id=<?= $appointment['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
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
