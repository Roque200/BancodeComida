<?php 
require 'auth.php'; 
if ($_SESSION['role_id'] != 3) die("Acceso denegado. Esta vista es solo para el Coordinador de Rutas."); 
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="container mt-5 text-center">
    <div class="alert alert-info shadow">
        <h1>Coordinador de Rutas </h1>
        <a href='logout.php' class='btn btn-danger mt-3'>Cerrar Sesión</a>
    </div>
</div>