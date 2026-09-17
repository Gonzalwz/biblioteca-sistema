<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyecto Biblioteca - CRUD</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #FAF7F2;
            color: #1F2A44;
            max-width: 640px;
            margin: 0 auto;
            padding: 20px 24px;
            min-height: 100vh;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        header {
            border-bottom: 2px solid #1F2A44;
            padding-bottom: 18px;
            margin-bottom: 8px;
        }
        h1 {
            font-size: 30px;
            font-weight: normal;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }
        .subtitulo {
            font-family: 'Trebuchet MS', system-ui, sans-serif;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #8A8477;
            margin: 0;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 30px 0 0 0;
        }
        li {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 4px;
            border-bottom: 1px solid #E4DFD5;
            font-family: 'Trebuchet MS', system-ui, sans-serif;
        }
        .indice {
            font-family: Georgia, serif;
            font-size: 13px;
            color: #B08968;
            border: 1px solid #B08968;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        a {
            color: #1F2A44;
            text-decoration: none;
            font-size: 17px;
        }
        a:hover { text-decoration: underline; }
        .pendiente {
            font-size: 17px;
            color: #B7B2A6;
        }
        .pendiente .indice {
            color: #C9C3B6;
            border-color: #DCD7CB;
        }
        .etiqueta {
            margin-left: auto;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #C9C3B6;
        }
        footer {
            font-family: 'Trebuchet MS', system-ui, sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8A8477;
            text-align: center;
            margin-top: auto;
            padding-top: 30px;
            border-top: 1px solid #E4DFD5;
        }
    </style>
</head>
<body>

    <header>
        <p class="subtitulo">Proyecto Base de Datos 1</p>
        <h1>Sistema de Biblioteca</h1>
    </header>

    <ul>
        <li><span class="indice">A</span> <a href="editoriales.php">Editoriales</a></li>
        <li><span class="indice">B</span> <a href="autores.php">Autores</a></li>
        <li><span class="indice">C</span> <a href="libros.php">Libros</a></li>
        <li><span class="indice">D</span> <a href="estudiantes.php">Estudiantes</a></li>
        <li><span class="indice">E</span> <a href="prestamos.php">Prestamos</a></li>
        <li><span class="indice">F</span> <a href="detalle_prestamo.php">Detalle de los Prestamos</a></li>
        <li><span class="indice">G</span> <a href="devoluciones.php">Devoluciones</a></li>
    </ul>

    <footer>
    Universidad Mariano Galvez De Guatemala | Alex Ricardo Ronaldo Gonzalez Vasquez
    </footer>

</body>
</html>
