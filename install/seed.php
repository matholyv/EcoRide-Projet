<?php
require_once __DIR__ . '/../src/Config/Database.php';

$database = new Database();
$db = $database->getConnection();

echo "<pre>";
echo "Seeding Ecoride database\n";

try {
    // 1. Récupérer l'utilisateur EcoDriver (créé par schema.sql)
    $stmt = $db->query("SELECT id_utilisateur FROM utilisateur WHERE email = 'test@test.com'");
    $id_conducteur = $stmt->fetchColumn();

    if (!$id_conducteur) {
        die("❌ Erreur : L'utilisateur test@test.com n'existe pas. Veuillez lancer reinstall_db.php d'abord.\n");
    }
    echo "👤 Utilisateur EcoDriver trouvé (ID $id_conducteur).\n";

    // On utilise cet ID pour la suite
    $id_utilisateur = $id_conducteur;

    // Récupérer l'ID de Tesla (On sait qu'elle est ID 1 grâce à schema.sql)
    $id_tesla = 1;

    // On verifie si la voiture existe deja 
    $stmt = $db->query("SELECT id_voiture FROM voiture WHERE immatriculation = 'AB-123-CD'");
    $id_voiture = $stmt->fetchColumn();

    if(!$id_voiture) {
        $db->prepare("INSERT INTO voiture (modele, immatriculation, energie, couleur, nombre_places, id_marque, id_utilisateur) 
                      VALUES ('Model 3', 'AB-123-CD', 'Electrique', 'Blanc', 4, ?, ?)")->execute([$id_tesla, $id_utilisateur]);
        $id_voiture = $db->lastInsertId();
        echo "🚗 Voiture créée (Tesla Model 3).\n";
    } else {
        echo "ℹ️ Voiture Tesla déjà existante.\n";
    }

    // 2. Créer des trajets
    $today = date('Y-m-d');
    
    // Trajet 1: Paris -> Lyon
    $sql = "INSERT INTO covoiturage (date_depart, heure_depart, date_arrivee, heure_arrivee, lieu_depart, lieu_arrivee, nb_place, prix_personne, est_ecologique, id_conducteur, id_voiture, statut) 
            VALUES (:date, '08:00', :date, '12:30', 'Paris', 'Lyon', 3, 45, 1, :conducteur, :voiture, 'PLANIFIÉ')";
    $stmt = $db->prepare($sql);
    $stmt->execute([':date' => $today, ':voiture' => $id_voiture, ':conducteur' => $id_utilisateur]);
    echo "Trajet Paris -> Lyon créé.\n";

    // Trajet 2: Lyon -> Marseille
    $sql = "INSERT INTO covoiturage (date_depart, heure_depart, date_arrivee, heure_arrivee, lieu_depart, lieu_arrivee, nb_place, prix_personne, est_ecologique, id_conducteur, id_voiture, statut) 
            VALUES (:date, '14:00', :date, '17:45', 'Lyon', 'Marseille', 2, 30, 1, :conducteur, :voiture, 'PLANIFIÉ')";
    $stmt = $db->prepare($sql);
    $stmt->execute([':date' => $today, ':voiture' => $id_voiture, ':conducteur' => $id_utilisateur]);
    echo "Trajet Lyon -> Marseille créé.\n";

    echo "\nSeeding terminé avec succès !";
    echo "\n<a href='../public/index.php'>Retour à l'accueil</a>";

} catch (PDOException $e) {
    echo "Erreur seeding: " . $e->getMessage();
}
echo "</pre>";
