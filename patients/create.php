<!DOCTYPE html>
<html lang="es">

<?php include '../shared/head.php'; ?>

<body>
    <?php include '../shared/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Registrar Paciente</h1>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="../../controllers/PatientsController.php?action=create" method="POST" class="mt-4">
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

                <!-- Documento de Identidad -->
                <div class="col-md-4 mb-3">
                    <label for="document_id" class="form-label">Documento de Identidad</label>
                    <input type="text" name="document_id" id="document_id" class="form-control" placeholder="Ingrese el documento">
                </div>

                <!-- Fecha de Nacimiento -->
                <div class="col-md-6 mb-3">
                    <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" name="birth_date" id="birth_date" class="form-control" required>
                </div>

                <!-- Edad -->
                <div class="col-md-6 mb-3">
                    <label for="age" class="form-label">Edad</label>
                    <input type="text" id="age" class="form-control" placeholder="Se calculará automáticamente" readonly>
                </div>

                <!-- Género -->
                <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">Género</label>
                    <select name="gender" id="gender" class="form-control" required>
                        <option value="">Seleccione el género</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <!-- Contacto -->
                <div class="col-md-6 mb-3">
                    <label for="contact_number" class="form-label">Número de Contacto</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="Ingrese el número de contacto" required>
                </div>

                <!-- Dirección -->
                <div class="col-md-12 mb-3">
                    <label for="address" class="form-label">Dirección</label>
                    <textarea name="address" id="address" class="form-control" placeholder="Ingrese la dirección" rows="2" required></textarea>
                </div>

                <!-- Es pediátrico -->
                <div class="col-md-6 mb-3">
                    <label for="is_pediatric" class="form-label">¿Es paciente pediátrico?</label>
                    <select name="is_pediatric" id="is_pediatric" class="form-control" required>
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

                <!-- Buscar Representante -->
                <div id="representative-section" class="d-none">
                    <h4 class="mt-4">Seleccionar Representante</h4>

                    <div class="col-md-12 mb-3">
                        <label for="representative_search" class="form-label">Buscar Representante (Nombre o Cédula)</label>
                        <input type="text" id="representative_search" class="form-control" placeholder="Ingrese nombre o cédula del representante">
                        <div id="representative_results" class="mt-2"></div>
                    </div>

                    <input type="hidden" name="representative_id" id="representative_id">
                </div>

                <!-- Botón de Registro -->
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Registrar Paciente</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('birth_date').addEventListener('change', function () {
            const birthDate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            document.getElementById('age').value = age;

            const isPediatric = document.getElementById('is_pediatric');
            const repSection = document.getElementById('representative-section');
            if (age < 18) {
                isPediatric.value = '1';
                repSection.classList.remove('d-none');
            } else {
                isPediatric.value = '0';
                repSection.classList.add('d-none');
            }
        });

        document.getElementById('representative_search').addEventListener('input', function () {
            const query = this.value;
            if (query.length > 2) {
                fetch(`../../controllers/PatientsController.php?action=search_representative&query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        const resultsContainer = document.getElementById('representative_results');
                        resultsContainer.innerHTML = '';
                        data.forEach(rep => {
                            const div = document.createElement('div');
                            div.textContent = `${rep.first_name} ${rep.last_name} - ${rep.document_id}`;
                            div.classList.add('list-group-item');
                            div.style.cursor = 'pointer';
                            div.onclick = () => {
                                document.getElementById('representative_id').value = rep.id;
                                document.getElementById('representative_search').value = `${rep.first_name} ${rep.last_name}`;
                                resultsContainer.innerHTML = '';
                            };
                            resultsContainer.appendChild(div);
                        });
                    });
            }
        });
    </script>

    <?php include '../shared/footer.php'; ?>
</body>

</html>
