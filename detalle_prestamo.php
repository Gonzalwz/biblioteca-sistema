<?php
require "conexion.php";

// ---------- CREAR o ACTUALIZAR ----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_prestamo = $conexion->real_escape_string($_POST["id_prestamo"]);
    $id_libro = $conexion->real_escape_string($_POST["id_libro"]);
    $cantidad = $conexion->real_escape_string($_POST["cantidad"]);

    if (!empty($_POST["id_detalle"])) {
        // Actualizar un registro existente
        $id = (int) $_POST["id_detalle"];
        $sql = "UPDATE detalle_prestamo SET id_prestamo='$id_prestamo', id_libro='$id_libro', cantidad='$cantidad' WHERE id_detalle=$id";
    } else {
        // Insertar un registro nuevo
        $sql = "INSERT INTO detalle_prestamo (id_prestamo, id_libro, cantidad) VALUES ('$id_prestamo', '$id_libro', '$cantidad')";
    }

    if (!$conexion->query($sql)) {
        $error = "Error al guardar: " . $conexion->error;
    }
    // Volvemos a cargar la página para mostrar la tabla actualizada
    header("Location: detalle_prestamo.php");
    exit;
}

// ---------- ELIMINAR ----------
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $conexion->query("DELETE FROM detalle_prestamo WHERE id_detalle=$id");
    header("Location: detalle_prestamo.php");
    exit;
}

// ---------- Si viene un ID por GET, cargamos sus datos para editar ----------
$editando = null;
if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $resultado = $conexion->query("SELECT * FROM detalle_prestamo WHERE id_detalle=$id");
    $editando = $resultado->fetch_assoc();
}

// ---------- LEER todos los registros ----------
$prestamos = $conexion->query("SELECT id_prestamo FROM prestamos ORDER BY id_prestamo");
$libros = $conexion->query("SELECT id_libro, titulo FROM libros ORDER BY titulo");
$detalle_prestamo = $conexion->query("
    SELECT detalle_prestamo.*, libros.titulo, estudiantes.nombre AS nombre_estudiante
    FROM detalle_prestamo
    INNER JOIN libros
        ON detalle_prestamo.id_libro = libros.id_libro
    INNER JOIN prestamos
        ON detalle_prestamo.id_prestamo = prestamos.id_prestamo
    INNER JOIN estudiantes
        ON prestamos.id_estudiante = estudiantes.id_estudiante
    ORDER BY detalle_prestamo.id_detalle
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
    <form method="POST" action="detalle_prestamo.php">
    <input type="hidden" name="id_detalle" value="<?php echo $editando["id_detalle"] ?? ""; ?>">

    <div class="campo">
        <label>Prestamo:</label>
        <select name="id_prestamo" required>
            <option value="">Seleccione un préstamo</option>
            <?php while ($prestamo = $prestamos->fetch_assoc()): ?>
                <option value="<?php echo $prestamo["id_prestamo"]; ?>"
                    <?php echo (($editando["id_prestamo"] ?? "") == $prestamo["id_prestamo"]) ? "selected" : ""; ?>>
                    Préstamo #<?php echo $prestamo["id_prestamo"]; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="campo">
        <label>Libros:</label>
        <select name="id_libro" required>
            <option value="">Seleccione un libro</option>
            <?php while ($libro = $libros->fetch_assoc()): ?>
                <option value="<?php echo $libro["id_libro"]; ?>"
                    <?php echo (($editando["id_libro"] ?? "") == $libro["id_libro"]) ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($libro["titulo"]); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="campo">
        <label>Cantidad:</label>
        <input type="number" name="cantidad" min="1" required
        value="<?php echo htmlspecialchars($editando["cantidad"] ?? ""); ?>">
    </div>

    <button type="submit"><?php echo $editando ? "Actualizar" : "Agregar"; ?></button>
    <?php if ($editando): ?>
        <a href="detalle_prestamo.php">Cancelar edición</a>
    <?php endif; ?>
</form>

    <table>
        <tr>
            <th>ID</th>
            <th>Préstamo</th>
            <th>Libro</th>
            <th>Cantidad</th>
            <th>Acciones</th>
        </tr>
        <?php while ($fila = $detalle_prestamo->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila["id_detalle"]; ?></td>
                <td>
                    <?php echo htmlspecialchars($fila["id_prestamo"]); ?>
                    (<?php echo htmlspecialchars($fila["nombre_estudiante"]); ?>)
                </td>
                <td><?php echo htmlspecialchars($fila["titulo"]); ?></td>
                <td><?php echo htmlspecialchars($fila["cantidad"]); ?></td>
                <td>
                    <a class="btn-editar" href="detalle_prestamo.php?editar=<?php echo $fila['id_detalle']; ?>">Editar</a>
                    <a class="btn-eliminar" href="detalle_prestamo.php?eliminar=<?php echo $fila['id_detalle']; ?>"
                    onclick="return confirm('¿Seguro que quieres eliminar este detalle?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>