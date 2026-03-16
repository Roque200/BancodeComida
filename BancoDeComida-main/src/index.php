<?php
session_start();
require 'db.php';

// Expiración por inactividad (120 seg para pruebas)
$timeout = 120;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset(); session_destroy();
    $error = "Sesión expirada por inactividad.";
}
$_SESSION['last_activity'] = time();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    // Validación de credenciales con hash
    if ($user && password_verify($_POST['password'], $user['password_hash']) && $user['activo']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        
        // Redirección por rol
        if ($user['role_id'] == 1) {
            header("Location: admin.php");
        } elseif ($user['role_id'] == 2) {
            header("Location: alimentos.php");
        } elseif ($user['role_id'] == 3) {
            header("Location: rutas.php");
        }
        exit;
    } else {
        $error = "Credenciales inválidas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Banco de Alimentos | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container text-center" style="max-width: 400px;">
        <div class="card shadow border-success">
            <div class="card-header bg-success text-white">
                <h4> Banco de Alimentos</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Usuario" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>