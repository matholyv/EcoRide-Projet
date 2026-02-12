<?php
require_once __DIR__ . '/../src/Config/Database.php';

try {
    // 1. Connexion au serveur MySQL sans choisir de base (pour pouvoir la créer)
    $pdo = new PDO("mysql:host=127.0.0.1", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Création de la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ecoride CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Base de données 'ecoride' créée ou déjà existante.<br>";

    // 3. Connexion à la base ecoride
    $pdo->exec("USE ecoride");

    // 4. Lecture et exécution du fichier schema.sql
    $sqlFile = __DIR__ . '/../sql/schema.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        echo "Tables créées avec succès (schema.sql).<br>";
    } else {
        echo "Erreur : Fichier schema.sql introuvable.<br>";
    }

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
echo "Installation terminée ! <a href='seed.php'>Cliquez ici pour remettre les fausses données (Seed)</a>";
