<?php
//Maneja el registro de los usuarios, inicio de sesion y la generacion de los tokens

require '../vendor/autoload.php'; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

$secret_key = "RicardoMurcia";

$mysqli = new mysqli("localhost", "root", "", "gestiones_proyectos"); //Conexion a la BD
if ($mysqli->connect_error) {
    die(json_encode(["message" => "Error de conexión a la base de datos"]));
}

//Registrando el usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["register"])) {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["username"];
    $password = password_hash($data["password"], PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Usuario registrado correctamente"]);
    } else {
        echo json_encode(["message" => "Error al registrar usuario"]);
    }
    exit;
}

//Iniciando la sesion y generando el uso de JWT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["login"])) {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["username"];
    $password = $data["password"];

    $stmt = $mysqli->prepare("SELECT id, password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $payload = [
            "iss" => "mi_api",
            "iat" => time(),
            "exp" => time() + (5 * 60), // Expira en 5 minutos
            "sub" => $user_id
        ];
        $token = JWT::encode($payload, $secret_key, "HS256");
        echo json_encode(["token" => $token]);
    } else {
        echo json_encode(["message" => "Credenciales incorrectas"]);
    }
    exit;
}
?>