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

require_once __DIR__ . '/data-access/CalendarDataAccess.php';
require_once __DIR__ . '/entities/Event.php';

$dbFile = __DIR__ . '/calendar.db';
$dao = new CalendarDataAccess($dbFile);

$events = $dao->getEventsByUserId($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Eventos - Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="events.php">Mi Agenda</a>
            <div>
                <a href="new-event.php" class="btn btn-light btn-sm me-2">Nuevo Evento</a>
                <a href="events.php?logout=true" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h3 text-dark mb-0">Tus Eventos Guardados</h2>
        </div>

        <?php if (empty($events)): ?>
            <div class="alert alert-info text-center shadow-sm" role="alert">
                No tienes ningún evento registrado todavía.
            </div>

        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalEvents = count($events);
                                for ($i = 0; $i < $totalEvents; $i++): 
                                    $event = $events[$i];
                                ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($event->getTitle()) ?></td>
                                        <td class="text-muted"><?= htmlspecialchars($event->getDescription()) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($event->getStartDate()) ?></span></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($event->getEndDate()) ?></span></td>
                                        <td class="text-end">
                                            <a href="edit-event.php?id=<?= $event->getId() ?>" class="btn btn-outline-primary btn-sm me-1" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="delete-event.php?id=<?= $event->getId() ?>" class="btn btn-outline-danger btn-sm" title="Borrar">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>