<?php
session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    
    $startDate = str_replace('T', ' ', $_POST['start_date'] ?? '');
    $endDate = str_replace('T', ' ', $_POST['end_date'] ?? '');

    if (strlen($startDate) === 16) { $startDate .= ':00'; }
    if (strlen($endDate) === 16) { $endDate .= ':00'; }

    if (empty($title) || empty($startDate) || empty($endDate)) {
        $error = "Por favor, rellena los campos obligatorios.";
    } else {
        require_once __DIR__ . '/data-access/CalendarDataAccess.php';
        require_once __DIR__ . '/entities/Event.php';

        $dbFile = __DIR__ . '/calendar.db';
        $dao = new CalendarDataAccess($dbFile);

        $newEvent = new Event($_SESSION['user_id'], $title, $description, $startDate, $endDate);

        if ($dao->createEvent($newEvent)) {
            $success = "¡Evento creado con éxito!";
        } else {
            $error = "Error al guardar el evento.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="events.php"><i class="fa-regular fa-calendar-days me-2"></i>Mi Agenda</a>
            <div>
                <a href="events.php" class="btn btn-light btn-sm me-2">Mis Eventos</a>
                <a href="newEvents.php?logout=true" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4"><i class="fa-solid fa-plus me-2 text-primary"></i>Nuevo Evento</h3>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form action="newEvents.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Título *</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label">Inicio *</label>
                                    <input type="datetime-local" class="form-control" name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fin *</label>
                                    <input type="datetime-local" class="form-control" name="end_date" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="events.php" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" name="create_event" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>