<?php
require 'auth.php';
if ($_SESSION['role_id'] != 2) die("Acceso denegado.");

// Datos simulados del inventario (en sesión para persistencia durante la sesión)
if (!isset($_SESSION['inventario'])) {
    $_SESSION['inventario'] = [
        ['id' => 1, 'nombre' => 'Arroz',       'tipo' => 'Granos',   'cantidad' => 150, 'unidad' => 'kg',  'fecha_caducidad' => '2025-08-10', 'estado' => 'disponible'],
        ['id' => 2, 'nombre' => 'Frijol',       'tipo' => 'Granos',   'cantidad' => 80,  'unidad' => 'kg',  'fecha_caducidad' => '2025-07-01', 'estado' => 'disponible'],
        ['id' => 3, 'nombre' => 'Leche en polvo','tipo' => 'Lácteos', 'cantidad' => 12,  'unidad' => 'kg',  'fecha_caducidad' => '2025-04-05', 'estado' => 'por vencer'],
        ['id' => 4, 'nombre' => 'Atún en lata', 'tipo' => 'Proteína', 'cantidad' => 5,   'unidad' => 'pz',  'fecha_caducidad' => '2025-03-20', 'estado' => 'crítico'],
        ['id' => 5, 'nombre' => 'Aceite',       'tipo' => 'Aceites',  'cantidad' => 30,  'unidad' => 'lt',  'fecha_caducidad' => '2026-01-15', 'estado' => 'disponible'],
        ['id' => 6, 'nombre' => 'Avena',        'tipo' => 'Granos',   'cantidad' => 8,   'unidad' => 'kg',  'fecha_caducidad' => '2025-05-10', 'estado' => 'crítico'],
        ['id' => 7, 'nombre' => 'Azúcar',       'tipo' => 'Otros',    'cantidad' => 60,  'unidad' => 'kg',  'fecha_caducidad' => '2026-06-30', 'estado' => 'disponible'],
    ];
}

$mensaje = '';
$error    = '';

// Procesar entrada o salida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $id       = (int) $_POST['producto_id'];
    $cantidad = (float) $_POST['cantidad'];
    $accion   = $_POST['accion'];

    foreach ($_SESSION['inventario'] as &$producto) {
        if ($producto['id'] === $id) {
            if ($accion === 'entrada') {
                $producto['cantidad'] += $cantidad;
                $mensaje = "✅ Entrada registrada: +{$cantidad} {$producto['unidad']} de {$producto['nombre']}.";
            } elseif ($accion === 'salida') {
                if ($cantidad > $producto['cantidad']) {
                    $error = "❌ No hay suficiente stock de {$producto['nombre']} (disponible: {$producto['cantidad']} {$producto['unidad']}).";
                } else {
                    $producto['cantidad'] -= $cantidad;
                    $mensaje = "✅ Salida registrada: -{$cantidad} {$producto['unidad']} de {$producto['nombre']}.";
                }
            }
            // Recalcular estado según cantidad
            if (!$error) {
                if ($producto['cantidad'] <= 0) {
                    $producto['estado'] = 'crítico';
                } elseif ($producto['cantidad'] <= 10) {
                    $producto['estado'] = 'crítico';
                } elseif (strtotime($producto['fecha_caducidad']) <= strtotime('+30 days')) {
                    $producto['estado'] = 'por vencer';
                } else {
                    $producto['estado'] = 'disponible';
                }
            }
            break;
        }
    }
    unset($producto);
}

// Filtros
$filtro_tipo   = $_GET['tipo']   ?? '';
$filtro_estado = $_GET['estado'] ?? '';
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';

$inventario_filtrado = array_filter($_SESSION['inventario'], function ($p) use ($filtro_tipo, $filtro_estado, $filtro_fecha_desde, $filtro_fecha_hasta) {
    if ($filtro_tipo   && $p['tipo']   !== $filtro_tipo)   return false;
    if ($filtro_estado && $p['estado'] !== $filtro_estado) return false;
    if ($filtro_fecha_desde && $p['fecha_caducidad'] < $filtro_fecha_desde) return false;
    if ($filtro_fecha_hasta && $p['fecha_caducidad'] > $filtro_fecha_hasta) return false;
    return true;
});

$tipos = array_unique(array_column($_SESSION['inventario'], 'tipo'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario | Banco de Alimentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge-disponible { background-color: #198754; }
        .badge-critico    { background-color: #dc3545; }
        .badge-porvencer  { background-color: #fd7e14; }
    </style>
</head>
<body class="bg-light p-4">
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>📦 Dashboard de Inventario</h2>
        <a href="alimentos.php" class="btn btn-secondary btn-sm">← Volver</a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Formulario Entrada / Salida -->
    <div class="card shadow-sm mb-4 border-success">
        <div class="card-header bg-success text-white"><strong>Registrar Movimiento</strong></div>
        <div class="card-body">
            <form method="POST" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Producto</label>
                    <select name="producto_id" class="form-select" required>
                        <?php foreach ($_SESSION['inventario'] as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['nombre'] ?> (<?= $p['unidad'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" step="0.01" min="0.01" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo de movimiento</label>
                    <select name="accion" class="form-select" required>
                        <option value="entrada">📥 Entrada</option>
                        <option value="salida">📤 Salida</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Registrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Tipo de producto</label>
            <select name="tipo" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= $t ?>" <?= $filtro_tipo === $t ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="disponible"  <?= $filtro_estado === 'disponible'  ? 'selected' : '' ?>>Disponible</option>
                <option value="crítico"     <?= $filtro_estado === 'crítico'     ? 'selected' : '' ?>>Crítico</option>
                <option value="por vencer"  <?= $filtro_estado === 'por vencer'  ? 'selected' : '' ?>>Por vencer</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Caducidad desde</label>
            <input type="date" name="fecha_desde" class="form-control" value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">Caducidad hasta</label>
            <input type="date" name="fecha_hasta" class="form-control" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-md-1">
            <a href="inventario.php" class="btn btn-outline-secondary w-100">Limpiar</a>
        </div>
    </form>

    <!-- Tabla de inventario -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-success text-center">
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Cantidad actual</th>
                        <th>Unidad</th>
                        <th>Fecha de caducidad</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php if (empty($inventario_filtrado)): ?>
                        <tr><td colspan="6" class="text-muted p-3">No hay productos que coincidan con los filtros.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inventario_filtrado as $p): 
                            $badge = match($p['estado']) {
                                'disponible' => 'badge-disponible',
                                'crítico'    => 'badge-critico',
                                default      => 'badge-porvencer',
                            };
                        ?>
                        <tr>
                            <td class="text-start"><?= htmlspecialchars($p['nombre']) ?></td>
                            <td><?= htmlspecialchars($p['tipo']) ?></td>
                            <td><strong><?= $p['cantidad'] ?></strong></td>
                            <td><?= htmlspecialchars($p['unidad']) ?></td>
                            <td><?= $p['fecha_caducidad'] ?></td>
                            <td><span class="badge <?= $badge ?>"><?= ucfirst($p['estado']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
