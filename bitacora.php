<?php

require "conexion.php";

// Filtros (llegan por GET)
$desde       = $_GET["desde"]        ?? "";
$hasta        = $_GET["hasta"]        ?? "";
$tabla        = $_GET["tabla"]        ?? "";
$accion       = $_GET["accion"]       ?? "";
$autor        = trim($_GET["autor"]   ?? "");
$nacionalidad = $_GET["nacionalidad"] ?? "";

// WHERE dinamico: los "?" se llenan aparte (evita inyeccion SQL)
$condiciones = [];
$valores     = [];
$tipos       = "";

if ($desde !== "") {
    $condiciones[] = "fecha_hora >= ?";
    $valores[]     = $desde . " 00:00:00";
    $tipos        .= "s";
}
if ($hasta !== "") {
    $condiciones[] = "fecha_hora <= ?";
    $valores[]     = $hasta . " 23:59:59";
    $tipos        .= "s";
}
if ($tabla !== "") {
    $condiciones[] = "tabla = ?";
    $valores[]     = $tabla;
    $tipos        .= "s";
}
if ($accion !== "") {
    $condiciones[] = "accion = ?";
    $valores[]     = $accion;
    $tipos        .= "s";
}
if ($autor !== "") {
    $condiciones[] = "detalle LIKE ?";
    $valores[]     = "%" . $autor . "%";
    $tipos        .= "s";
}
if ($nacionalidad !== "") {
    $condiciones[] = "nacionalidad = ?";
    $valores[]     = $nacionalidad;
    $tipos        .= "s";
}

// Consulta (DATE_FORMAT separa fecha y hora)
$sql ="SELECT id_bitacora, tabla, accion, id_registro, detalle, nacionalidad,
               DATE_FORMAT(fecha_hora, '%d/%m/%Y') AS fecha,
               DATE_FORMAT(fecha_hora, '%H:%i:%s') AS hora
        FROM bitacora";

if ($condiciones) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

// Mas reciente primero, maximo 500
$sql .= " ORDER BY fecha_hora DESC, id_bitacora DESC LIMIT 500";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}
if ($valores) {
    $stmt->bind_param($tipos, ...$valores);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Lista de nacionalidades (de la bitacora, incluye autores borrados)
$nacionalidades = $conexion->query(
    "SELECT DISTINCT nacionalidad FROM bitacora
     WHERE nacionalidad IS NOT NULL ORDER BY nacionalidad"
);

$tablas_sistema = ["editoriales", "autores", "libros", "estudiantes",
                   "prestamos", "detalle_prestamo", "devoluciones"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bitacora - Sistema de Biblioteca</title>
<style>
    body { font-family: Georgia, "Times New Roman", serif; background: #FAF7F2;
           color: #1F2A44; max-width: 960px; margin: 0 auto; padding: 40px 20px; }
    .subtitulo { font-family: "Trebuchet MS", system-ui, sans-serif; font-size: 12px;
                 letter-spacing: 2px; text-transform: uppercase; color: #8A8477; margin: 0; }
    h1 { font-weight: normal; font-size: 34px; margin: 4px 0 14px; }
    nav { display: flex; flex-wrap: wrap; gap: 6px 22px; padding-bottom: 14px; margin-bottom: 28px;
          border-bottom: 1px solid #E4DFD5; font-family: "Trebuchet MS", system-ui, sans-serif; font-size: 13px; }
    nav a { color: #8A8477; text-decoration: none; }
    nav a:hover { color: #1F2A44; text-decoration: underline; }
    .encabezado { border-bottom: 2px solid #1F2A44; margin-bottom: 24px; }
    a { color: #1F2A44; }
    .volver { font-family: "Trebuchet MS", system-ui, sans-serif; font-size: 14px; }

    form { display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-end;
           background: #fff; border: 1px solid #E4DFD5; padding: 10px; margin-bottom: 14px; }
    label { display: block; font-family: "Trebuchet MS", system-ui, sans-serif;
            font-size: 11px; color: #8A8477; margin-bottom: 2px; }
    input, select { font-family: "Trebuchet MS", system-ui, sans-serif; font-size: 13px;
                    padding: 4px 6px; border: 1px solid #CFC8B8; background: #fff; color: #1F2A44; }
    input[type=text] { width: 110px; }
    button { font-family: "Trebuchet MS", system-ui, sans-serif; font-size: 13px;
             padding: 6px 14px; background: #1F2A44; color: #fff; border: 0; cursor: pointer; }
    .limpiar { font-family: "Trebuchet MS", system-ui, sans-serif; font-size: 13px; padding: 6px 2px; }

    .total { font-family: "Trebuchet MS", system-ui, sans-serif; font-size: 13px;
             color: #8A8477; margin-bottom: 8px; }
    table { width: 100%; border-collapse: collapse; background: #fff;
            font-family: "Trebuchet MS", system-ui, sans-serif; font-size: 14px; }
    th { text-align: left; padding: 9px 10px; background: #1F2A44; color: #fff; font-weight: normal; }
    td { padding: 9px 10px; border-bottom: 1px solid #E4DFD5; }
    .insert { color: #2E7D4F; font-weight: bold; }
    .update { color: #B08968; font-weight: bold; }
    .delete { color: #B23A3A; font-weight: bold; }
    .vacio  { text-align: center; color: #8A8477; padding: 24px; }
</style>
</head>
<body>

<nav>
    <a href="index.php">Inicio</a>
    <a href="editoriales.php">Editoriales</a>
    <a href="autores.php">Autores</a>
    <a href="libros.php">Libros</a>
    <a href="estudiantes.php">Estudiantes</a>
    <a href="prestamos.php">Prestamos</a>
    <a href="detalle_prestamo.php">Detalle de los Prestamos</a>
    <a href="devoluciones.php">Devoluciones</a>
</nav>

<div class="encabezado">
    <p class="subtitulo">Proyecto Base de Datos 1</p>
    <h1>Bitacora de cambios</h1>
</div>

<p class="volver"><a href="index.php">&larr; Volver al inicio</a></p>

<!-- Los campos recuerdan lo ultimo que se busco -->
<form method="GET" action="bitacora.php">
    <div>
        <label>Desde</label>
        <input type="date" name="desde" value="<?php echo htmlspecialchars($desde); ?>">
    </div>
    <div>
        <label>Hasta</label>
        <input type="date" name="hasta" value="<?php echo htmlspecialchars($hasta); ?>">
    </div>
    <div>
        <label>Tabla</label>
        <select name="tabla">
            <option value="">Todas</option>
            <?php foreach ($tablas_sistema as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo $tabla === $t ? "selected" : ""; ?>>
                    <?php echo $t; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>Accion</label>
        <select name="accion">
            <option value="">Todas</option>
            <?php foreach (["INSERT", "UPDATE", "DELETE"] as $a): ?>
                <option value="<?php echo $a; ?>" <?php echo $accion === $a ? "selected" : ""; ?>>
                    <?php echo $a; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>Autor / detalle</label>
        <input type="text" name="autor" placeholder="Ej: Garcia"
               value="<?php echo htmlspecialchars($autor); ?>">
    </div>
    <div>
        <label>Nacionalidad (autores)</label>
        <select name="nacionalidad">
            <option value="">Todas</option>
            <?php while ($n = $nacionalidades->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($n["nacionalidad"]); ?>"
                    <?php echo $nacionalidad === $n["nacionalidad"] ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($n["nacionalidad"]); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div>
        <button type="submit">Filtrar</button>
        <a class="limpiar" href="bitacora.php">Limpiar</a>
    </div>
</form>

<p class="total"><?php echo $resultado->num_rows; ?> registro(s) (maximo 500, los mas recientes primero)</p>

<table>
    <tr>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Tabla</th>
        <th>Accion</th>
        <th>ID</th>
        <th>Detalle</th>
        <th>Nacionalidad</th>
    </tr>
    <?php if ($resultado->num_rows === 0): ?>
        <tr><td class="vacio" colspan="7">No hay registros con esos filtros.</td></tr>
    <?php endif; ?>

    <?php while ($fila = $resultado->fetch_assoc()): ?>
    <tr class="fila">
        <td><?php echo htmlspecialchars($fila["fecha"]); ?></td>
        <td><?php echo htmlspecialchars($fila["hora"]); ?></td>
        <td><?php echo htmlspecialchars($fila["tabla"]); ?></td>
        <td class="<?php echo strtolower(htmlspecialchars($fila["accion"])); ?>">
            <?php echo htmlspecialchars($fila["accion"]); ?>
        </td>
        <td><?php echo (int) $fila["id_registro"]; ?></td>
        <td><?php echo htmlspecialchars($fila["detalle"] ?? ""); ?></td>
        <td><?php echo htmlspecialchars($fila["nacionalidad"] ?? "—"); ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>