<?php
//Endpoints donde devolvera los proyectos en formato JSON

require '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

$secret_key = "RicardoMurcia";

$mysqli = new mysqli("localhost", "root", "", "gestiones_proyectos");//Se conecta a la base de datos
if ($mysqli->connect_error) {
    die(json_encode(["message" => "Error de conexión a la base de datos"]));
}

// Función para verificar el token
function verificarToken() {
    global $secret_key;
    $headers = apache_request_headers();

    if (!isset($headers["Authorization"])) {
        echo json_encode(["message" => "Acceso denegado, token requerido"]);
        http_response_code(401);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers["Authorization"]);

    try {
        return JWT::decode($token, new Key($secret_key, "HS256"));
    } catch (Exception $e) {
        echo json_encode(["message" => "Token inválido o expirado"]);
        http_response_code(401);
        exit;
    }
}

// Verificar token antes de permitir acceso
$usuario = verificarToken();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $result = $mysqli->query("SELECT * FROM proyectos");
    $proyectos = [];

    while ($row = $result->fetch_assoc()) {
        $proyectos[] = $row;
    }

    echo json_encode($proyectos);
} 

elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["nombre"]) && isset($data["descripcion"])) {
        $stmt = $mysqli->prepare("INSERT INTO proyectos (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $data["nombre"], $data["descripcion"]);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Proyecto creado"]);
        } else {
            echo json_encode(["message" => "Error al crear el proyecto"]);
        }
    } else {
        echo json_encode(["message" => "Datos incompletos"]);
    }
} 

elseif ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["id"]) && isset($data["nombre"]) && isset($data["descripcion"])) {
        $stmt = $mysqli->prepare("UPDATE proyectos SET nombre=?, descripcion=? WHERE id=?");
        $stmt->bind_param("ssi", $data["nombre"], $data["descripcion"], $data["id"]);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Proyecto actualizado"]);
        } else {
            echo json_encode(["message" => "Error al actualizar el proyecto"]);
        }
    } else {
        echo json_encode(["message" => "Datos incompletos"]);
    }
} 

elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["id"])) {
        $stmt = $mysqli->prepare("DELETE FROM proyectos WHERE id=?");
        $stmt->bind_param("i", $data["id"]);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Proyecto eliminado"]);
        } else {
            echo json_encode(["message" => "Error al eliminar el proyecto"]);
        }
    } else {
        echo json_encode(["message" => "ID requerido"]);
    }
} 

else {
    echo json_encode(["message" => "Método no permitido"]);
    http_response_code(405);
}