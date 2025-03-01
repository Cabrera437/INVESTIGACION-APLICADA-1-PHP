<?php
session_start();
if (!isset($_SESSION["token"])) {
    header("Location: login.php");
    exit();
}

$token = $_SESSION["token"];
$url_api = "http://localhost/proyecto/api/proyectos.php";

// Función para obtener proyectos
function obtenerProyectos($token, $url_api)
{
    $ch = curl_init($url_api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Manejo de creación de proyectos
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["crear"])) {
    $data = ["nombre" => $_POST["nombre"], "descripcion" => $_POST["descripcion"]];
    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\nAuthorization: Bearer $token",
            "method" => "POST",
            "content" => json_encode($data),
        ],
    ];
    file_get_contents($url_api, false, stream_context_create($options));
    header("Location: proyectos.php");
    exit();
}

// Manejo de actualización de proyectos
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["actualizar"])) {
    $data = ["id" => $_POST["id"], "nombre" => $_POST["nombre"], "descripcion" => $_POST["descripcion"]];
    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\nAuthorization: Bearer $token",
            "method" => "PUT",
            "content" => json_encode($data),
        ],
    ];
    file_get_contents($url_api, false, stream_context_create($options));
    header("Location: proyectos.php");
    exit();
}

// Manejo de eliminación de proyectos
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar"])) {
    $data = ["id" => $_POST["id"]];
    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\nAuthorization: Bearer $token",
            "method" => "DELETE",
            "content" => json_encode($data),
        ],
    ];
    file_get_contents($url_api, false, stream_context_create($options));
    header("Location: proyectos.php");
    exit();
}

$proyectos = obtenerProyectos($token, $url_api);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proyectos</title>
    <link rel="stylesheet" href="css/proyectos.css">
</head>
<body>
    <h2>Bienvenido a la Gestión de Proyectos</h2>

    <!-- Formulario para crear un nuevo proyecto -->
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del proyecto" required>
        <input type="text" name="descripcion" placeholder="Descripción" required>
        <button type="submit" name="crear">Crear Proyecto</button>
    </form>

    <!-- Listado de proyectos -->
    <h3>Proyectos</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($proyectos as $proyecto): ?>
        <tr>
            <td><?= $proyecto["id"] ?></td>
            <td><?= $proyecto["nombre"] ?></td>
            <td><?= $proyecto["descripcion"] ?></td>
            <td>
                <!-- Formulario para actualizar -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $proyecto["id"] ?>">
                    <input type="text" name="nombre" value="<?= $proyecto["nombre"] ?>" required>
                    <input type="text" name="descripcion" value="<?= $proyecto["descripcion"] ?>" required>
                    <button type="submit" name="actualizar">Actualizar</button>
                </form>

                <!-- Formulario para eliminar -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $proyecto["id"] ?>">
                    <button type="submit" name="eliminar" onclick="return confirm('¿Seguro que quieres eliminar este proyecto?')">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="logout.php" class="cerrarsesion">Cerrar sesión</a>
</body>
</html>
