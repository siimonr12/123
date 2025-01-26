<?php
// Incluir configuraciones y conexión a la base de datos
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

// Incluir archivos compartidos para el diseño
include __DIR__ . '/../shared/head.php';
include __DIR__ . '/../shared/header.php';
?>

<div class="container mt-4">
    <h1 class="text-center">Detalles del Médico</h1>

    <?php if (!isset($doctor)): ?>
        <div class="alert alert-danger">
            Médico no encontrado. Por favor, verifica la información.
        </div>
    <?php else: ?>
        <!-- Información general -->
        <div class="card mb-4">
            <div class="card-header">Información del Médico</div>
            <div class="card-body">
                <p><strong>Nombre:</strong> <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></p>
                <p><strong>Cédula:</strong> <?= htmlspecialchars($doctor['national_id']) ?></p>
                <p><strong>Especialidad:</strong> <?= htmlspecialchars($doctor['specialty']) ?></p>
                <p><strong>Citas diarias máximas:</strong> <?= htmlspecialchars($doctor['max_daily_appointments']) ?></p>
                <p><strong>Días disponibles:</strong> <?= htmlspecialchars($doctor['available_days']) ?></p>
            </div>
        </div>

        <!-- Citas del día -->
        <div class="card mb-4">
            <div class="card-header">Citas del Día</div>
            <div class="card-body">
                <?php if (!empty($dailyAppointments)): ?>
                    <ul class="list-group">
                        <?php foreach ($dailyAppointments as $appointment): ?>
                            <li class="list-group-item">
                                <strong>Paciente:</strong> <?= htmlspecialchars($appointment['patient_name']) ?>
                                <br>
                                <strong>Fecha:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?>
                                <br>
                                <strong>Descripción:</strong> <?= htmlspecialchars($appointment['description']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No hay citas para hoy.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Histórico de citas -->
        <div class="card mb-4">
            <div class="card-header">Histórico de Citas</div>
            <div class="card-body">
                <?php if (!empty($historicalAppointments)): ?>
                    <ul class="list-group">
                        <?php foreach ($historicalAppointments as $appointment): ?>
                            <li class="list-group-item">
                                <strong>Paciente:</strong> <?= htmlspecialchars($appointment['patient_name']) ?>
                                <br>
                                <strong>Fecha:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?>
                                <br>
                                <strong>Estado:</strong> <?= htmlspecialchars($appointment['status']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No hay citas registradas.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="card mb-4">
            <div class="card-header">Estadísticas de Citas</div>
            <div class="card-body">
                <canvas id="appointmentsChart"></canvas>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('appointmentsChart').getContext('2d');
const appointmentsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Citas Concretadas', 'Citas Totales'],
        datasets: [{
            label: 'Número de Citas',
            data: [<?= $completedAppointments ?>, <?= count($historicalAppointments) ?>],
            backgroundColor: ['#4CAF50', '#2196F3'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

<?php include __DIR__ . '/../shared/footer.php'; ?>
