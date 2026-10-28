<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/data-access/CalendarDataAccess.php';
require_once __DIR__ . '/entities/Event.php';

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

$eventId = $_GET['id'] ?? null;
$error = '';
$event = null;
$hasAccess = true;

if ($eventId) {
    $event = $dao->getEventById((int)$eventId);
}

if (!$event || $event->getUserId() !== $userId) {
    $hasAccess = false;
}

if ($hasAccess && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    if (empty($title) || empty($startDate) || empty($endDate)) {
        $error = "Todos los campos obligatorios deben estar rellenados.";
    } elseif (strtotime($endDate) <= strtotime($startDate)) {
        $error = "La fecha y hora de fin debe ser posterior a la fecha y hora de inicio.";
    } else {
        $event->setTitle($title);
        $event->setDescription($description);
        $event->setStartDate($startDate);
        $event->setEndDate($endDate);

        if ($dao->updateEvent($event)) {
            header("Location: events.php");
            exit();
        } else {
            $error = "Error al actualizar el evento.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento - Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm" style="max-width: 400px; width: 100%;"> 
            <div class="card-body p-4">
                <h2 class="card-title mb-4">Editar evento</h2>

                <?php if (!$hasAccess): ?>
                    <div class="alert alert-danger" role="alert">
                        No se puede acceder al evento porque no existe o porque no tiene permisos para verlo.
                    </div>

                    <div class="text-center mt-3">
                        <a href="events.php" class="btn btn-primary">Volver al listado de eventos</a>
                    </div>
                <?php else: ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="edit-event.php?id=<?= htmlspecialchars($eventId) ?>" method="POST">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título *</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($event->getTitle()) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($event->getDescription()) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Fecha y hora de inicio *</label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="<?= date('Y-m-d\TH:i', strtotime($event->getStartDate())) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">Fecha y hora de fin *</label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="<?= date('Y-m-d\TH:i', strtotime($event->getEndDate())) ?>" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            <a href="events.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>

                <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html>