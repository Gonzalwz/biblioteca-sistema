<?php
require "conexion.php";

// ---------- CREAR o ACTUALIZAR ----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $conexion->real_escape_string($_POST["titulo"]);
    $isbn = $conexion->real_escape_string($_POST["isbn"]);
    $anio = $conexion->real_escape_string($_POST["anio_publicacion"]);
    $stock = $conexion->real_escape_string($_POST["stock"]);
    $id_editorial = $conexion->real_escape_string($_POST["id_editorial"]);
    $id_autor = $conexion->real_escape_string($_POST["id_autor"]);

    if (!empty($_POST["id_libro"])) {
        // Actualizar un registro existente
        $id = (int) $_POST["id_libro"];
        $sql = "UPDATE libros SET titulo='$titulo', isbn='$isbn', anio_publicacion='$anio', stock='$stock', id_editorial='$id_editorial', id_autor='$id_autor' WHERE id_libro=$id";
    } else {
        // Insertar un registro nuevo
        $sql = "INSERT INTO libros (titulo, isbn, anio_publicacion, stock, id_editorial, id_autor) VALUES ('$titulo', '$isbn', '$anio', '$stock', '$id_editorial', '$id_autor')";
    }

    if (!$conexion->query($sql)) {
        $error = "Error al guardar: " . $conexion->error;
    }
    // Volvemos a cargar la página para mostrar la tabla actualizada
    header("Location: libros.php");
    exit;
}

// ---------- ELIMINAR ----------
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];
    $conexion->query("DELETE FROM libros WHERE id_libro=$id");
    header("Location: libros.php");
    exit;
}

// ---------- Si viene un ID por GET, cargamos sus datos para editar ----------
$editando = null;
if (isset($_GET["editar"])) {
    $id = (int) $_GET["editar"];
    $resultado = $conexion->query("SELECT * FROM libros WHERE id_libro=$id");
    $editando = $resultado->fetch_assoc();
}

// ---------- LEER todos los registros ----------
$editoriales = $conexion->query("SELECT * FROM editoriales ORDER BY nombre");
$autores = $conexion->query("SELECT * FROM autores ORDER BY nombre");
$libros = $conexion->query("
    SELECT l.*, e.nombre AS nombre_editorial, a.nombre AS nombre_autor
    FROM libros l
    JOIN editoriales e ON l.id_editorial = e.id_editorial
    JOIN autores a ON l.id_autor = a.id_autor
    ORDER BY l.id_libro
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libros - Proyecto Biblioteca</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1300px; margin: 30px auto; padding: 0 15px; }
        h1 { color: #2c3e50; }
        nav a { margin-right: 12px; }
        form { background: #f4f4f4; padding: 15px; border-radius: 6px; margin-bottom: 25px; }
        form { background: #f4f4f4; padding: 15px; border-radius: 6px; margin-bottom: 25px;
        display: grid; grid-template-columns: 1fr 1fr; gap: 15px 25px; align-items: start; }
        .campo { display: flex; flex-direction: column; }
        .campo input, .campo select { padding: 6px; width: 100%; }
        button[type=submit] { grid-column: 1 / -1; justify-self: start; padding: 8px 20px; }
        input[type=text], input[type=number], select { padding: 6px; width: 250px; margin-bottom: 10px; display: block; }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px 15px;
            text-align: left;
            word-wrap: break-word;
            vertical-align: middle;
        }

        /* Anchos para que no se amontone */
        th:nth-child(1), td:nth-child(1) { width: 5%; }   /* ID */
        th:nth-child(2), td:nth-child(2) { width: 20%; }  /* Titulo */
        th:nth-child(3), td:nth-child(3) { width: 12%; }  /* ISBN */
        th:nth-child(4), td:nth-child(4) { width: 10%; }  /* Año */
        th:nth-child(5), td:nth-child(5) { width: 7%; }   /* Stock */
        th:nth-child(6), td:nth-child(6) { width: 18%; }  /* Editorial */
        th:nth-child(7), td:nth-child(7) { width: 15%; }  /* Autor */
        th:nth-child(8), td:nth-child(8) { width: 13%; }  /* Acciones */

        td a {
            display: inline-block;
            margin-right: 8px;
        }
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

    <h1>Libros</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Formulario: sirve tanto para crear como para editar -->
    <form method="POST" action="libros.php">
    <input type="hidden" name="id_libro" value="<?php echo $editando["id_libro"] ?? ""; ?>">

    <div class="campo">
        <label>Titulo:</label>
        <input type="text" name="titulo" required
               value="<?php echo htmlspecialchars($editando["titulo"] ?? ""); ?>">
    </div>

    <div class="campo">
        <label>ISBN:</label>
        <input type="text" name="isbn" required
               value="<?php echo htmlspecialchars($editando["isbn"] ?? ""); ?>">
    </div>

    <div class="campo">
        <label>Año de publicación:</label>
        <input type="number" name="anio_publicacion" min="1501" required
               value="<?php echo htmlspecialchars($editando["anio_publicacion"] ?? ""); ?>">
    </div>

    <div class="campo">
        <label>Stock:</label>
        <input type="number" name="stock" min="0" required
               value="<?php echo htmlspecialchars($editando["stock"] ?? ""); ?>">
    </div>

    <div class="campo">
        <label>Editorial:</label>
        <select name="id_editorial" required>
            <option value="">Seleccione una editorial</option>
            <?php while ($editorial = $editoriales->fetch_assoc()): ?>
                <option value="<?php echo $editorial["id_editorial"]; ?>"
                    <?php echo (($editando["id_editorial"] ?? "") == $editorial["id_editorial"]) ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($editorial["nombre"]); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="campo">
        <label>Autor:</label>
        <select name="id_autor" required>
            <option value="">Seleccione un autor</option>
            <?php while ($autor = $autores->fetch_assoc()): ?>
                <option value="<?php echo $autor["id_autor"]; ?>"
                    <?php echo (($editando["id_autor"] ?? "") == $autor["id_autor"]) ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($autor["nombre"]); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <button type="submit"><?php echo $editando ? "Actualizar" : "Agregar"; ?></button>
    <?php if ($editando): ?>
        <a href="libros.php">Cancelar edición</a>
    <?php endif; ?>
</form>

    <table>
        <tr>
            <th>ID</th>
            <th>Titulo</th>
            <th>ISBN</th>
            <th>Año de publicación</th>
            <th>Stock</th>
            <th>Editorial</th>
            <th>Autor</th>
            <th>Acciones</th>
        </tr>
        <?php while ($fila = $libros->fetch_assoc()): ?>
            <tr>
                <td><?php echo $fila["id_libro"]; ?></td>
                <td><?php echo htmlspecialchars($fila["titulo"]); ?></td>
                <td><?php echo htmlspecialchars($fila["isbn"]); ?></td>
                <td><?php echo htmlspecialchars($fila["anio_publicacion"]); ?></td>
                <td><?php echo htmlspecialchars($fila["stock"]); ?></td>
                <td><?php echo htmlspecialchars($fila["nombre_editorial"]); ?></td>
                <td><?php echo htmlspecialchars($fila["nombre_autor"]); ?></td>
                <td>
                    <a class="btn-editar" href="libros.php?editar=<?php echo $fila['id_libro']; ?>">Editar</a>
                    <a class="btn-eliminar" href="libros.php?eliminar=<?php echo $fila['id_libro']; ?>"
                       onclick="return confirm('¿Seguro que quieres eliminar este libro?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>