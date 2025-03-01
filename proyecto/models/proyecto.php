<?php
//Maneja la logica para que haya acceso a la base de datos

class Proyecto{
    private $conn;
    private $table_name = "proyectos"; //Nombre de la TABLA de la base de datos

    public $id;
    public $nombre;
    public $descripcion;
    public $fecha_creacion;

    public function __construct($db) { //Variable $db contiene proceso de la conexion de la base de datos
        $this->conn = $db;
    }

    //Recibiendo el proyecto
    public function obtenerProyectos() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //Crear proyectos
    public function crearProyecto() {
        $query = "INSERT INTO " . $this->table_name . " (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->conn->prepare($query);
    
        //Aqui estara limpiando los datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
    
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
    
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    //Actualizando proyectos
    public function actualizarProyecto() {
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    //Eliminandolo
    public function eliminarProyecto() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
?>