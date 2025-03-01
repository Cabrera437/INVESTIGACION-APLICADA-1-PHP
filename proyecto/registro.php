<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = [
        "username" => $_POST["username"],
        "password" => $_POST["password"]
    ];

    $json_data = json_encode($data);
    $url = "http://localhost/proyecto/api/auth.php?register";

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

    if (isset($response["message"]) && $response["message"] === "Usuario registrado correctamente") {
        header("Location: login.php");
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
    <title>Registro</title>
    <link rel="stylesheet" href="css/registro_estilo.css">
</head>
<body>
    <div class="form-container">
        <h2>Registro</h2>
        <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="crear un Usuario o ingresar un correo" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>
