<?php
// Active l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/Config/Database.php';

echo "<h1>Installation de la Base de Données EcoRide (MySQL)</h1>";

$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        $sqlFile = __DIR__ . '/../sql/schema.sql';
        if (!file_exists($sqlFile)) {
            die("<p style='color:red'>Erreur : Le fichier schema.sql est introuvable.</p>");
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Exécution pour MySQL
        $db->exec($sql);
        
        echo "<p style='color:green; font-weight:bold;'>✅ Succès ! Base MySQL installée.</p>";
        echo "<p>Vous pouvez maintenant <a href='seed.php'>Lancer seed.php</a> pour ajouter des données de test.</p>";
        echo "<p>Puis aller sur <a href='index.php'>l'accueil</a>.</p>";
        
    } catch (PDOException $e) {
        echo "<p style='color:red'>Erreur SQL : " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>Impossible de se connecter à MySQL.</p>";
}
