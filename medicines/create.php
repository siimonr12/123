<!DOCTYPE html>
<html lang="es">
<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Registrar Medicamento</h1>
        <form method="POST" action="../../controllers/MedicinesController.php?action=create">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre del Medicamento</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Cantidad Disponible</label>
                <input type="number" name="quantity" id="quantity" class="form-control" min="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar Medicamento</button>
        </form>
    </div>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
