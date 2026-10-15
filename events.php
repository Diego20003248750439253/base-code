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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Calendario de Eventos</title>
</head>
<body>
    <h1>Bienvenido a tu Calendario</h1>
    <p>¡Has iniciado sesión correctamente!</p>

    <a href="events.php?logout=true">Cerrar sesión</a>
</body>
</html>