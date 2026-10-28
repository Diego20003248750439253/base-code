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
            $error = "Ya existe un usuario registrado con ese correo electrónico. Por favor inicia sesión";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Agenda de Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="bg-light">

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4">Registro</h3>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php else: ?>
                            <form action="register.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Correo electrónico *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label">Nombre *</label>
                                        <input type="text" class="form-control" name="first_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apellidos *</label>
                                        <input type="text" class="form-control" name="last_name" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Fecha de nacimiento *</label>
                                    <input type="date" class="form-control" name="birth_date" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Contraseña (mín. 8 caracteres, letras y números) *</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Repetir contraseña *</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Acerca de mí</label>
                                    <textarea class="form-control" name="about" rows="2"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3">Registrarse</button>
                            </form>

                            <div class="text-center">
                                <p class="mb-0">¿Ya tienes cuenta? <a href="index.php">Inicia sesión</a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>