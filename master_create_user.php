<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'admin'; // Valor predeterminado: 'admin'

    if (empty($username) || empty($password)) {
        echo "Por favor, complete todos los campos.";
        exit();
    }

    // Verificar que el rol sea válido
    $validRoles = ['admin', 'doctor', 'receptionist', 'patient'];
    if (!in_array($role, $validRoles)) {
        echo "Rol inválido.";
        exit();
    }

    try {
        // Encriptar la contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insertar el usuario en la base de datos
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->execute();

        echo "Usuario creado exitosamente.";
    } catch (PDOException $e) {
        echo "Error al crear el usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master - Crear Usuario</title>
</head>
<body>
    <h1>Crear Usuario - Herramienta Master</h1>
    <form method="POST">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="role">Rol:</label>
        <select id="role" name="role">
            <option value="admin">Administrador</option>
            <option value="doctor">Doctor</option>
            <option value="receptionist">Recepcionista</option>
            <option value="patient">Paciente</option>
        </select>
        <br>
        <button type="submit">Crear Usuario</button>
    </form>
</body>
</html>
