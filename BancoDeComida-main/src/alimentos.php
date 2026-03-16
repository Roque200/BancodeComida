<?php 
require 'auth.php'; // Esto ya inicia la sesión de forma centralizada
if ($_SESSION['role_id'] != 2) die("Acceso denegado. Esta vista es solo para el Coordinador de Alimentos."); 
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="container mt-5 text-center">
    <div class="alert alert-warning shadow">
        <h1>Coordinador de Alimentos </h1>
        <a href='logout.php' class='btn btn-danger mt-3'>Cerrar Sesión</a>
        <a href="registrar_donacion.php" class="btn btn-success mt-3">Registrar Donación</a>
<a href="ver_donaciones.php" class="btn btn-primary mt-3">Ver Tabla de Donaciones</a>
    </div>
</div>