<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;

    public function getConnection() {
        $this->conn = null;

        $this->host = getenv('MYSQLHOST') ?: '127.0.0.1';
        $this->db_name = getenv('MYSQLDATABASE') ?: 'railway';
        $this->username = getenv('MYSQLUSER') ?: 'root';
        $this->password = getenv('MYSQLPASSWORD') ?: '';
        $this->port = getenv('MYSQLPORT') ?: '3306';

        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            die("Erreur de connexion SQL : " . $exception->getMessage() . 
                " (Host: " . $this->host . ", DB: " . $this->db_name . ")");
        }

        return $this->conn;
    }

    public static function getGlobalParam($key, $default) {
        try {
            $inst = new Database();
            $conn = $inst->getConnection();
            if (!$conn) return $default;
            
            $stmt = $conn->prepare("SELECT valeur FROM parametre WHERE propriete = ?");
            $stmt->execute([$key]);
            $val = $stmt->fetchColumn();
            return ($val !== false) ? $val : $default;
        } catch (Exception $e) {
            return $default;
        }
    }
}