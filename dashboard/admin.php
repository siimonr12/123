<?php
require_once '../../config/constants.php';
require_once '../../config/database.php';
session_start();

// Verificar que el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== ROLE_ADMIN) {
    header('Location: ../login.php');
    exit();
}

// Conexión a la base de datos
$db = new PDO("mysql:host=localhost;dbname=imsasur_management", "root", "");

// Consultas de estadísticas
$totalPatients = $db->query("SELECT COUNT(*) AS total FROM patients")->fetch(PDO::FETCH_ASSOC)['total'];
$totalDoctors = $db->query("SELECT COUNT(*) AS total FROM doctors")->fetch(PDO::FETCH_ASSOC)['total'];
$totalAppointments = $db->query("SELECT COUNT(*) AS total FROM appointments")->fetch(PDO::FETCH_ASSOC)['total'];
$totalCompletedAppointments = $db->query("SELECT COUNT(*) AS total FROM appointments WHERE status = 'concretada'")->fetch(PDO::FETCH_ASSOC)['total'];

// Últimas citas
$recentAppointments = $db->query("
    SELECT a.*, 
           CONCAT(p.first_name, ' ', p.last_name) AS patient_name, 
           CONCAT(d.first_name, ' ', d.last_name) AS doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    ORDER BY a.appointment_date DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Enfermedades más frecuentes
$diseasesStats = $db->query("
    SELECT diagnosis, COUNT(*) AS count 
    FROM consultations 
    GROUP BY diagnosis 
    ORDER BY count DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Datos para el mapa
$mapData = $db->query("
    SELECT address, latitude, longitude 
    FROM patients 
    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Dashboard del Administrador</h1>

        <!-- Botones rápidos -->
        <div class="row mb-4">
            <div class="col-md-4">
                <a href="../patients/create.php" class="btn btn-primary btn-block">Agregar Paciente</a>
            </div>
            <div class="col-md-4">
                <a href="../doctors/create.php" class="btn btn-success btn-block">Agregar Médico</a>
            </div>
            <div class="col-md-4">
                <a href="../appointments/create.php" class="btn btn-warning btn-block">Agendar Cita</a>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row text-center">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Pacientes</h5>
                        <h2><?= $totalPatients ?></h2>
                        <a href="../patients/list.php" class="btn btn-primary btn-sm">Ver Pacientes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Médicos</h5>
                        <h2><?= $totalDoctors ?></h2>
                        <a href="../doctors/list.php" class="btn btn-success btn-sm">Ver Médicos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Citas Totales</h5>
                        <h2><?= $totalAppointments ?></h2>
                        <a href="../appointments/list.php" class="btn btn-warning btn-sm">Ver Citas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Citas Completadas</h5>
                        <h2><?= $totalCompletedAppointments ?></h2>
                        <a href="../appointments/list.php?filter=completed" class="btn btn-danger btn-sm">Ver Completadas</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">Estado de las Citas</div>
                    <div class="card-body">
                        <canvas id="appointmentsStatusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">Enfermedades más Frecuentes</div>
                    <div class="card-body">
                        <canvas id="diseasesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mapa -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Mapa de Incidencias</h4>
                <div id="map" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=TU_CLAVE_DE_API"></script>
    <script>
        // Gráfico de estados de citas
        const ctxAppointments = document.getElementById('appointmentsStatusChart').getContext('2d');
        new Chart(ctxAppointments, {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'Completada', 'Cancelada'],
                datasets: [{
                    data: [<?= $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'pendiente'")->fetchColumn() ?>, 
                           <?= $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'concretada'")->fetchColumn() ?>, 
                           <?= $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'cancelada'")->fetchColumn() ?>],
                    backgroundColor: ['#FFA726', '#66BB6A', '#EF5350'],
                }]
            }
        });

        // Gráfico de enfermedades
        const diseaseData = <?= json_encode($diseasesStats) ?>;
        new Chart(document.getElementById('diseasesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: diseaseData.map(item => item.diagnosis),
                datasets: [{
                    label: 'Frecuencia',
                    data: diseaseData.map(item => item.count),
                    backgroundColor: '#42A5F5',
                }]
            }
        });

        // Mapa
        const mapData = <?= json_encode($mapData) ?>;
        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: { lat: 10.65, lng: -71.65 } // Coordenadas centrales de San Francisco
        });
        mapData.forEach(point => {
            new google.maps.Marker({
                position: { lat: parseFloat(point.latitude), lng: parseFloat(point.longitude) },
                map: map,
                title: point.address
            });
        });
    </script>

    <?php include '../shared/footer.php'; ?>
</body>
</html>
