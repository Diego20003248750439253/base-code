<?php
session_start();

// Si ya hay sesión iniciada, redirige directamente a la creación de eventos
if (isset($_SESSION['user_id'])){
    header("Location: newEvents.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)){
        $error = "Por favor, rellena todos los campos.";
    }else {
        require_once __DIR__ . '/data-access/CalendarDataAccess.php';
        $dbFile = __DIR__ . '/calendar.db';
        $dao = new CalendarDataAccess($dbFile);

        $user = $dao->getUserByEmail($email);

        if ($user && password_verify($password, $user->getPassword())){
            $_SESSION['user_id'] = $user->getId();
            session_regenerate_id(true);

            // Redirige directamente a newEvents.php tras hacer login con éxito
            header("Location: newEvents.php");
            exit();
        }else{
            $error = "Correo o contraseña incorrectos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($error)):?>
        <p style="color:red"> <?=$error ?></p>
    <?php endif ?>

    <form action="index.php" method="POST">
        <label>Correo electrónico: </label>
        <input type="email" name="email" required><br><br>

        <label>Contraseña</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Entrar</button>
    </form>

    <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
    
</body>
</html>