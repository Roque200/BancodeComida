<?php
require 'auth.php';
require 'db.php';

$query = "SELECT d.folio_seguimiento, d.fecha, d.donante, p.nombre as producto, dd.cantidad 
          FROM donaciones d 
          JOIN donacion_detalles dd ON d.id = dd.donacion_id 
          JOIN productos p ON dd.producto_id = p.id";
$donaciones = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>📦 Lista de Donaciones</h2>
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-success">
                <tr><th>Folio (UUID)</th><th>Fecha</th><th>Donante</th><th>Producto</th><th>Cantidad</th></tr>
            </thead>
            <tbody>
                <?php foreach($donaciones as $d): ?>
                <tr>
                    <td><?= $d['folio_seguimiento'] ?></td>
                    <td><?= $d['fecha'] ?></td>
                    <td><?= $d['donante'] ?></td>
                    <td><?= $d['producto'] ?></td>
                    <td><?= $d['cantidad'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="alimentos.php" class="btn btn-secondary">Volver</a>
    </div>
</body>
</html>