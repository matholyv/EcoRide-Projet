<?php
class Database {
    private $host = "127.0.0.1"; // Retour à l'IP car localhost est bloqué au niveau des permissions MySQL
    private $db_name = "ecoride";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Connexion MySQL
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Tentative de connexion sans base de données (pour la création)
            try {
                $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                echo "Erreur de connexion MySQL : " . $e->getMessage();
            }
        }

        return $this->conn;
    }

    public static function getGlobalParam($key, $default) {
        try {
            $inst = new Database();
            $conn = $inst->getConnection();
            $stmt = $conn->prepare("SELECT valeur FROM parametre WHERE propriete = ?");
            $stmt->execute([$key]);
            $val = $stmt->fetchColumn();
            return ($val !== false) ? $val : $default;
        } catch (Exception $e) {
            return $default;
        }
    }
}
