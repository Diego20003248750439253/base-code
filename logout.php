<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrar Sesión - Agenda de eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm" style="max-width: 400px; width: 100%;"> 
            <div class="card-body p-4">
                <h2 class="card-title mb-4">Cerrar sesión</h2>
                <p class="text-muted mb-4">¿Seguro que desea desconectar?</p>

                <form action="logout.php" method="POST">
                    <div class="d-grid gap-2">
                        <button type="submit" name="confirm" value="yes" class="btn btn-danger">Sí, desconectar</button>
                        <button type="submit" name="confirm" value="no" class="btn btn-secondary">No, volver al listado de eventos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>