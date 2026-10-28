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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    $about = trim($_POST['about'] ?? '');

    if (empty($email) || empty($name) || empty($surname) || empty($birthdate)) {
        $error = "Todos los campos obligatorios deben estar rellenados.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    } else {
        $existingUser = $dao->getUserByEmail($email);
        if ($existingUser && $existingUser->getId() !== $userId) {
            $error = "El correo electrónico ya está en uso por otro usuario.";
        } else {
            $d = DateTime::createFromFormat('Y-m-d', $birthdate);
            if (!$d || $d->format('Y-m-d') !== $birthdate) {
                $error = "La fecha de nacimiento no es válida.";
            } else {
                $user->setEmail($email);
                $user->setFirstName($name);
                $user->setLastName($surname);
                $user->setBirthdate($birthdate);
                $user->setAbout($about);

                if ($dao->updateUser($user)) {
                    header("Location: profile.php");
                    exit();
                } else {
                    $error = "Ha ocurrido un error al actualizar el perfil.";
                }
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
    <title>Editar Perfil - Mi Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="events.php">Mi Agenda</a>
            <div>
                <a href="new-event.php" class="btn btn-light btn-sm me-2">Nuevo Evento</a>
                <a href="profile.php" class="btn btn-outline-light btn-sm me-2">Mi Perfil</a>
                <a href="events.php?logout=true" class="btn btn-outline-danger btn-sm text-white border-danger">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <div class="container" style="max-width: 700px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title mb-4">Editar Perfil</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="edit-profile.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $user->getEmail()) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $user->getFirstName()) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="surname" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="surname" name="surname" value="<?= htmlspecialchars($_POST['surname'] ?? $user->getLastName()) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="birthdate" class="form-label">Fecha de nacimiento *</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?= htmlspecialchars($_POST['birthdate'] ?? $user->getBirthdate()) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="about" class="form-label">Acerca de mí</label>
                        <textarea class="form-control" id="about" name="about" rows="3"><?= htmlspecialchars($_POST['about'] ?? ($user->getAbout() ?? '')) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <div>
                            <a href="change-password.php" class="btn btn-outline-primary ms-2">Cambiar contraseña</a>
                            <a href="profile.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>