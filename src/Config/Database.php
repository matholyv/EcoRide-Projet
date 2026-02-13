<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;

    public function __construct() {
        // On récupère les variables de Railway, ou on utilise localhost par défaut
        $this->host = getenv('MYSQLHOST') ?: '127.0.0.1';
        $this->db_name = getenv('MYSQLDATABASE') ?: 'ecoride';
        $this->username = getenv('MYSQLUSER') ?: 'root';
        $this->password = getenv('MYSQLPASSWORD') ?: '';
        $this->port = getenv('MYSQLPORT') ?: '3306';
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Pour le débug, tu peux décommenter la ligne suivante :
            // echo "Erreur : " . $exception->getMessage(); 
            die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
        }
        return $this->conn;
    }
}