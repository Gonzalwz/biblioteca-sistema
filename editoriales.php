<?php
require "conexion.php";

// ---------- CREAR o ACTUALIZAR ----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $conexion->real_escape_string($_POST["nombre"]);
    $pais = $conexion->real_escape_string($_POST["pais"]);

    if (!empty($_POST["id_editorial"])) {
        // Actualizar un registro existente
        $id = (int) $_POST["id_editorial"];
        $sql = "UPDATE editoriales SET nombre='$nombre', pais='$pais' WHERE id_editorial=$id";
    } else {
        // Insertar un registro nuevo
        $sql = "INSERT INTO editoriales (nombre, pais) VALUES ('$nombre', '$pais')";
    }

    if (!$conexion->query($sql)) {
        $error = "Error al guardar: " . $conexion->error;
    }
    // Volvemos a cargar la página para mostrar la tabla actualizada
    header("Location: editoriales.php");
    exit;
}

// ---------- ELIMINAR ----------
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $conexion->query("DELETE FROM editoriales WHERE id_editorial=$id");
    header("Location: editoriales.php");
    exit;
}

// ---------- Si viene un ID por GET, cargamos sus datos para editar ----------
$editando = null;
if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $resultado = $conexion->query("SELECT * FROM editoriales WHERE id_editorial=$id");
    $editando = $resultado->fetch_assoc();
}

// ---------- LEER todos los registros ----------
$editoriales = $conexion->query("SELECT * FROM editoriales ORDER BY id_editorial");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editoriales - Proyecto Biblioteca</title>
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
        <a href="editoriales.php">Editoriales</a>
        <!-- Aquí se agregan los links a las demás tablas cuando estén listas -->
    </nav>

    <h1>Editoriales</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Formulario: sirve tanto para crear como para editar -->
    <form method="POST" action="editoriales.php">
        <input type="hidden" name="id_editorial" value="<?php echo $editando["id_editorial"] ?? ""; ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" required
               value="<?php echo htmlspecialchars($editando["nombre"] ?? ""); ?>">

        <label>País:</label>
        <input type="text" name="pais" required
               value="<?php echo htmlspecialchars($editando["pais"] ?? ""); ?>">

        <button type="submit"><?php echo $editando ? "Actualizar" : "Agregar"; ?></button>
        <?php if ($editando): ?>
            <a href="editoriales.php">Cancelar edición</a>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>País</th>
            <th>Acciones</th>
        </tr>
        <?php while ($fila = $editoriales->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila["id_editorial"]; ?></td>
                <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
                <td><?php echo htmlspecialchars($fila["pais"]); ?></td>
                <td>
                    <a class="btn-editar" href="editoriales.php?editar=<?php echo $fila['id_editorial']; ?>">Editar</a>
                    <a class="btn-eliminar" href="editoriales.php?eliminar=<?php echo $fila['id_editorial']; ?>"
                       onclick="return confirm('¿Seguro que quieres eliminar esta editorial?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
