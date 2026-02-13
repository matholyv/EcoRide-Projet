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

        // DÉTECTION DE L'ENVIRONNEMENT (Railway vs Local)
        if (getenv('MYSQLHOST')) {
            // Configuration RAILWAY (Production)
            $this->host = getenv('MYSQLHOST');
            $this->db_name = getenv('MYSQLDATABASE');
            $this->username = getenv('MYSQLUSER');
            $this->password = getenv('MYSQLPASSWORD');
            $this->port = getenv('MYSQLPORT') ?: 3306;
        } else {
            // Configuration LOCALE (Wamp/Xampp)
            $this->host = "127.0.0.1";
            $this->db_name = "ecoride"; // Remettez le nom de votre base locale ici
            $this->username = "root";
            $this->password = "";
            $this->port = 3306;
        }

        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("set names utf8mb4");

        } catch(PDOException $exception) {
            // Affiche l'erreur complète pour aider au débogage (à retirer en prod si sensible)
            die("Erreur de connexion SQL : " . $exception->getMessage() . 
                " <br> (Host: " . $this->host . ", DB: " . $this->db_name . ")");
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