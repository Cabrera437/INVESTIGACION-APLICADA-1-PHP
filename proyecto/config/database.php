<?php
//Haciendo la conexion con la base de datos
class Database{
    private $host = "localhost";
    private $db_name = "gestiones_proyectos";
    private $username = "root";
    public $password = "";
    public $conn;

    public function getConnection(){
        $this->conn = null; //$this variable que usa para la conexion

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOEXCEPTION $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>