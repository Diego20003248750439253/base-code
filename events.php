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
    <title>Mis Eventos</title>
</head>

<body>
    <h1>Tus Eventos Guardados</h1>

    <p>
        <a href="newEvents.php">Crear nuevo evento</a> | 
        <a href="events.php?logout=true">Cerrar sesión</a>
    </p>

    <hr>

    <?php if (empty($events)): ?>
        <p>No tienes ningún evento registrado todavía.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($events as $event): ?>
                <li>
                    <strong><?= htmlspecialchars($event->getTitle()) ?></strong><br>
                    <span><?= htmlspecialchars($event->getDescription()) ?></span><br>
                    <small>Desde: <?= htmlspecialchars($event->getStartDate()) ?> | Hasta: <?= htmlspecialchars($event->getEndDate()) ?></small>
                </li>
                <br>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
</body>
</html>