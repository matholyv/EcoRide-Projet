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

        // Détection de l'environnement (Railway ou Local)
        // Railway fournit ces variables automatiquement
        if (getenv('MYSQLHOST')) {
            $this->host = getenv('MYSQLHOST');
            $this->db_name = getenv('MYSQLDATABASE');
            $this->username = getenv('MYSQLUSER');
            $this->password = getenv('MYSQLPASSWORD');
            $this->port = getenv('MYSQLPORT') ?: 3306;
        } else {
            // Configuration Locale (PC Windows)
            $this->host = "127.0.0.1";
            $this->db_name = "ecoride";
            $this->username = "root";
            $this->password = "";
            $this->port = 3306;
        }

        try {
            // Connexion MySQL avec Port explicite
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Options de sécurité et encodage
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("set names utf8mb4");

        } catch(PDOException $exception) {
            // En cas d'échec (ex: DB non créée en local), tentative de connexion au serveur seul (sans DB name)
            // Utile uniquement pour les scripts d'installation locale
            try {
                $dsn_no_db = "mysql:host=" . $this->host . ";port=" . $this->port . ";charset=utf8mb4";
                $this->conn = new PDO($dsn_no_db, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                // En production, on évite d'afficher l'erreur brute pour sécurité
                if (getenv('RAILWAY_ENVIRONMENT')) {
                    error_log("DB Connection Error: " . $e->getMessage());
                    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
                } else {
                    echo "Erreur de connexion MySQL : " . $e->getMessage();
                }
            }
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
