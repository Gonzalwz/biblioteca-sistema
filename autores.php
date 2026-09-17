<?php
require "conexion.php";

// ---------- CREAR o ACTUALIZAR ----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $conexion->real_escape_string($_POST["nombre"]);
    $nacionalidad = $conexion->real_escape_string($_POST["nacionalidad"]);

    if (!empty($_POST["id_autor"])) {
        // Actualizar un registro existente
        $id = (int) $_POST["id_autor"];
        $sql = "UPDATE autores SET nombre='$nombre', nacionalidad='$nacionalidad' WHERE id_autor=$id";
    } else {
        // Insertar un registro nuevo
        $sql = "INSERT INTO autores (nombre, nacionalidad) VALUES ('$nombre', '$nacionalidad')";
    }

    if (!$conexion->query($sql)) {
        $error = "Error al guardar: " . $conexion->error;
    }
    // Volvemos a cargar la página para mostrar la tabla actualizada
    header("Location: autores.php");
    exit;
}

// ---------- ELIMINAR ----------
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $conexion->query("DELETE FROM autores WHERE id_autor=$id");
    header("Location: autores.php");
    exit;
}

// ---------- Si viene un ID por GET, cargamos sus datos para editar ----------
$editando = null;
if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $resultado = $conexion->query("SELECT * FROM autores WHERE id_autor=$id");
    $editando = $resultado->fetch_assoc();
}

// ---------- LEER todos los registros ----------
$autores = $conexion->query("SELECT * FROM autores ORDER BY id_autor");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autores - Proyecto Biblioteca</title>
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
        <a href="autores.php">Autores</a>
        <a href="libros.php">Libros</a>
        <a href="Estudiantes.php">Estudiantes</a>
        <a href="prestamos.php">Prestamos</a>
        <a href="detalle_prestamo.php">Detalle de los Prestamos</a>
        <a href="devoluciones.php">Devoluciones</a>
    </nav>

    <h1>Autores</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Formulario: sirve tanto para crear como para editar -->
    <form method="POST" action="autores.php">
        <input type="hidden" name="id_autor" value="<?php echo $editando["id_autor"] ?? ""; ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" required
               value="<?php echo htmlspecialchars($editando["nombre"] ?? ""); ?>">

        <label>Nacionalidad:</label>
        <input type="text" name="nacionalidad" required
               value="<?php echo htmlspecialchars($editando["nacionalidad"] ?? ""); ?>">

        <button type="submit"><?php echo $editando ? "Actualizar" : "Agregar"; ?></button>
        <?php if ($editando): ?>
            <a href="autores.php">Cancelar edición</a>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Nacionalidad</th>
            <th>Acciones</th>
        </tr>
        <?php while ($fila = $autores->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila["id_autor"]; ?></td>
                <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
                <td><?php echo htmlspecialchars($fila["nacionalidad"]); ?></td>
                <td>
                    <a class="btn-editar" href="autores.php?editar=<?php echo $fila['id_autor']; ?>">Editar</a>
                    <a class="btn-eliminar" href="autores.php?eliminar=<?php echo $fila['id_autor']; ?>"
                       onclick="return confirm('¿Seguro que quieres eliminar este autor?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
