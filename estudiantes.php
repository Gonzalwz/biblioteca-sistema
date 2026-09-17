<?php
require "conexion.php";

// ---------- CREAR o ACTUALIZAR ----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $carne = $conexion->real_escape_string($_POST["carne"]);
    $nombre = $conexion->real_escape_string($_POST["nombre"]);
    $correo = $conexion->real_escape_string($_POST["correo"]);
    $estado = $conexion->real_escape_string($_POST["estado"]);

    if (!empty($_POST["id_estudiante"])) {
        // Actualizar un registro existente
        $id = (int) $_POST["id_estudiante"];
        $sql = "UPDATE estudiantes SET carne='$carne', nombre='$nombre', correo='$correo', estado='$estado' WHERE id_estudiante=$id";
    } else {
        // Insertar un registro nuevo
        $sql = "INSERT INTO estudiantes (carne, nombre, correo, estado) VALUES ('$carne', '$nombre', '$correo', '$estado')";
    }

    if (!$conexion->query($sql)) {
        $error = "Error al guardar: " . $conexion->error;
    }
    // Volvemos a cargar la página para mostrar la tabla actualizada
    header("Location: estudiantes.php");
    exit;
}

// ---------- ELIMINAR ----------
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $conexion->query("DELETE FROM estudiantes WHERE id_estudiante=$id");
    header("Location: estudiantes.php");
    exit;
}

// ---------- Si viene un ID por GET, cargamos sus datos para editar ----------
$editando = null;
if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $resultado = $conexion->query("SELECT * FROM estudiantes WHERE id_estudiante=$id");
    $editando = $resultado->fetch_assoc();
}

// ---------- LEER todos los registros ----------
$estudiantes = $conexion->query("SELECT * FROM estudiantes ORDER BY id_estudiante");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estudiantes - Proyecto Biblioteca</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 30px auto; padding: 0 15px; }
        h1 { color: #2c3e50; }
        nav a { margin-right: 12px; }
        form { background: #f4f4f4; padding: 15px; border-radius: 6px; margin-bottom: 25px; }
        input[type=text] { padding: 6px; width: 250px; margin-bottom: 10px; display: block; }
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
        <a href="estudiantes.php">Estudiantes</a>
        <!-- Aquí se agregan los links a las demás tablas cuando estén listas -->
    </nav>

    <h1>Estudiantes</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Formulario: sirve tanto para crear como para editar -->
    <form method="POST" action="estudiantes.php">
        <input type="hidden" name="id_estudiante" value="<?php echo $editando["id_estudiante"] ?? ""; ?>">

        <label>Carne:</label>
        <input type="text" name="carne" required
               value="<?php echo htmlspecialchars($editando["carne"] ?? ""); ?>">
        
        <label>Nombre:</label>
        <input type="text" name="nombre" required
               value="<?php echo htmlspecialchars($editando["nombre"] ?? ""); ?>">

        <label>Correo:</label>
        <input type="text" name="correo" required
               value="<?php echo htmlspecialchars($editando["correo"] ?? ""); ?>">

        <label>Estado:</label>
        <select name="estado" required>
            <option value="ACTIVO" <?php echo (($editando["estado"] ?? "ACTIVO") === "ACTIVO") ? "selected" : ""; ?>>
                ACTIVO
            </option>

            <option value="SUSPENDIDO" <?php echo (($editando["estado"] ?? "") === "SUSPENDIDO") ? "selected" : ""; ?>>
                SUSPENDIDO
            </option>
        </select>

        <button type="submit"><?php echo $editando ? "Actualizar" : "Agregar"; ?></button>
        <?php if ($editando): ?>
            <a href="estudiantes.php">Cancelar edición</a>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Carne</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php while ($fila = $estudiantes->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila["id_estudiante"]; ?></td>
                <td><?php echo htmlspecialchars($fila["carne"]); ?></td>
                <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
                <td><?php echo htmlspecialchars($fila["correo"]); ?></td>
                <td><?php echo htmlspecialchars($fila["estado"]); ?></td>
                <td>
                    <a class="btn-editar" href="estudiantes.php?editar=<?php echo $fila['id_estudiante']; ?>">Editar</a>
                    <a class="btn-eliminar" href="estudiantes.php?eliminar=<?php echo $fila['id_estudiante']; ?>"
                       onclick="return confirm('¿Seguro que quieres eliminar este estudiante?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>