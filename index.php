<?php
session_start();

if (isset($_SESSION['user_id'])){
    header("Location: newEvents.php");
    exit();
}

$error = "";
$rememberedEmail = $_COOKIE['remember_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)){
        $error = "Por favor, rellena todos los campos.";
    } else {
        require_once __DIR__ . '/data-access/CalendarDataAccess.php';
        $dbFile = __DIR__ . '/calendar.db';
        $dao = new CalendarDataAccess($dbFile);

        $user = $dao->getUserByEmail($email);

        if ($user && password_verify($password, $user->getPassword())){
            $_SESSION['user_id'] = $user->getId();
            session_regenerate_id(true);

            if ($remember) {
                setcookie('remember_email', $email, time() + (86400 * 30), "/");
            } else {
                setcookie('remember_email', '', time() - 3600, "/");
            }

            header("Location: newEvents.php");
            exit();
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Agenda de Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm" style="max-width: 400px; width: 100%;">
            <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">Iniciar Sesión</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="index.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($rememberedEmail) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" <?= !empty($rememberedEmail) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="remember">Recuérdame</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-0">¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>