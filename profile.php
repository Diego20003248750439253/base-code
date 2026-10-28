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

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - Mi Agenda</title>
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
                <h2 class="card-title mb-4">Perfil del usuario</h2>

                <dl class="row mb-4">
                    <dt class="col-sm-4 text-muted">Correo electrónico:</dt>
                    <dd class="col-sm-8 fw-bold"><?= htmlspecialchars($user->getEmail()) ?></dd>

                    <dt class="col-sm-4 text-muted">Nombre:</dt>
                    <dd class="col-sm-8 fw-bold"><?= htmlspecialchars($user->getFirstName()) ?></dd>

                    <dt class="col-sm-4 text-muted">Apellidos:</dt>
                    <dd class="col-sm-8 fw-bold"><?= htmlspecialchars($user->getLastName()) ?></dd>

                    <dt class="col-sm-4 text-muted">Fecha de nacimiento:</dt>
                    <dd class="col-sm-8 fw-bold"><?= htmlspecialchars($user->getBirthdate()) ?></dd>

                    <dt class="col-sm-4 text-muted">Acerca de mí:</dt>
                    <dd class="col-sm-8"><?= nl2br(htmlspecialchars($user->getAbout() ?? '')) ?></dd>
                </dl>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="events.php" class="btn btn-secondary">Volver al listado</a>
                    <a href="edit-profile.php" class="btn btn-primary">Editar perfil</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>