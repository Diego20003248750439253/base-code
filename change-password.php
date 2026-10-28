<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/data-access/CalendarDataAccess.php';

$dbFile = __DIR__ . '/calendar.db';
$dao = new CalendarDataAccess($dbFile);

$userId = $_SESSION['user_id'];
$user = $dao->getUserById($userId);

if (!$user) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!password_verify($currentPassword, $user->getPassword())) {
        $error = "La contraseña actual no es correcta.";
    } elseif (strlen($newPassword) < 8 || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[a-zA-Z]/', $newPassword)) {
        $error = "La nueva contraseña debe tener al menos 8 caracteres y contener al menos un número y una letra.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Las nuevas contraseñas no coinciden.";
    } else {
        $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
        if ($dao->updateUser($user)) {
            session_regenerate_id(true);
            $success = "Contraseña modificada";
        } else {
            $error = "Ha ocurrido un error al actualizar la contraseña.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - Mi Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="bg-light">
    <div class="container py-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title mb-4">Cambiar contraseña</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <?= htmlspecialchars($success) ?>
                    </div>
                    <div class="mt-3">
                        <a href="events.php" class="btn btn-primary">Volver al listado de eventos</a>
                    </div>
                <?php else: ?>

                    <form action="change-password.php" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña actual *</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva contraseña (mín. 8 caracteres, letras y números) *</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Repetir nueva contraseña *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                            <a href="events.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
</body>
</html>