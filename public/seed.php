<?php
require_once __DIR__ . '/../src/Config/Database.php';

$database = new Database();
$db = $database->getConnection();

echo "<pre>";
echo "🌱 Seeding database...\n";

try {
    // 0. Vider et remplir la table MARQUE et autres de test
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("DELETE FROM marque");
    $db->exec("ALTER TABLE marque AUTO_INCREMENT = 1");
    // Optionnel : Nettoyage des voitures/trajets liés si on veut repartir clean
    // $db->exec("TRUNCATE TABLE voiture"); 
    // $db->exec("TRUNCATE TABLE covoiturage");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    $marques = [
        'Tesla', 'Renault', 'Peugeot', 'Citroën', 'Volkswagen', 'BMW', 
        'Mercedes-Benz', 'Audi', 'Toyota', 'Ford', 'Fiat', 'Hyundai', 
        'Kia', 'Nissan', 'Volvo', 'Dacia', 'Opel', 'Skoda', 'Seat', 'Mazda'
    ];

    $sql = "INSERT INTO marque (libelle) VALUES (?)";
    $stmt = $db->prepare($sql);

    foreach ($marques as $marque) {
        $stmt->execute([$marque]);
    }
    echo "✅ " . count($marques) . " marques automobiles ajoutées.\n";

    // 1. Créer une voiture pour l'utilisateur 1 (EcoDriver)
    // On suppose que l'utilisateur 1 existe déjà (créé par install_db ou seed_users si existait)
    // S'il n'existe pas, on le crée.
    $stmt = $db->query("SELECT id_utilisateur FROM utilisateur WHERE id_utilisateur = 1");
    if (!$stmt->fetch()) {
        // Création EcoDriver par défaut
        $mdp = password_hash('test', PASSWORD_BCRYPT);
        $db->exec("INSERT INTO utilisateur (id_utilisateur, email, password, pseudo, id_role, credits) VALUES 
                   (1, 'test@test.com', '$mdp', 'EcoDriver', 2, 50)");
        echo "👤 Utilisateur EcoDriver (ID 1) créé.\n";
    }

    // Récupérer l'ID de Tesla (probablement 1 si auto incrément reset, mais soyons sûrs)
    $stmt = $db->prepare("SELECT id_marque FROM marque WHERE libelle = 'Tesla'");
    $stmt->execute();
    $id_tesla = $stmt->fetchColumn() ?: 1;

    // Check if car exists
    $stmt = $db->prepare("SELECT id_voiture FROM voiture WHERE immatriculation = 'AB-123-CD'");
    $stmt->execute();
    $id_voiture = $stmt->fetchColumn();

    if(!$id_voiture) {
        $db->prepare("INSERT INTO voiture (modele, immatriculation, energie, couleur, nombre_places, id_marque, id_utilisateur) 
                      VALUES ('Model 3', 'AB-123-CD', 'Electrique', 'Blanc', 4, ?, 1)")->execute([$id_tesla]);
        $id_voiture = $db->lastInsertId();
        echo "🚗 Voiture créée (Tesla Model 3).\n";
    } else {
        echo "ℹ️ Voiture Tesla déjà existante.\n";
    }

    // 2. Créer des trajets (Covoiturages)
    $today = date('Y-m-d');
    
    // Trajet 1: Paris -> Lyon
    $sql = "INSERT INTO covoiturage (date_depart, heure_depart, date_arrivee, heure_arrivee, lieu_depart, lieu_arrivee, nb_place, prix_personne, est_ecologique, id_conducteur, id_voiture, statut) 
            VALUES (:date, '08:00', :date, '12:30', 'Paris', 'Lyon', 3, 45.50, 1, 1, :voiture, 'PLANIFIÉ')";
    $stmt = $db->prepare($sql);
    $stmt->execute([':date' => $today, ':voiture' => $id_voiture]);
    echo "📍 Trajet Paris -> Lyon créé.\n";

    // Trajet 2: Lyon -> Marseille
    $sql = "INSERT INTO covoiturage (date_depart, heure_depart, date_arrivee, heure_arrivee, lieu_depart, lieu_arrivee, nb_place, prix_personne, est_ecologique, id_conducteur, id_voiture, statut) 
            VALUES (:date, '14:00', :date, '17:45', 'Lyon', 'Marseille', 2, 30.00, 1, 1, :voiture, 'PLANIFIÉ')";
    $stmt = $db->prepare($sql);
    $stmt->execute([':date' => $today, ':voiture' => $id_voiture]);
    echo "📍 Trajet Lyon -> Marseille créé.\n";

    echo "\n✨ Seeding terminé avec succès !";
    echo "\n<a href='index.php'>Retour à l'accueil</a>";

} catch (PDOException $e) {
    echo "❌ Erreur seeding: " . $e->getMessage();
}
echo "</pre>";
