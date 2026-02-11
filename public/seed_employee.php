<?php
require_once __DIR__ . '/../src/Config/Database.php';

$database = new Database();
$db = $database->getConnection();

echo "<pre>";
echo "👷 Création du compte Employé et données de test...\n";

try {
    // 1. Créer l'employé (Role ID 3)
    $stmt = $db->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = 'employe@test.com'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $mdp = password_hash('test', PASSWORD_BCRYPT);
        // On insère avec role 3 (Employé)
        $sql = "INSERT INTO utilisateur (email, password, pseudo, id_role, credits) VALUES 
                ('employe@test.com', '$mdp', 'Modérateur bob', 3, 0)";
        $db->exec($sql);
        echo "✅ Compte Employé créé : employe@test.com / test\n";
    } else {
        echo "ℹ️ Compte Employé déjà existant.\n";
    }

    // 2. Créer quelques avis en attente (s'il n'y en a pas)
    // On a besoin d'utilisateurs et de covoiturages existants.
    // On va supposer que les IDs 1 et 2 existent (créés par les seeds précédents).
    
    // On vérifie s'il y a des avis en attente
    $stmt = $db->query("SELECT COUNT(*) FROM avis WHERE statut = 'EN_ATTENTE'");
    if ($stmt->fetchColumn() < 2) {
        // On insère des faux avis si covoiturage id 1 existe, sinon on ignore
        // Ceci est juste pour la démo, ça peut échouer si la base est vide
        $db->exec("INSERT INTO avis (commentaire, note, statut, id_covoiturage, id_auteur, id_destinataire) 
                   VALUES ('Super trajet, très calme.', 5, 'EN_ATTENTE', 1, 2, 1)");
        
        $db->exec("INSERT INTO avis (commentaire, note, statut, id_covoiturage, id_auteur, id_destinataire) 
                   VALUES ('Conduite un peu sportive...', 3, 'EN_ATTENTE', 1, 2, 1)");
                   
        echo "✅ Faux avis ajoutés pour le test.\n";
    }

    echo "\n✨ Terminé.";
    echo "\n<a href='index.php'>Retour à l'accueil</a>";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
echo "</pre>";
