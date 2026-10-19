<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: events.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $birthDate = $_POST['birth_date'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $about = $_POST['about'] ?? '';

    // Validaciones básicas
    if (empty($email) || empty($firstName) || empty($lastName) || empty($birthDate) || empty($password) || empty($confirmPassword)) {
        $error = "Por favor, rellena todos los campos obligatorios.";
    } elseif ($password !== $confirmPassword) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "La contraseña debe tener al menos 8 caracteres y contener letras y números.";
    } else {
        require_once __DIR__ . '/data-access/CalendarDataAccess.php';
        $dbFile = __DIR__ . '/calendar.db';
        $dao = new CalendarDataAccess($dbFile);

        if ($dao->getUserByEmail($email)) {
            $error = "Ya existe un usuario registrado con ese correo electrónico.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            require_once __DIR__ . '/entities/User.php';
            $newUser = new User($email, $passwordHash, $firstName, $lastName, $birthDate, $about);
            
            if ($dao->createUser($newUser)) {
                $success = "¡Usuario creado con éxito! Ya puedes <a href='index.php'>iniciar sesión</a>.";
            } else {
                $error = "Hubo un error al registrar el usuario en la base de datos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>
    <h2>Registro de nuevo usuario</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php else: ?>
        <form action="register.php" method="POST">
            <label>Correo electrónico:</label><br>
            <input type="email" name="email" required><br><br>

            <label>Nombre:</label><br>
            <input type="text" name="first_name" required><br><br>

            <label>Apellidos:</label><br>
            <input type="text" name="last_name" required><br><br>

            <label>Fecha de nacimiento:</label><br>
            <input type="date" name="birth_date" required><br><br>

            <label>Contraseña (mín. 8 caracteres, letras y números):</label><br>
            <input type="password" name="password" required><br><br>

            <label>Repetir contraseña:</label><br>
            <input type="password" name="confirm_password" required><br><br>

            <label>Acerca de mí:</label><br>
            <textarea name="about"></textarea><br><br>

            <button type="submit">Registrarse</button>
        </form>
    <?php endif; ?>

    <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión</a></p>
</body>
</html>