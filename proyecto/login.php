<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = [
        "username" => $_POST["username"],
        "password" => $_POST["password"]
    ];

    $json_data = json_encode($data);
    $url = "http://localhost/proyecto/api/auth.php?login";

    $options = [
        "http" => [
            "header" => "Content-Type: application/json",
            "method" => "POST",
            "content" => $json_data
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);

    if (isset($response["token"])) {
        $_SESSION["token"] = $response["token"];
        header("Location: proyectos.php");
        exit;
    }

    $error = $response["message"] ?? "Error desconocido";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/inicio_sesion.css">
</head>
<body>
    <div class="form-container">
        <h2>Iniciar Sesión</h2>
        <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Ingrese su usuario o correo electronico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>