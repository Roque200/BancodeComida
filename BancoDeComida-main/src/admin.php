<?php
require 'auth.php';
require 'db.php';
if ($_SESSION['role_id'] != 1) die("Acceso denegado.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'create') {
        $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role_id) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['username'], $hash, $_POST['role_id']]);
    } elseif ($_POST['action'] == 'toggle') {
        $stmt = $pdo->prepare("UPDATE users SET activo = NOT activo WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    } elseif ($_POST['action'] == 'edit_role') {
        $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        $stmt->execute([$_POST['role_id'], $_POST['id']]);
    }
}
$users = $pdo->query("SELECT u.*, r.nombre as rol FROM users u JOIN roles r ON u.role_id = r.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Panel Administrador 🌾 <a href='logout.php' class='btn btn-danger btn-sm float-end'>Salir</a></h2>
        
        <form method="POST" class="d-flex gap-2 mb-4 p-3 bg-light border">
            <input type="hidden" name="action" value="create">
            <input type="text" name="username" placeholder="Nuevo Usuario" required class="form-control">
            <input type="password" name="password" placeholder="Clave" required class="form-control">
            <select name="role_id" class="form-control">
                <option value="1">Admin</option><option value="2">Coord. Alimentos</option><option value="3">Coord. Rutas</option>
            </select>
            <button type="submit" class="btn btn-success">Crear</button>
        </form>

        <table class="table table-bordered text-center">
            <tr><th>Usuario</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['username'] ?></td>
                <td>
                    <form method="POST" class="d-flex gap-1">
                        <input type="hidden" name="action" value="edit_role">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <select name="role_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="1" <?= $u['role_id']==1?'selected':'' ?>>Admin</option>
                            <option value="2" <?= $u['role_id']==2?'selected':'' ?>>Alimentos</option>
                            <option value="3" <?= $u['role_id']==3?'selected':'' ?>>Rutas</option>
                        </select>
                    </form>
                </td>
                <td><?= $u['activo'] ? '🟢 Activo' : '🔴 Inactivo' ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit" class="btn btn-sm <?= $u['activo'] ? 'btn-warning' : 'btn-primary' ?>">
                            <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>