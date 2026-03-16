<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Insertar la donación generando el folio con UUID()
        $stmt = $pdo->prepare("INSERT INTO donaciones (folio_seguimiento, donante) VALUES (UUID(), ?)");
        $stmt->execute([$_POST['donante']]);
        $donacion_id = $pdo->lastInsertId();

        // 2. Insertar el detalle del producto
        $stmtDetalle = $pdo->prepare("INSERT INTO donacion_detalles (donacion_id, producto_id, cantidad, fecha_caducidad) VALUES (?, ?, ?, ?)");
        $stmtDetalle->execute([$donacion_id, $_POST['producto_id'], $_POST['cantidad'], $_POST['caducidad']]);

        $pdo->commit();
        $mensaje = "¡Donación registrada con éxito!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}

// Obtener productos para el formulario
$productos = $pdo->query("SELECT * FROM productos")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container" style="max-width: 500px;">
        <div class="card shadow border-success">
            <div class="card-header bg-success text-white"><h4>📦 Registrar Donación</h4></div>
            <div class="card-body">
                <?php if(isset($mensaje)) echo "<div class='alert alert-success'>$mensaje</div>"; ?>
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                
                <form method="POST">
                    <input type="text" name="donante" class="form-control mb-2" placeholder="Nombre del Donante" required>
                    
                    <select name="producto_id" class="form-control mb-2" required>
                        <option value="">Selecciona un producto...</option>
                        <?php foreach($productos as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['nombre'] ?> (<?= $p['unidad_medida'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="number" name="cantidad" step="0.01" class="form-control mb-2" placeholder="Cantidad" required>
                    <label class="text-muted small">Fecha de Caducidad:</label>
                    <input type="date" name="caducidad" class="form-control mb-3" required>
                    
                    <button type="submit" class="btn btn-success w-100">Guardar Donación</button>
                    <a href="alimentos.php" class="btn btn-secondary w-100 mt-2">Volver</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>