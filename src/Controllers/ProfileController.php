<?php

class ProfileController {

    // US 8: Afficher le profil, les préférences et les voitures
    public function index() {
        if (!isset($_SESSION['user'])) { header('Location: index.php?page=login'); exit; }
        
        $db = new Database();
        $conn = $db->getConnection();
        $userId = $_SESSION['user']['id'];

        // 1. Récupérer les infos utilisateur à jour
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Récupérer les voitures de l'utilisateur
        $stmt = $conn->prepare("
            SELECT v.*, m.libelle as marque_libelle 
            FROM voiture v 
            JOIN marque m ON v.id_marque = m.id_marque 
            WHERE v.id_utilisateur = ?
        ");
        $stmt->execute([$userId]);
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Récupérer la liste des marques pour le formulaire d'ajout
        $stmt = $conn->query("SELECT * FROM marque ORDER BY libelle");
        $marques = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../Views/profile.php';
    }

    // US 8: Mettre à jour les infos personnelles et préférences
    public function update() {
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
        
        $userId = $_SESSION['user']['id'];
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        
        // Checkbox : si coché "on", sinon false
        $pref_fumeur = isset($_POST['pref_fumeur']) ? 1 : 0;
        $pref_animaux = isset($_POST['pref_animaux']) ? 1 : 0;
        
        // Texte libre pour les préférences de voyage
        $pref_voyage = trim($_POST['pref_voyage'] ?? '');

        // Gestion de l'Upload Photo
        $photoSQL = ""; 
        $params = [$nom, $prenom, $adresse, $telephone, $bio, $pref_fumeur, $pref_animaux, $pref_voyage];

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newFilename = uniqid('user_' . $userId . '_') . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/assets/uploads/';
                
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newFilename)) {
                    $photoSQL = ", photo = ?";
                    $params[] = $newFilename; // Ajouter le nom du fichier aux params
                    $_SESSION['user']['photo'] = $newFilename; // Update session immedialty
                }
            }
        }

        $params[] = $userId; // ID à la fin pour le WHERE

        $db = new \Database(); 
        $conn = $db->getConnection();
        
        $sql = "UPDATE utilisateur SET 
                nom = ?, prenom = ?, adresse = ?, telephone = ?, bio = ?,
                pref_fumeur = ?, pref_animaux = ?, pref_voyage = ?" . $photoSQL . "
                WHERE id_utilisateur = ?";
        
        $stmt = $conn->prepare($sql);
        if ($stmt->execute($params)) {
            // Mettre à jour la session
            $_SESSION['user']['pseudo'] = $_SESSION['user']['pseudo']; // Inchangé
            // Idéalement on recharge tout l'user en session
            header('Location: index.php?page=profile&success=updated');
        } else {
            header('Location: index.php?page=profile&error=update_failed');
        }
    }

    // US 8: Ajouter une voiture
    public function addCar() {
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }

        $immatriculation = trim($_POST['immatriculation'] ?? '');
        $modele = trim($_POST['modele'] ?? '');
        $id_marque = $_POST['id_marque'] ?? null;
        $places = $_POST['places'] ?? 4;
        $energie = $_POST['energie'] ?? 'Essence';
        $couleur = $_POST['couleur'] ?? '';

        if (empty($immatriculation) || empty($modele) || empty($id_marque)) {
            header('Location: index.php?page=profile&error=missing_car_fields');
            exit;
        }

        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO voiture (modele, immatriculation, energie, couleur, nombre_places, id_marque, id_utilisateur) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        try {
            if ($stmt->execute([$modele, $immatriculation, $energie, $couleur, $places, $id_marque, $_SESSION['user']['id']])) {
                header('Location: index.php?page=profile&success=car_added');
            } else {
                header('Location: index.php?page=profile&error=car_add_failed');
            }
        } catch (PDOException $e) {
            // Doublon immatriculation probable
            header('Location: index.php?page=profile&error=duplicate_plate');
        }
    }

    // US 8: Supprimer une voiture
    public function deleteCar() {
        if (!isset($_SESSION['user']) || !isset($_GET['id'])) { header('Location: index.php'); exit; }

        $db = new Database();
        $conn = $db->getConnection();
        
        // Vérifier que la voiture appartient bien à l'utilisateur !
        $stmt = $conn->prepare("DELETE FROM voiture WHERE id_voiture = ? AND id_utilisateur = ?");
        $stmt->execute([$_GET['id'], $_SESSION['user']['id']]);

        header('Location: index.php?page=profile&success=car_deleted');
    }
}
