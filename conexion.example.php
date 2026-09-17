<?php

$host = "127.0.0.1";
$puerto = 3306;
$usuario = "root";
$contrasena = "TU_CONTRASENA_AQUI";
$basedatos = "Proyecto_biblioteca";

$conexion = new mysqli($host, $usuario, $contrasena, $basedatos, $puerto);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>