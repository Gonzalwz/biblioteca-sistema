<?php
// Datos de conexión al servidor MySQL (el mismo al que se conecta MySQL Workbench)
$host = "127.0.0.1";
$puerto = 3306;
$usuario = "root";
$contrasena = "123456"; // <-- reemplaza con tu contraseña real de MySQL
$basedatos = "Proyecto_biblioteca";

// Crear la conexión
$conexion = new mysqli($host, $usuario, $contrasena, $basedatos, $puerto);

// Verificar que la conexión sí se haya hecho
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// Para que los acentos (á, é, í, ó, ú, ñ) se guarden y muestren bien
$conexion->set_charset("utf8mb4");
?>
