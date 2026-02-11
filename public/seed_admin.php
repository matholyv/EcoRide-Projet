<?php
require_once __DIR__ . '/../src/Config/Database.php';

$database = new Database();
$db = $database->getConnection();

echo "<pre>";
echo "👑 Création du compte Administrateur...\n";

try {
    // 1. Créer l'admin (Role ID 4)
    $stmt = $db->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = 'admin@test.com'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $mdp = password_hash('test', PASSWORD_BCRYPT);
        // On insère avec role 4 (Administrateur)
        $sql = "INSERT INTO utilisateur (email, password, pseudo, id_role, credits) VALUES 
                ('admin@test.com', '$mdp', 'Administrateur', 4, 1000)";
        $db->exec($sql);
        echo "✅ Compte Admin créé : admin@test.com / test\n";
    } else {
        echo "ℹ️ Compte Admin déjà existant.\n";
    }
    
    echo "\n✨ Terminé.";
    echo "\n<a href='index.php'>Retour à l'accueil</a>";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
echo "</pre>";
