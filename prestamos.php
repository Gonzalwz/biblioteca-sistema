<?php
require "conexion.php";

// ---------- CREAR o ACTUALIZAR ----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_estudiante = $conexion->real_escape_string($_POST["id_estudiante"]);
    $fecha_prestamo = $conexion->real_escape_string($_POST["fecha_prestamo"]);
    $fecha_devolucion_esperada = $conexion->real_escape_string($_POST["fecha_devolucion_esperada"]);

    if (!empty($_POST["id_prestamo"])) {
        // Actualizar un registro existente
        $id = (int) $_POST["id_prestamo"];
        $sql = "UPDATE prestamos SET id_estudiante='$id_estudiante', fecha_prestamo='$fecha_prestamo', fecha_devolucion_esperada='$fecha_devolucion_esperada' WHERE id_prestamo=$id";
    } else {
        // Insertar un registro nuevo
        $sql = "INSERT INTO prestamos (id_estudiante, fecha_prestamo, fecha_devolucion_esperada) VALUES ('$id_estudiante', '$fecha_prestamo', '$fecha_devolucion_esperada')";
    }

    if (!$conexion->query($sql)) {
        $error = "Error al guardar: " . $conexion->error;
    }
    // Volvemos a cargar la página para mostrar la tabla actualizada
    header("Location: prestamos.php");
    exit;
}

// ---------- ELIMINAR ----------
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $conexion->query("DELETE FROM prestamos WHERE id_prestamo=$id");
    header("Location: prestamos.php");
    exit;
}

// ---------- Si viene un ID por GET, cargamos sus datos para editar ----------
$editando = null;
if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $resultado = $conexion->query("SELECT * FROM prestamos WHERE id_prestamo=$id");
    $editando = $resultado->fetch_assoc();
}

// ---------- LEER todos los registros ----------
$estudiantes = $conexion->query("SELECT * FROM estudiantes ORDER BY nombre");
$prestamos = $conexion->query("
    SELECT prestamos.*, estudiantes.nombre, estudiantes.estado AS estado_estudiante
    FROM prestamos
    INNER JOIN estudiantes 
        ON prestamos.id_estudiante = estudiantes.id_estudiante
    ORDER BY prestamos.id_prestamo
");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prestamos - Proyecto Biblioteca</title>
    <style>
    body { font-family: Arial, sans-serif; max-width: 1000px; margin: 30px auto; padding: 0 15px; }
    h1 { color: #2c3e50; }
    nav a { margin-right: 12px; }
    form { background: #f4f4f4; padding: 15px; border-radius: 6px; margin-bottom: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px 25px; align-items: start; }
    .campo { display: flex; flex-direction: column; }
    .campo input, .campo select { padding: 6px; width: 100%; box-sizing: border-box; }
    button[type=submit] { grid-column: 1 / -1; justify-self: start; padding: 8px 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #2c3e50; color: white; }
    a.btn-editar { color: #2980b9; margin-right: 10px; }
    a.btn-eliminar { color: #c0392b; }
    .error { color: red; }
</style>
</head>
<body>

    <nav>
        <a href="index.php">Inicio</a>
        <a href="prestamos.php">Prestamos</a>
        <!-- Aquí se agregan los links a las demás tablas cuando estén listas -->
    </nav>

    <h1>Prestamos</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Formulario: sirve tanto para crear como para editar -->
    <form method="POST" action="prestamos.php">
    <input type="hidden" name="id_prestamo" value="<?php echo $editando["id_prestamo"] ?? ""; ?>">

    <div class="campo">
        <label>Estudiante:</label>
        <select name="id_estudiante" required>
            <option value="">Seleccione una estudiante</option>
            <?php while ($estudiante = $estudiantes->fetch_assoc()): ?>
                <option value="<?php echo $estudiante["id_estudiante"]; ?>"
                    <?php echo (($editando["id_estudiante"] ?? "") == $estudiante["id_estudiante"]) ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($estudiante["nombre"]); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="campo">
        <label>Fecha del prestamo:</label>
        <input type="date" name="fecha_prestamo" required
        value="<?php echo htmlspecialchars($editando["fecha_prestamo"] ?? ""); ?>">
    </div>

    <div class="campo">
        <label>Fecha devolucion esperada:</label>
        <input type="date" name="fecha_devolucion_esperada" required
        value="<?php echo htmlspecialchars($editando["fecha_devolucion_esperada"] ?? ""); ?>">
    </div>

    <button type="submit"><?php echo $editando ? "Actualizar" : "Agregar"; ?></button>
    <?php if ($editando): ?>
        <a href="prestamos.php">Cancelar edición</a>
    <?php endif; ?>
</form>

    <table>
        <tr>
            <th>ID</th>
            <th>Estudiante</th>
            <th>Fecha del prestamo</th>
            <th>Fecha devolucion esperada</th>
            <th>Estado del estudiante</th>
            <th>Acciones</th>
        </tr>
        <?php while ($fila = $prestamos->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila["id_prestamo"]; ?></td>
                <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
                <td><?php echo htmlspecialchars($fila["fecha_prestamo"]); ?></td>
                <td><?php echo htmlspecialchars($fila["fecha_devolucion_esperada"]); ?></td>
                <td><?php echo htmlspecialchars($fila["estado_estudiante"]); ?></td>
                <td>
                    <a class="btn-editar" href="prestamos.php?editar=<?php echo $fila['id_prestamo']; ?>">Editar</a>
                    <a class="btn-eliminar" href="prestamos.php?eliminar=<?php echo $fila['id_prestamo']; ?>"
                       onclick="return confirm('¿Seguro que quieres eliminar este préstamo?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>