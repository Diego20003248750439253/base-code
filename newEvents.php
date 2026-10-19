<?php
session_start();

// Cierre de sesión
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Control de acceso
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// Procesar la creación del evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    
    // Limpiar el formato datetime-local de HTML5
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

        $newEvent = new Event(
            $_SESSION['user_id'],
            $title,
            $description,
            $startDate,
            $endDate
        );

        if ($dao->createEvent($newEvent)) {
            $success = "¡Evento creado con éxito!";
        } else {
            $error = "Error al guardar el evento en la base de datos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nuevo Evento</title>
</head>
<body>
    <h1>Crear un nuevo evento</h1>
    
    <!-- Opciones de navegación y cierre de sesión -->
    <p>
        <a href="events.php">Ver mis eventos</a> | 
        <a href="newEvents.php?logout=true">Cerrar sesión</a>
    </p>

    <hr>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <form action="newEvents.php" method="POST">
        <label>Título:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Descripción:</label><br>
        <textarea name="description"></textarea><br><br>

        <label>Fecha y hora de inicio:</label><br>
        <input type="datetime-local" name="start_date" required><br><br>

        <label>Fecha y hora de fin:</label><br>
        <input type="datetime-local" name="end_date" required><br><br>

        <button type="submit" name="create_event">Guardar Evento</button>
    </form>
</body>
</html>