<?php
require "conexion.php";

// ---------- CREAR o ACTUALIZAR ----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_prestamo = $conexion->real_escape_string($_POST["id_prestamo"]);
    $fecha_devolucion_real = $conexion->real_escape_string($_POST["fecha_devolucion_real"]);
    $mora_paga = $conexion->real_escape_string($_POST["mora_paga"]);

    if (!empty($_POST["id_devolucion"])) {
        $id = (int) $_POST["id_devolucion"];
        $sql = "UPDATE devoluciones SET id_prestamo='$id_prestamo',
                fecha_devolucion_real='$fecha_devolucion_real',
                mora_paga='$mora_paga'
                WHERE id_devolucion=$id";
    } else {
        $sql = "INSERT INTO devoluciones
                (id_prestamo, fecha_devolucion_real, mora_paga)
                VALUES ('$id_prestamo', '$fecha_devolucion_real', '$mora_paga')";
    }

    if (!$conexion->query($sql)) {
        $error = "Error al guardar: " . $conexion->error;
    }

    header("Location: devoluciones.php");
    exit;
}

// ---------- ELIMINAR ----------
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $conexion->query("DELETE FROM devoluciones WHERE id_devolucion=$id");
    header("Location: devoluciones.php");
    exit;
}

// ---------- EDITAR ----------
$editando = null;

if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $resultado = $conexion->query("SELECT * FROM devoluciones WHERE id_devolucion=$id");
    $editando = $resultado->fetch_assoc();
}

// ---------- DATOS PARA LOS SELECT ----------
$prestamos = $conexion->query("
    SELECT prestamos.id_prestamo, estudiantes.nombre
    FROM prestamos
    INNER JOIN estudiantes
        ON prestamos.id_estudiante = estudiantes.id_estudiante
    ORDER BY prestamos.id_prestamo
");

// ---------- LEER DEVOLUCIONES ----------
$devoluciones = $conexion->query("
    SELECT devoluciones.*, estudiantes.nombre, prestamos.fecha_devolucion_esperada,
           estudiantes.estado AS estado_estudiante
    FROM devoluciones
    INNER JOIN prestamos
        ON devoluciones.id_prestamo = prestamos.id_prestamo
    INNER JOIN estudiantes
        ON prestamos.id_estudiante = estudiantes.id_estudiante
    ORDER BY devoluciones.id_devolucion
");
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devoluciones - Proyecto Biblioteca</title>
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
        <a href="editoriales.php">Editoriales</a>
        <a href="autores.php">Autores</a>
        <a href="libros.php">Libros</a>
        <a href="Estudiantes.php">Estudiantes</a>
        <a href="prestamos.php">Prestamos</a>
        <a href="detalle_prestamo.php">Detalle de los Prestamos</a>
        <a href="devoluciones.php">Devoluciones</a>
    </nav>

    <h1>Devoluciones</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Formulario: sirve tanto para crear como para editar -->
<form method="POST" action="devoluciones.php">

    <input type="hidden" name="id_devolucion"
           value="<?php echo $editando["id_devolucion"] ?? ""; ?>">

    <div class="campo">
        <label>Préstamo:</label>
        <select name="id_prestamo" required>
            <option value="">Seleccione un préstamo</option>

            <?php while ($prestamo = $prestamos->fetch_assoc()): ?>
                <option value="<?php echo $prestamo["id_prestamo"]; ?>"
                    <?php echo (($editando["id_prestamo"] ?? "") == $prestamo["id_prestamo"]) ? "selected" : ""; ?>>
                    Préstamo #<?php echo $prestamo["id_prestamo"]; ?>
                    - <?php echo htmlspecialchars($prestamo["nombre"]); ?>
                </option>
            <?php endwhile; ?>

        </select>
    </div>

    <div class="campo">
        <label>Fecha de devolución:</label>
        <input type="date" name="fecha_devolucion_real" required
               value="<?php echo htmlspecialchars($editando["fecha_devolucion_real"] ?? ""); ?>">
    </div>

    <div class="campo">
        <label>Mora pagada:</label>
        <input type="number" name="mora_paga" min="0" step="0.01" value="<?php echo htmlspecialchars($editando["mora_paga"] ?? "0.00"); ?>">
    </div>

    <button type="submit">
        <?php echo $editando ? "Actualizar" : "Agregar"; ?>
    </button>

    <?php if ($editando): ?>
        <a href="devoluciones.php">Cancelar edición</a>
    <?php endif; ?>

</form>

    <table>
        <tr>
            <th>ID</th>
            <th>Préstamo</th>
            <th>Estudiante</th>
            <th>Fecha devolución</th>
            <th>Fecha esperada</th>
            <th>Mora pagada</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>

        <?php while ($fila = $devoluciones->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila["id_devolucion"]; ?></td>

                <td><?php echo htmlspecialchars($fila["id_prestamo"]); ?></td>

                <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>

                <td><?php echo htmlspecialchars($fila["fecha_devolucion_real"]); ?></td>

                <td><?php echo htmlspecialchars($fila["fecha_devolucion_esperada"]); ?></td>

                <td><?php echo htmlspecialchars($fila["mora_paga"]); ?></td>

                <td><?php echo htmlspecialchars($fila["estado_estudiante"]); ?></td>

                <td>
                    <a class="btn-editar"
                    href="devoluciones.php?editar=<?php echo $fila['id_devolucion']; ?>">
                        Editar
                    </a>

                    <a class="btn-eliminar"
                    href="devoluciones.php?eliminar=<?php echo $fila['id_devolucion']; ?>"
                    onclick="return confirm('¿Seguro que quieres eliminar esta devolución?');">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
